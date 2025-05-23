<?php
define('SCRIPT_NAME', basename(__FILE__, '.php'));
require_once __DIR__ . '/../common/func-common.inc.php';

function spacesGlobal(bool $asCsv = true) {
    logMeHead("++ %s ++", "Site Spaces");

    $curlSession = prepareCurl();
    $searchUrl   = prepareSpaceListUrl('global', 100);
    $response    = execCurl($curlSession, $searchUrl);

    if (checkData($response)) {
        $spaces = prepareSpaceArray($response['results'], $asCsv);
        prepareFilesystem();
        storeText(TARGET_DIR, TARGET_FILENAME . '.inc.php', prepareMySpaceFile($spaces));
    }
}

function spacesPersonal(bool $asCsv = true) {
    logMeHead("++ %s ++", "Personal Spaces");

    $curlSession = prepareCurl();
    $searchUrl   = prepareSpaceListUrl('personal', 150);
    $response    = execCurl($curlSession, $searchUrl);

    if (checkData($response)) {
        prepareSpaceArray($response['results'], $asCsv);
    }
}

function main() {
    spacesGlobal();
    spacesPersonal();
}

main();