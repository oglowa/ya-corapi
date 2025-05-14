<?php

define("SCRIPT_NAME", basename(__FILE__, ".php"));
require_once __DIR__ . "/common/functions.inc.php";

$searchUrl =
    "https://nma-s-confluence-test.arvato-systems.de/rest/api/search?cql=siteSearch~%22macroName%3Aadd-page%22+AND+type=%22page%22&expand=content.space,content.history,content.version";

$curlSession = prepareCurl();
$result      = execCurl($curlSession, $searchUrl);

var_dump($result);