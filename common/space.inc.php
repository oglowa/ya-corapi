<?php

CONST SPACE_SINGLE = 1;
CONST SPACE_SIMPLE = 2;
CONST SPACE_ALL    = 99;

CONST C_MYSPACES = MY_DIR . DIRECTORY_SEPARATOR . "myspaces.inc.php";
if (file_exists(C_MYSPACES)) {
    include_once C_MYSPACES;
} else {
    echo sprintf("'%s' not loaded!", C_MYSPACES);
}

if (!function_exists("_getSpaceListAll")) {
    function _getSpaceListAll(): array {
        return [];
    }
}

function _getSpaceList1(): array {
    return ['NMAS'];
}

function _getSpaceList2(): array {
    return [];
}

function getSpaceList(int $mode = SPACE_SINGLE): array {
    switch ($mode) {
        case SPACE_SIMPLE:
            return _getSpaceList2();
            break;
        case SPACE_ALL:
            return _getSpaceListAll();
            break;
        default:
            return _getSpaceList1();
    }
}
