<?php
define('SCRIPT_NAME', basename(__FILE__, '.php'));
require_once __DIR__ . '/../common/func-common.inc.php';

$spaceKey   = 'NMAS';
$searchTerm = 'title=REST-API%2001';
$pageId     = 591855803;

function resultPosUpdate($startNow, $sizeNow, $totalsNow): void {
    global $currentPos, $nextPos, $totalSize;

    logMe("CURRENT: currentPos: %s / nextPos: %s / totalSize: %s\n", $currentPos, $nextPos, $totalSize);
    $totalSize  = $totalsNow;
    $currentPos = $startNow;
    $nextPos    = $startNow + $sizeNow;
    logMe("NEW    : currentPos: %s / nextPos: %s / totalSize: %s\n", $currentPos, $nextPos, $totalSize);
}

function outputResult($response) {
    $hasResults = checkData($response);
    if ($hasResults) {
        showResults($response);
    } else {
        logFast(MSG_FOUND_NO_RESULTS);
    }
}

function outputResults($response) {
    $hasResults = checkData($response);
    if ($hasResults) {
        $idx = 0;
        foreach ($response['results'] as $singleResult) {
            //storeResults(TARGET_DIR, $singleResult);
            showResults($singleResult, $idx++);
        }
    } else {
        logFast(MSG_FOUND_NO_RESULTS);
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
    $searchUrl   = _prepareSearchUrl($searchTerm, $spaceKey, $searchFromPos, $searchLimit);
    $response    = execCurl($curlSession, $searchUrl);

    $hasResults = checkData($response);
    if ($hasResults) {
        $idx = 0;
        foreach ($response['results'] as $singleResult) {
            showResults($singleResult, $idx++);
        }
        resultPosUpdate($response['start'], $response['size'], $response['totalSize']);
    } else {
        logFast(MSG_FOUND_NO_RESULTS);
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
            logFast("+++ fallback exit after 10 iterations +++");
            exit(10);
        }
    } while ($totalSize > $currentPos);
}

function mainSearchPageId(): void {
    global $pageId;

    $curlSession = prepareCurl();
    $searchUrl   = prepareApiByPageIdUrl($pageId);
    $response    = execCurl($curlSession, $searchUrl);

    outputResult($response);
}

function mainSearchBrowse(): void {
    global $searchTerm;

    $curlSession = prepareCurl();
    $searchUrl   = prepareBrowseUrl($searchTerm);
    $response    = execCurl($curlSession, $searchUrl);

    outputResults($response);
}

function mainSearchBrowseWithSpaceKey(): void {
    global $searchTerm, $spaceKey;

    $curlSession = prepareCurl();
    $searchUrl   = prepareBrowseUrl($searchTerm, $spaceKey);
    $response    = execCurl($curlSession, $searchUrl);

    outputResults($response);
}

function mainSearchScan(): void {
    global $searchTerm, $spaceKey;

    $curlSession = prepareCurl();
    $searchUrl   = prepareScanUrl($searchTerm, $spaceKey);
    $response    = execCurl($curlSession, $searchUrl);

    outputResults($response);
}

function main() {
    logFast("\n\n+++ mainSearchWithLoop +++\n");
    mainSearchWithLoop();

    logFast("\n\n+++ mainSearchBrowse +++\n");
    mainSearchBrowse();

    logFast("\n\n+++ mainSearchPageId +++\n");
    mainSearchPageId();

    logFast("\n\n+++ mainSearchBrowse +++\n");
    mainSearchBrowse();

    logFast("\n\n+++ mainSearchBrowseWithSpaceKey +++\n");
    mainSearchBrowseWithSpaceKey();

    logFast("\n\n+++ mainSearchScan +++\n");
    mainSearchScan();
}

main();