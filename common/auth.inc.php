<?php

define("C_MYAUTH", MY_DIR . DIRECTORY_SEPARATOR . "myauth.inc.php");
if (file_exists(C_MYAUTH)) {
    include_once C_MYAUTH;
} else {
    echo sprintf("'%s' not loaded!", C_MYAUTH);
}
if (!defined("USE_PROD")) {
    define("USE_PROD", false);
}

if (USE_PROD) {
    define("AUTH_TOKEN_NAME", "CONF_PAT_PROD");
    $resultsFile = __DIR__ . "/../risks-prod.csv";
    echo "\n\n+++ RUNNING ON PRODUCTION IS OK 4 U? Waiting 5sec +++\n\n";
    sleep(5); // NOSONAR php:S2964
} else {
    define("AUTH_TOKEN_NAME", "CONF_PAT_TEST");
    $resultsFile = __DIR__ . "/../risks-test.csv";
}
