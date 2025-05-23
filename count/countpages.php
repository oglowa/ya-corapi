<?php
define('SCRIPT_NAME', basename(__FILE__, '.php'));
require_once __DIR__ . '/../common/func-common.inc.php';

function countPagesInSpace($space, $pageType): int {
    $curlSession = prepareCurl();
    $searchUrl   = CONF_SEARCH_URL . "?cql=type+in+($pageType)+AND+space=$space";
    $response    = execCurl($curlSession, $searchUrl);

    $countPages = 0;
    if (checkData($response)) {
        $countPages = $response['totalSize'];
    }

    return $countPages;
}

function loopPageTypes(string $space): void {
    foreach (PAGE_TYPES as $pageType) {
        $countPages = countPagesInSpace($space, $pageType);
        logMe("Space '%s' has %s %s\n", $space, $countPages, $pageType);
        $line = prepareCsvLine("%s;%s;%s", $space, $pageType, $countPages);
        storeCsv(TARGET_DIR, TARGET_FILENAME, $line);
    }
}

function loopSpaces(array $spaces): void {
    foreach ($spaces as $space) {
        logMe("Checking space '%s'\n", $space);
        loopPageTypes($space);
    }
}

function main() {
    prepareFilesystem();
    $head = prepareCsvLine("%s;%s;%s;%s", 'space', 'pagetype', 'count', "empty");
    storeCsv(TARGET_DIR, TARGET_FILENAME, $head);

    $spaces = getSpaceList(SPACE_ALL);
    loopSpaces($spaces);
}

main();