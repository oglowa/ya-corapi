<?php
define("SCRIPT_NAME", basename(__FILE__, ".php"));
require_once __DIR__ . "/../common/functions.inc.php";

function loopSpaces($addOn, $macroName, $searchTerm, $spaces = []) {
    $curlSession = prepareCurl();
    foreach ($spaces as $space) {
        logMe("        Checking Space: '%s' - START ++\n", $space);
        $searchUrl = prepareSearchUrl2($space, $searchTerm, 0, 1);
        $result    = execCurl($curlSession, $searchUrl);
        analyzeResults($addOn, $macroName, $result);
        logMe("        Checking Space: '%s' - END  ++\n", $space);
    }
}

function loopMacros($addOn, $macroNames, $spaces = []) {
    foreach ($macroNames as $macroName) {
        logMe("    Checking Macro: '%s' - START +++\n", $macroName);
        $searchTerm = "macroName:$macroName";
        if (isset($spaces) && sizeof($spaces) > 0) {
            loopSpaces($addOn, $macroName, $searchTerm, $spaces);
        } else {
            logMe("%s\n", "No Space defined");
        }
        logMe("    Checking Macro: '%s' - END   +++\n", $macroName);
    }
}

function loopAddons($addOns, $spaces = []) {
    foreach ($addOns as $addOn => $macroNames) {
        logMe("Checking Addon: '%s' - START +++\n", $addOn);
        loopMacros($addOn, $macroNames, $spaces);
        logMe("Checking Addon: '%s' - END   +++\n\n", $addOn);
    }
}

function main() {
    $spaces = getSpaceList(SPACE_ALL);
    $addOns = getAddonsMacrosList(MACRO_BLOCKER);
    loopAddons($addOns, $spaces);
    outputStats();
}

main();