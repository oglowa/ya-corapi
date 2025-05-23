<?php
define('SCRIPT_NAME', basename(__FILE__, '.php'));
require_once __DIR__ . '/../common/func-common.inc.php';

$pageId   = 532951146;
$spaceKey = "NMAS";
$where    = "Name=TOOL";

function readDocument() {
    global $spaceKey, $where;

    $curlSession = prepareCurl();

    $searchUrl = preparePdtDocumentReadUrl(PDT_PROP_ALL_DEFAULT, $spaceKey, $where);
    $response  = execCurl($curlSession, $searchUrl);
    var_dump($response);
}

function readDefaultProperties() {
    global $pageId;

    prepareFilesystem();
    storeCsv(TARGET_DIR, TARGET_FILENAME, CSV_LINE_PDT_PROPERTY_HEADER);
    $curlSession = prepareCurl();

    foreach (PDT_PROP_ALL_DEFAULT as $property) {
        $searchUrl = preparePdtPropertyReadUrl($pageId, $property);
        $response  = execCurl($curlSession, $searchUrl);

        if (checkDataPdtProperty($response)) {
            storeCsv(TARGET_DIR, TARGET_FILENAME, prepareCsvLinePdtProperty($response, $property));
        } else {
            storeCsv(TARGET_DIR, TARGET_FILENAME, prepareCsvLinePdtProperty([], $property));
        }
    }
}

function main() {
    readDocument();
//    readDefaultProperties();
}

main();