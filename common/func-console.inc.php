<?php
require_once __DIR__ . '/const.inc.php';

define('ARCH_FLAG1', '[archived]');
define('ARCH_FLAG2', '[archive]');

/**
 * @param string $format
 * @param array  ...$param
 */
function logMe(string $format, ...$param): void {
    echo sprintf($format, ...$param);
}

function logMeHead(string $format, ...$param): void {
    logMe("\n$format\n", ...$param);
    logMe("%s\n", str_repeat('=', 40));
}

/**
 * @param $text
 */
function logFast($text): void {
    logMe("%s\n", $text);
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
