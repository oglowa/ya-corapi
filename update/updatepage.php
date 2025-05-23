<?php
define('SCRIPT_NAME', basename(__FILE__, '.php'));
require_once __DIR__ . '/../common/func-common.inc.php';

function loopThruUpdates($pageId): void {
    global $totalSize;

    $curlSession = prepareCurl();
    $loadUrl     = prepareLoadUrl($pageId);
    $response    = execCurl($curlSession, $loadUrl);

    if (key_exists('body', $response)) {
        $pageIdLoaded = $response['id'];
        if ($pageId == $pageIdLoaded) {
            $pageBodyLoaded    = $response['body']['storage']['value'];
            $pageVersionLoaded = $response['version']['number'];
            $pageTitleLoaded   = $response['title'];

            storeData(TARGET_ORGDIR, $pageIdLoaded, $pageBodyLoaded);
            updatePage($pageIdLoaded, $pageBodyLoaded, $pageVersionLoaded, $pageTitleLoaded);
            $totalSize = 1;
        } else {
            logMe("+++ Not matching page '%s' and '%s' +++\n", $pageId, $pageIdLoaded);
        }
    } else {
        logMe("Page '%s' not loaded!\n", $pageId);
    }
}

function mainUpdate(): void {
    global $resultsFile;

    $pageList = readResultFile($resultsFile);
    //$pageList = dummyResult();

    prepareFilesystem();
    $fallbackIdx = 0;
    foreach ($pageList as $page) {
        loopThruUpdates($page[0]);
        $fallbackIdx++;
        logMe("%s. total:%s / pageid:%s / title:%s\n", $fallbackIdx, $fallbackIdx, $page[0], $page[2]);
        if ($fallbackIdx >= 1000) {
            logMe("+++ fallback exit after %s iterations +++", $fallbackIdx);
            exit(10);
        }
    }
}

mainUpdate();
