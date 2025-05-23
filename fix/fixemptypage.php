<?php
define('SCRIPT_NAME', basename(__FILE__, '.php'));
require_once __DIR__ . '/../common/func-common.inc.php';

function scanPagesInSpace($space): void {
    $curlSession = prepareCurl();
    $start       = 0;
    $limit       = 25;

    $idx   = 0;
    $bLoop = true;
    while ($bLoop) {
        $searchUrl = _prepareSearchUrl(
            "type:page AND -macroName:projectdoc-properties-marker",
            $space,
            $start,
            $limit,
            'page',
            true
        );

        $response = execCurl($curlSession, $searchUrl);
        if (checkData($response)) {
            foreach ($response['results'] as $result) {
                if (key_exists('content', $result)) {
                    $result = $result['content'];
                }
                $bodySize = strlen($result['body']['storage']['value']);
                if ($bodySize <= 10) {
                    $line = prepareCsvLine(
                        "%s;%s;%s;\"%s\";%s;%s",
                        $idx,
                        $result['id'],
                        $result['type'],
                        $result['title'],
                        $bodySize,
                        WEB_SHOW_PAGEID . $result['id']
                    );
                    logFast($line);
                    storeCsv(TARGET_DIR, TARGET_FILENAME, $line);
                    $idx++;
                }
            }
        } else {
            logFast("\nExiting no results found.\n");
            $bLoop = false;
            break;
        }
        if ($idx > PAGE_MAX_RESULTS) {
            logMe("\nExiting after at least %s results.\n", PAGE_MAX_RESULTS);
            $bLoop = false;
            break;
        }
        $start += $limit;
    }
}

function main() {
    prepareFilesystem();
    scanPagesInSpace('NMVSSUP');
}

main();