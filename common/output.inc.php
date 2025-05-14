<?php
require_once __DIR__ . "/const.inc.php";

define("ARCH_FLAG1", "[archived]");
define("ARCH_FLAG2", "[archive]");

/**
 * @param string $format
 * @param array  ...$param
 */
function logMe(string $format, ...$param): void {
    echo sprintf($format, ...$param);
}

function logMeHead(string $format, ...$param): void {
    logMe("\n$format\n", ...$param);
    logMe("%s\n", str_repeat("=", 40));
}

function prepareCsvLine(...$param) {
    return sprintf("%s;%s;%s;%s\n", ...$param);
}

/**
 * @param array    $page
 * @param int|null $idx
 */
function showResults(array $page, ?int $idx = null): void {
    $line = '';

    if (isset($idx)) {
        $line = sprintf("%s;", $idx);
    }
    if (is_array($page)) {
        $line .= sprintf(
            "%s;%s;\"%s\";%s;%s%s",
            isset($page['content']['id']) ? $page['content']['id'] : $page['id'],
            isset($page['content']['space']['key']) ? $page['content']['space']['key'] : $page['space']['key'],
            isset($page['content']['title']) ? $page['content']['title'] : $page['title'],
            isset($page['content']['type']) ? $page['content']['type'] : $page['type'],
            isset($page['_links']['base']) ? $page['_links']['base'] : CONF_BASE_URL,
            isset($page['content']['_links']['webui']) ? $page['content']['_links']['webui'] : (isset($page['url']) ? $page['url'] : $page['_links']['webui'])
        );
        logMe("%s\n", $line);
    } else {
        echo "unsupported " . gettype($page);
        var_dump($page);
    }
}

/**
 * @param array $responseData
 * @param bool  $asCsv
 */
function showTotals(array $responseData, bool $asCsv = false) {
    $start = isset($responseData['start']) ? $responseData['start'] : '-';
    $size  = isset($responseData['size']) ? $responseData['size'] : '-';
    $limit = isset($responseData['limit']) ? $responseData['limit'] : '-';
    $total = isset($responseData['total']) ? $responseData['total'] : (isset($responseData['totalSize']) ? $responseData['totalSize'] : '-');

    if ($asCsv) {
        logMe("%s;%s;%s", $total, $start, $size, $limit);
    } else {
        logMe(
            "\t\t\t\t\ttotal: %s / start: %s / size: %s / limit: %s\n",
            $total,
            $start,
            $size,
            $limit
        );
    }
}

function prepareSpaceArray(array $results, bool $noArchived = true, bool $asCsv = true): array {
    $spaces = [];

    if ($asCsv) {
        $idx = 0;
        foreach ($results as $result) {
            $line = "";
            if (isset($result) && is_array($result)) {
                $addResult = true;
                if ($noArchived) {
                    $descr = $result['description']['plain']['value'];
                    if (false !== stripos($descr, ARCH_FLAG1)) {
                        $addResult = false;
                    } elseif (false !== stripos($descr, ARCH_FLAG2)) {
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
            if (isset($result) && is_array($result)) {
                $spaces[] = $result['key'];
            }
        }
    }
    natcasesort($spaces);

    return $spaces;
}

function prepareMySpaceFile(array $spaces): string {
    $line = "<?php\nfunction _getSpaceListAll(): array {\nreturn [\n";
    foreach ($spaces as $space) {
        $line .= sprintf("\"%s\",\n", $space);
    }
    $line .= "\n];}\n";

    return $line;
}

/**
 * @param array $page
 *
 * @return string
 */
function getSpaceKeyFromResult($page = []): string {
    return isset($page['content']['space']['key']) ? $page['content']['space']['key'] : $page['space']['key'];
}

/**
 * Returns a single result as dummy.
 *
 * @return array[]
 */
function dummyResult(): array {
    return [0 => [0 => 543326561, 1 => null, 2 => "TEST"]];
}

/**
 * @return array
 */
function dummyResultEntry(): array {
    $entry            = [];
    $entry['content'] = [
        'id'     => 'id',
        'title'  => 'title',
        'type'   => 'type',
        '_links' => ['webui' => 'webui'],
        'space'  => ['key' => 'key'],
    ];
    $entry['space']   = ['key' => 'key'];

    return $entry;
}

/**
 * @return array
 */
function dummyResults(): array {
    $result              = [];
    $result['results']   = [0 => dummyResultEntry()];
    $result['start']     = 0;
    $result['size']      = 1;
    $result['limit']     = 22;
    $result['totalSize'] = 1;

    return $result;
}
