<?php
require_once "../common/auth.inc.php";

define("SCRIPT_NAME", basename(__FILE__, ".php"));
require_once "../common/functions.inc.php";

/**
 * @param $pageId
 *
 * @return void
 */
function loopThruUpdates($pageId): void {
    global $totalSize;

    $curlSession = prepareCurl();
    $loadUrl     = prepareLoadUrl($pageId);
    $result      = execCurl($curlSession, $loadUrl);

    if (key_exists('body', $result)) {
        $pageIdLoaded = $result['id'];
        if ($pageId == $pageIdLoaded) {
            $pageBodyLoaded    = $result['body']['storage']['value'];
            $pageVersionLoaded = $result['version']['number'];
            $pageTitleLoaded   = $result['title'];

            storeData(TARGET_ORGDIR, $pageIdLoaded, $pageBodyLoaded);
            updatePage($pageIdLoaded, $pageBodyLoaded, $pageVersionLoaded, $pageTitleLoaded);
            $totalSize = 1;
        } else {
            echo sprintf("+++ Not matching page '%s' and '%s' +++\n", $pageId, $pageIdLoaded);
        }
    } else {
        echo sprintf("Page '%s' not loaded!\n", $pageId);
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
        echo sprintf("%s. total:%s / pageid:%s / title:%s\n", $fallbackIdx, $fallbackIdx, $page[0], $page[2]);
        if ($fallbackIdx >= 1000) {
            echo sprintf("+++ fallback exit after %s iterations +++", $fallbackIdx);
            exit(10);
        }
    }
}

mainUpdate();
