<?php
define("SCRIPT_NAME", basename(__FILE__, ".php"));
require_once __DIR__ . "/../common/functions.inc.php";

$spaceKey   = 'NMAS';
$searchTerm = 'title=REST-API%2001';
$pageId     = 591855803;

function outputResult($result) {
    $hasResults = checkData($result);
    if ($hasResults) {
        showResults($result);
    } else {
        echo FOUND_NO_RESULTS;
    }
}

function outputResults($result) {
    $hasResults = checkData($result);
    if ($hasResults) {
        $idx = 0;
        foreach ($result['results'] as $singleResult) {
            //storeResults(TARGET_DIR, $singleResult);
            showResults($singleResult, $idx++);
        }
    } else {
        echo FOUND_NO_RESULTS;
    }
}

/**
 * @param $spaceKey
 * @param $searchTerm
 * @param $searchFromPos
 * @param $searchLimit
 *
 * @return void
 */
function loopThruSearchResults($spaceKey, $searchTerm, $searchFromPos, $searchLimit = SEARCH_LIMIT): void {
    $curlSession = prepareCurl();
    $searchUrl   = prepareSearchUrl2($spaceKey, $searchTerm, $searchFromPos, $searchLimit);
    $result      = execCurl($curlSession, $searchUrl);

    $hasResults = checkData($result);
    if ($hasResults) {
        $idx = 0;
        foreach ($result['results'] as $singleResult) {
            showResults($singleResult, $idx++);
        }
        resultPosUpdate($result['start'], $result['size'], $result['totalSize']);
    } else {
        echo FOUND_NO_RESULTS;
    }
}

function mainSearchWithLoop(): void {
    global $spaceKey;
    global $totalSize, $currentPos, $nextPos;
    global $searchTerm;

    $fallbackIdx = 0;
    do {
        loopThruSearchResults($spaceKey, $searchTerm, $nextPos, SEARCH_LIMIT);
        $fallbackIdx++;
        if ($fallbackIdx >= 10) {
            echo "+++ fallback exit after 10 iterations +++";
            exit(10);
        }
    } while ($totalSize > $currentPos);
}

function mainSearchPageId(): void {
    global $pageId;

    $curlSession = prepareCurl();
    $searchUrl   = prepareApiByPageIdUrl($pageId);
    $result      = execCurl($curlSession, $searchUrl);

    outputResult($result);
}

function mainSearchBrowse(): void {
    global $searchTerm;

    $curlSession = prepareCurl();
    $searchUrl   = prepareBrowseUrl($searchTerm);
    $result      = execCurl($curlSession, $searchUrl);

    outputResults($result);
}

function mainSearchBrowseWithSpaceKey(): void {
    global $searchTerm, $spaceKey;

    $curlSession = prepareCurl();
    $searchUrl   = prepareBrowseUrl($searchTerm, $spaceKey);
    $result      = execCurl($curlSession, $searchUrl);

    outputResults($result);
}

function mainSearchScan(): void {
    global $searchTerm, $spaceKey;

    $curlSession = prepareCurl();
    $searchUrl   = prepareScanUrl($searchTerm, $spaceKey);
    $result      = execCurl($curlSession, $searchUrl);

    outputResults($result);
}

function main() {
    echo "\n\n+++ mainSearchWithLoop +++\n";
    mainSearchWithLoop();

    echo "\n\n+++ mainSearchBrowse +++\n";
    mainSearchBrowse();

    echo "\n\n+++ mainSearchPageId +++\n";
    mainSearchPageId();

    echo "\n\n+++ mainSearchBrowse +++\n";
    mainSearchBrowse();

    echo "\n\n+++ mainSearchBrowseWithSpaceKey +++\n";
    mainSearchBrowseWithSpaceKey();

    echo "\n\n+++ mainSearchScan +++\n";
    mainSearchScan();
}

main();