<?php

define("MY_DIR", getenv("HOME") . DIRECTORY_SEPARATOR . ".restapi");

require_once __DIR__ . "/auth.inc.php";

CONST REQP_LIGHT             = 'expand=space,history,version';
CONST REQP_FULL              = REQP_LIGHT . ',body.storage';
CONST REQP_PERM              = 'expand=read.restrictions.user,read.restrictions.group,update.restrictions.user,update.restrictions.group';
CONST REQP_SEARCH_LIGHT      = 'expand=content.space,content.history,content.version';
CONST REQP_SEARCH_FULL       = REQP_SEARCH_LIGHT . ',content.body.storage';
CONST RESP_CSV_SPACE_RESULTS = '.results[]| .key + ";" + .type + ";" + "status" + ";" + "\"" + .name + "\"" + ";" +  "\"" + .description.plain.value + "\""';
CONST MSG_FOUND_NO_RESULTS   = "WARN  Found no results!\n";
CONST MSG_NOT_IMPLEMENTED    = "FATAL not implemented so far!\n";
CONST MSG_CURL_ERROR         = "ERROR Status %s - '%s' - '%s'\n";

CONST PAGE_TYPES     = ['page', 'attachment', 'blogpost'];
CONST PAGE_TYPES_ALL = ['page', 'attachment', 'blogpost', 'comment'];

$totalSize  = 0;
$currentPos = 0;
$nextPos    = 0;

define("PROJECT_ROOT", realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'));
define("INPUT_ROOTDIR", sprintf("%s%sinput", PROJECT_ROOT, DIRECTORY_SEPARATOR));
define("INPUT_DIR", sprintf("%s%s%s", INPUT_ROOTDIR, DIRECTORY_SEPARATOR, SCRIPT_NAME));

define("TARGET_ROOTDIR", sprintf("%s%starget", PROJECT_ROOT, DIRECTORY_SEPARATOR));
define("TARGET_DIR", sprintf("%s%s%s/%s", TARGET_ROOTDIR, DIRECTORY_SEPARATOR, SCRIPT_NAME, date("Ymd-His")));
define("TARGET_FILENAME", sprintf("%s", SCRIPT_NAME));
define("TARGET_ORGDIR", sprintf("%s%sorg", TARGET_DIR, DIRECTORY_SEPARATOR));
define("TARGET_MODDIR", sprintf("%s%smod", TARGET_DIR, DIRECTORY_SEPARATOR));

define("CONF_CONTENT_URL", sprintf("%s/rest/api/content", CONF_BASE_URL));
define("CONF_SEARCH_URL", sprintf("%s/rest/api/search", CONF_BASE_URL));

define("MY_CERT_CA", MY_DIR . DIRECTORY_SEPARATOR . "cacert.pem");

function validateSettings() {
    if (!defined("CONF_BASE_URL")) {
        echo "FATAL - No URL for confluence is set";
        exit(1);
    }
    if (!defined("AUTH_TOKEN_NAME")) {
        define("AUTH_TOKEN_NAME", null);
    }
    if (!defined("SCRIPT_NAME")) {
        echo "FATAL - SCRIPT_NAME is not set";
        exit(1);
    }
    if (!defined("SEARCH_LIMIT")) {
        define("SEARCH_LIMIT", 100);
    }
}

validateSettings();