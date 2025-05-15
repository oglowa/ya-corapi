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

/**
 * @param $text
 */
function logFast($text): void {
    logMe("%s\n", $text);
}

function prepareCsvLine(...$param) {
    return sprintf("%s;%s;%s;%s\n", ...$param);
}

/**
 * @param array    $response
 * @param int|null $idx
 */
function showResults(array $response, ?int $idx = null): void {
    $line = '';

    if (isset($idx)) {
        $line = sprintf("%s;", $idx);
    }
    if (is_array($response)) {
        $line .= sprintf(
            "%s;%s;\"%s\";%s;%s%s",
            isset($response['content']['id']) ? $response['content']['id'] : $response['id'],
            isset($response['content']['space']['key']) ? $response['content']['space']['key'] : $response['space']['key'],
            isset($response['content']['title']) ? $response['content']['title'] : $response['title'],
            isset($response['content']['type']) ? $response['content']['type'] : $response['type'],
            isset($response['_links']['base']) ? $response['_links']['base'] : CONF_BASE_URL,
            isset($response['content']['_links']['webui']) ? $response['content']['_links']['webui'] :
                (isset($response['url']) ? $response['url'] : $response['_links']['webui'])
        );
        logMe("%s\n", $line);
    } else {
        logMe("unsupported %s", gettype($response));
        var_dump($response);
    }
}

/**
 * @param array $response
 * @param bool  $asCsv
 */
function showTotals(array $response, bool $asCsv = false) {
    $start = isset($response['start']) ? $response['start'] : '-';
    $size  = isset($response['size']) ? $response['size'] : '-';
    $limit = isset($response['limit']) ? $response['limit'] : '-';
    $total = isset($response['total']) ? $response['total'] : (isset($response['totalSize']) ? $response['totalSize'] : '-');

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

function prepareSpaceArray(?array $results, bool $noArchived = true, bool $asCsv = true): array {
    $spaces = [];

    if (is_array($results)) {
        if ($asCsv) {
            $idx = 0;
            foreach ($results as $result) {
                $line = "";
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
    $response              = [];
    $response['results']   = [0 => dummyResultEntry()];
    $response['start']     = 0;
    $response['size']      = 1;
    $response['limit']     = 22;
    $response['totalSize'] = 1;

    return $response;
}
