<?php
define("SCRIPT_NAME", basename(__FILE__, ".php"));
require_once __DIR__ . "/../common/functions.inc.php";

function spacesGlobal(bool $asCsv = true) {
    logMeHead("++ %s ++", "Site Spaces");

    $curlSession = prepareCurl();
    $searchUrl   = prepareSpaceListUrl('global', 100);
    $result      = execCurl($curlSession, $searchUrl);

    $spaces = prepareSpaceArray($result['results'], $asCsv);
    prepareFilesystem();
    storeText(TARGET_DIR, TARGET_FILENAME . ".inc.php", prepareMySpaceFile($spaces));
}

function spacesPersonal(bool $asCsv = true) {
    logMeHead("++ %s ++", "Personal Spaces");

    $curlSession = prepareCurl();
    $searchUrl   = prepareSpaceListUrl('personal', 150);
    $result      = execCurl($curlSession, $searchUrl);

    prepareSpaceArray($result['results'], $asCsv);
}

function main() {
    spacesGlobal();
    //spacesPersonal();
}

main();