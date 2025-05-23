<?php

$stats = [];

function outputStats() {
    global $stats;

    prepareFilesystem();
    storeCsv(TARGET_DIR, TARGET_FILENAME, prepareCsvLine("%s;%s;%s;%s", "addon", 'macroname', 'spacekey', "count"));

    foreach ($stats as $addonName => $addonValues) {
        foreach ($addonValues as $macroName => $statValues) {
            foreach ($statValues as $space => $occurence) {
                storeCsv(TARGET_DIR, TARGET_FILENAME, prepareCsvLine("%s;%s;%s;%s", $addonName, $macroName, $space, $occurence));
            }
        }
    }
}

function addToStats($addOn, $macroName, $space, $totals): void {
    global $stats;

    if (!empty($addOn)) {
        if (!key_exists($addOn, $stats)) {
            $stats[$addOn] = [];
        }

        if (!empty($macroName)) {
            if (!key_exists($macroName, $stats[$addOn])) {
                $stats[$addOn][$macroName] = [];
            }

            if (!empty($space)) {
                if (!key_exists($space, $stats[$addOn][$macroName])) {
                    $stats[$addOn][$macroName][$space] = 0;
                }
                $stats[$addOn][$macroName][$space] = $totals;
            }
        }
    }
}

function analyzeResponse($addOn, $macroName, $response) {
    $hasResults = checkData($response, true);
    if ($hasResults) {
        foreach ($response['results'] as $singleResult) {
            $totals = isset($response['totalSize']) ? $response['totalSize'] : 0;
            $space  = getSpaceKeyFromResult($singleResult);
            addToStats($addOn, $macroName, $space, $totals);
        }
        showTotals($response);
    } else {
        logFast(MSG_FOUND_NO_RESULTS);
    }
}
