<?php
$USE_PROD = false;
if (isset($argv[1]) && $argv[1] == "prod") {
    $USE_PROD = true;
}

if ($USE_PROD) {
    echo "\n\n+++ RUNNING ON PRODUCTION IS OK 4 U? Waiting 5sec +++\n\n";
    sleep(57);
    $confUrl     = "https://nma-s-confluence.arvato-systems.de";
    $tokenName   = "CONF_PAT_PROD";
    $resultsFile = dirname(__FILE__) . "/../risks-prod.csv";
} else {
    $confUrl     = "https://nma-s-confluence-test.arvato-systems.de";
    $tokenName   = "CONF_PAT_TEST";
    $resultsFile = dirname(__FILE__) . "/../risks-test.csv";
}
