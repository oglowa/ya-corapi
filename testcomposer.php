<?php
// Autoload files using the Composer autoloader.
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/common/functions.inc.php';

$pageId = 591855803;

$headers = [
    'Accept'        => 'application/json',
    'Authorization' => "Bearer " . getTokenValue(),
    CURLOPT_RETURNTRANSFER,
    true,
];

Unirest\Request::verifyPeer(false);

$response = Unirest\Request::get(
    CONF_BASE_URL . "/rest/api/content/$pageId/",
    $headers
);
showResults(json_decode($response->raw_body));

