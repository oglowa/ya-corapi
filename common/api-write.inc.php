<?php

/* --------------------------------------------------------------------------------------------- */
/*
 * REST API Functions
 */

define('MACROBODY_PLAIN', 'html;');
define('MACROBODY_RICHTEXT', 'section;column;');
define('CHOOSE_BODY_RICHTEXT', 'rich');
define('CHOOSE_BODY_PLAIN', 'plain');

function prepareUpdateURL($pageId): string {
    $prepareUrl = sprintf("%s/%s", CONF_CONTENT_URL, $pageId);

    return $prepareUrl;
}

function prepareCreatePage(): string {
    $prepareUrl = sprintf("%s/", CONF_CONTENT_URL);

    return $prepareUrl;
}

function prepareMacroParameter(?array $parameter = []): string {
    $newTag = '';
    if (isset($parameter) && sizeof($parameter) > 0) {
        foreach ($parameter as $item => $value) {
            $newTag .= sprintf("\n<ac:parameter ac:name=\"%s\">%s</ac:parameter>", $item, $value);
        }
    }

    return $newTag;
}

function preparePlainBody(?string $body = ''): string {
    $newTag = '';
    if (!empty($body)) {
        $newTag = sprintf("<ac:plain-text-body>\n<![CDATA[%s]]>\n</ac:plain-text-body>", $body);
    }

    return $newTag;
}

function prepareRichTextBody(?string $body = ''): string {
    $newTag = '';
    if (!empty($body)) {
        $newTag = sprintf("<ac:rich-text-body>\n%s\n</ac:rich-text-body>", $body);
    }

    return $newTag;
}

function chooseMacroBody($macroName = '') {
    $choose = '';
    switch (true) {
        case strpos(MACROBODY_PLAIN, strtolower($macroName . ';')) !== false:
            $choose = CHOOSE_BODY_PLAIN;
            break;
        case strpos(MACROBODY_RICHTEXT, strtolower($macroName . ';')) !== false:
            $choose = CHOOSE_BODY_RICHTEXT;
            break;
        default:
    }

    return $choose;
}

function prepareMacroBody($macroName, ?string $body = ''): string {
    $newTag = '';
    if (!empty($body)) {
        switch (chooseMacroBody($macroName)) {
            case CHOOSE_BODY_PLAIN:
                $newTag .= sprintf("\n%s", preparePlainBody($body));
                break;
            case CHOOSE_BODY_RICHTEXT:
                $newTag .= sprintf("\n%s", prepareRichTextBody($body));
                break;
            default:
                $newTag .= $body;
        }
    }

    return $newTag;
}

function prepareMacro(string $macroName, ?array $parameter = [], ?string $body = ''): string {
    $newTag = '';
    $newTag .= sprintf("<ac:structured-macro ac:name=\"%s\" ac:schema-version=\"%s\">", $macroName, "1");
    $newTag .= prepareMacroParameter($parameter);
    $newTag .= prepareMacroBody($macroName, $body);
    $newTag .= "\n</ac:structured-macro>";

    return $newTag;
}

/* --------------------------------------------------------------------------------------------- */
/*
 * Other Functions
 */

function modifyData($orgDir, $response, $modDir, $searchText, $replaceText): void {
    if (is_array($response) && array_key_exists('results', $response)) {
        $idx = 0;
        foreach ($response['results'] as $page) {
            $pageId    = $page['id'];
            $pageTitle = $page['title'];
            $pageBody  = $page['body']['storage']['value'];
            logMe("\n%s. %s\t%s", $idx, $pageTitle, $pageId);
            storeData($orgDir, $pageId, $pageBody);
            modifyBody($modDir, $pageId, $pageBody, $searchText, $replaceText);
            $idx++;
        }
    }
}

function modifyBody($modDir, $id, $oldBody, $searchText, $replaceText): void {
    if ($searchText) {
        $newBody = preg_replace(
            "/(projectdoc-properties-marker. ac:schema-version=.1. ac:macro-id=.\w{8}-\w{4}-\w{4}-\w{4}-\w{12}.><ac:parameter ac:name=.doctype.>)(" .
            $searchText .
            ")(<\/ac:parameter>)/",
            "$1" . $replaceText . "$3",
            $oldBody
        );
    } else {
        $newBody = $oldBody;
    }
    //str_replace($searchText, $replaceText, $oldBody);
    // grep -Po "projectdoc-properties-marker. ac:schema-version=.1. ac:macro-id=.\w{8}-\w{4}-\w{4}-\w{4}-\w{12}.><ac:parameter ac:name=.doctype.>.+?</ac:parameter>" *
    if ($oldBody !== $newBody) {
        storeData($modDir, $id, $newBody);
    } else {
        logMe("\nNot changed '%s'", $id);
    }
}

function updatePage($pageId, $pageBody, $pageVersion, $pageTitle, $comment = 'Update page without changes', $pageType = 'page'): bool {
    $success = false;

    if (is_numeric($pageVersion)) {
        $nextVersion = $pageVersion + 1;

        $writeSession = prepareCurl();
        $updateURL    = prepareUpdateURL($pageId);

        $postUpdate = [
            'id'      => $pageId,
            'type'    => $pageType,
            'title'   => $pageTitle,
            'body'    => ['storage' => ['value' => $pageBody, 'representation' => 'storage']],
            'version' => ['number' => $nextVersion, 'message' => $comment],
        ];

        curl_setopt_array(
            $writeSession,
            [
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS    => json_encode($postUpdate),
                //        CURLOPT_URL           => 'http://localhost:6003/rest/api/content/65604',
                //        CURLOPT_PORT          => '6003',
                //        CURLOPT_ENCODING => '',
                //        CURLOPT_MAXREDIRS => 10,
                //        CURLOPT_TIMEOUT => 30,
                //        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            ]
        );

//    curl_close($writeSession);
//    logMe("Exec updatePage %s\n", $pageId);
        $response = execCurl($writeSession, $updateURL);
        $success  = checkData($response);
        logMe("Update page '%s' for '%s' is %s!\n", $pageId, $pageTitle, $success ? "successful" : "failed");
    } else {
        logMe("No current version provided for '%s'!\n", $pageId);
    }

    return $success;
}
