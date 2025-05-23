<?php
/* --------------------------------------------------------------------------------------------- */
/*
 * REST API Functions
 */

function prepareSpacePagesUrl($space, $pageType = 'page', $start = PAGE_START, $limit = PAGE_LIMIT): string {
    $prepareUrl = sprintf("%s/%s/content/%s?start=%s&limit=%s&%s", CONF_SPACE_URL, $space, $pageType, $start, $limit, REQP_FULL);

    return $prepareUrl;
}

function prepareSpaceListUrl($spaceType = 'global', $limit = PAGE_LIMIT): string {
    $prepareUrl = sprintf("%s?%s&type=%s&limit=%s", CONF_SPACE_URL, REQ_SPACE_LIST, $spaceType, $limit);

    return $prepareUrl;
}

/* --------------------------------------------------------------------------------------------- */
/*
 * Other Functions
 */

function prepareSpaceArray(?array $results, bool $noArchived = true, bool $asCsv = true): array {
    $spaces = [];

    if (is_array($results)) {
        if ($asCsv) {
            $idx = 0;
            foreach ($results as $result) {
                $line = '';
                if (is_array($result)) {
                    $addResult = true;
                    if ($noArchived) {
                        $descr = $result['description']['plain']['value'];
                        if (false !== stripos($descr, ARCH_FLAG1) || (false !== stripos($descr, ARCH_FLAG2))) {
                            $addResult = false;
                        }
                    }
                    if ($addResult) {
                        $spaces[] = $result['key'];

                        $line .= sprintf(
                            "%s;%s;%s;%s",
                            $idx++,
                            $result['key'],
                            $result['type'],
                            'status'
                        );
                        $line .= sprintf(
                            ";\"%s\";\"%s\"",
                            $result['name'],
                            htmlentities(
                                implode(
                                    explode(
                                        PHP_EOL,
                                        $result['description']['plain']['value']
                                    )
                                )
                            )
                        );
                    } else {
                        logMe("  ++ Space '%s' already archived", $result['key']);
                    }
                }
                logMe("%s\n", $line);
            }
        } else {
            foreach ($results as $result) {
                if (is_array($result)) {
                    $spaces[] = $result['key'];
                }
            }
        }
        natcasesort($spaces);
    }

    return $spaces;
}

function prepareMySpaceFile(array $spaces): string {
    $line = "<?php\nfunction _getSpaceListAll(): array {\nreturn [\n";
    foreach ($spaces as $space) {
        $line .= sprintf("'%s',\n", $space);
    }
    $line .= "\n];}\n";

    return $line;
}

function getSpaceKeyFromResult($page = []): string {
    return isset($page['content']['space']['key']) ? $page['content']['space']['key'] : $page['space']['key'];
}
