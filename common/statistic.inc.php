<?php

$stats = [];

function outputStats() {
    global $stats;

    prepareFilesystem();
    storeCsv(TARGET_DIR, TARGET_FILENAME, prepareCsvLine("addon", "macroname", "spacekey", "count"));

    foreach ($stats as $addonName => $addonValues) {
        foreach ($addonValues as $macroName => $statValues) {
            foreach ($statValues as $space => $occurence) {
                storeCsv(TARGET_DIR, TARGET_FILENAME, prepareCsvLine($addonName, $macroName, $space, $occurence));
            }
        }
    }
}

function addToStats($addOn, $macroName, $space, $totals): void {
    global $stats;

    if (isset($addOn) && strlen($addOn) > 0) {
        if (!key_exists($addOn, $stats)) {
            $stats[$addOn] = [];
        }

        if (isset($macroName) && strlen($macroName) > 0) {
            if (!key_exists($macroName, $stats[$addOn])) {
                $stats[$addOn][$macroName] = [];
            }

            if (isset($space) && strlen($space) > 0) {
                if (!key_exists($space, $stats[$addOn][$macroName])) {
                    $stats[$addOn][$macroName][$space] = 0;
                }
                $stats[$addOn][$macroName][$space] = $totals;
            }
        }
    }
}

function analyzeResults($addOn, $macroName, $result) {
    $hasResults = checkData($result, true);
    if ($hasResults) {
        foreach ($result['results'] as $singleResult) {
            $totals = isset($result['totalSize']) ? $result['totalSize'] : 0;
            $space  = getSpaceKeyFromResult($singleResult);
            addToStats($addOn, $macroName, $space, $totals);
        }
        showTotals($result);
    } else {
        echo FOUND_NO_RESULTS;
    }
}
