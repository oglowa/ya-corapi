<?php
$scriptName = basename(__FILE__, ".php");
require_once "../common/functions.auth.php";
require_once "../common/functions.php";


$spaceKey        = 'NMAS';
$searchTerm      = 'title=REST-API%2001';
$pageId          = 591855803;


function outputResult($result) {
    $hasResults = checkData($result);
    if ($hasResults) {
            showResults($result);
    } else {
        echo "found no results!\n";
    }
}

function outputResults($result) {
    $hasResults = checkData($result);
    if ($hasResults) {
        $idx=1;
        foreach ($result['results'] as $singleResult) {
            //storeResults($targetDir, $singleResult);
            showResults($singleResult, $idx++);
        }
    } else {
        echo "found no results!\n";
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
function loopThruSearchResults($spaceKey, $searchTerm, $searchFromPos, $searchLimit): void {
    global $tokenName, $curlSession;
    global $targetDir;
    global $totalSize, $currentPos, $nextPos, $searchLimit;

    $curlSession = prepareCurl($tokenName);
    $searchUrl   = prepareSearchUrl($spaceKey, $searchTerm, $searchFromPos, $searchLimit);
    $result      = execCurl($curlSession, $searchUrl);

    $hasResults = checkData($result);
    if ($hasResults) {
        $idx=1;
        foreach ($result['results'] as $singleResult) {
            //storeResults($targetDir, $singleResult);
            showResults($singleResult, $idx++);
        }
        resultPosUpdate($result['start'], $result['size'], $result['totalSize']);
    } else {
        echo "found no results!\n";
    }
}

function mainSearchWithLoop(): void {
    global $targetDir, $spaceKey;
    global $totalSize, $currentPos, $nextPos, $searchLimit;
    global $searchTerm;

    //prepareFilesystem();
    $fallbackIdx = 0;
    do {
        loopThruSearchResults($spaceKey, $searchTerm, $nextPos, $searchLimit);
        $fallbackIdx++;
        if ($fallbackIdx >= 10) {
            echo "+++ fallback exit after 10 iterations +++";
            exit(1);
        }
    } while ($totalSize > $currentPos);
}

function mainSearchPageId(): void {
    global $tokenName, $curlSession;
    global $pageId;

    $curlSession = prepareCurl($tokenName);
    $searchUrl   = prepareApiByPageIdUrl($pageId);
    $result      = execCurl($curlSession, $searchUrl);

    outputResult($result);
}

function mainSearchBrowse(): void {
    global $tokenName, $curlSession;
    global $searchTerm;

    $curlSession = prepareCurl($tokenName);
    $searchUrl   = prepareBrowseUrl($searchTerm);
    $result      = execCurl($curlSession, $searchUrl);

    outputResults($result);
}

function mainSearchBrowseWithSpaceKey(): void {
    global $tokenName, $curlSession;
    global $searchTerm, $spaceKey;

    $curlSession = prepareCurl($tokenName);
    $searchUrl   = prepareBrowseUrl($searchTerm, $spaceKey);
    $result      = execCurl($curlSession, $searchUrl);

    outputResults($result);
}

function mainSearchScan(): void {
    global $tokenName, $curlSession;
    global $searchTerm, $spaceKey;

    $curlSession = prepareCurl($tokenName);
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