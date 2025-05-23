<?php

/**
 * Returns a single result as dummy.
 *
 * @return array[]
 */
function dummyResult(): array {
    return [0 => [0 => 543326561, 1 => null, 2 => 'TEST']];
}

function dummyBody(): array {
    return ['storage' => ['value' => 'dummy-body']];
}

/**
 * @param bool $withBody
 * @param bool $withContent
 *
 * @return array
 */
function dummyResultEntry(bool $withBody = false, bool $withContent = false): array {
    if ($withContent) {
        $entry            = [];
        $entry['content'] = [
            'id'     => 'dummy-id',
            'title'  => 'dummy-title',
            'type'   => 'dummy-type',
            '_links' => ['webui' => 'dummy-webui'],
            'space'  => ['key' => 'dummy-key'],
        ];
        if ($withBody) {
            $entry['content']['body'] = dummyBody();
        }
    } else {
        $entry = [
            'id'     => 'dummy-id',
            'title'  => 'dummy-title',
            'type'   => 'dummy-type',
            '_links' => ['webui' => 'dummy-webui'],
            'space'  => ['key' => 'dummy-key'],
        ];
        if ($withBody) {
            $entry['body'] = dummyBody();
        }
    }

    //$entry['space'] = ['key' => 'key'];

    return $entry;
}

/**
 * @param bool $withBody
 *
 * @return array
 */
function dummyResponse(bool $withBody = false): array {
    $response              = [];
    $response['results']   = [0 => dummyResultEntry($withBody)];
    $response['start']     = 0;
    $response['size']      = 1;
    $response['limit']     = 22;
    $response['totalSize'] = 1;

    return $response;
}
