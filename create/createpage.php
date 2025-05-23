<?php
define('SCRIPT_NAME', basename(__FILE__, '.php'));
require_once __DIR__ . '/../common/func-common.inc.php';

CONST C_PLAYGROUND_ID = 532951146;
CONST C_SPACE         = 'NMAS';
CONST C_NEW_TITLE     = 'NEW PAGE %s-%s';
CONST C_NEW_BODY      = "<p>This is <br/> a new page</p>\n";
CONST C_NEW_MACRO_1   = "<p><ac:structured-macro ac:name=\"status\" ac:schema-version=\"1\"><ac:parameter ac:name=\"colour\">Green</ac:parameter><ac:parameter ac:name=\"title\">Low</ac:parameter></ac:structured-macro></p>";
define('C_NEW_MACRO_2', prepareMacro('status', ['title' => 'high', 'colour' => 'Red'], null));
define('C_NEW_MACRO_3', prepareMacro('html', null, "<style>span{border: 1pt solid darkred !important;}</style>"));
define(
    'C_NEW_MACRO_4',
    prepareMacro(
        'section',
        null,
        prepareMacro('column', null, 'left') . prepareMacro('column', null, 'right')
    )
);

function createEmptyPage($pageTitle, $spaceKey, $parent = null, $pageBody = '', $pageType = 'page') {
    $curlSession = prepareCurlWrite();
    $createUrl   = prepareCreatePage();

    $createData = [
        'type'   => $pageType,
        'title'  => $pageTitle,
        'status' => 'current',
        'body'   => [
            'storage' => [
                'value'          => $pageBody,
                'representation' => 'storage',
            ],
        ],
    ];
    if (isset($spaceKey)) {
        $createData['space'] = ['key' => $spaceKey];
    }

    if (is_numeric($parent)) {
        $createData['ancestors'] = [['id' => $parent]];
    }
    //var_dump($createData);
    curl_setopt($curlSession, CURLOPT_POSTFIELDS, json_encode($createData));

    $response = execCurl($curlSession, $createUrl);

    $success = checkDataWrite($response);
    if ($success !== false) {
        logMe("Insert page '%s' for '%s' is %s!\n", $success, $pageTitle, "successfull");
    } else {
        var_dump($response);
    }
}

function main() {
    $idx = 0;

    createEmptyPage(sprintf(C_NEW_TITLE, TS_NOW . '-' . $idx++), C_SPACE);
    createEmptyPage(
        sprintf(C_NEW_TITLE, TS_NOW, $idx++),
        C_SPACE,
        C_PLAYGROUND_ID,
        C_NEW_BODY . C_NEW_MACRO_1 . C_NEW_MACRO_2 . C_NEW_MACRO_3 . C_NEW_MACRO_4
    );
}

main();