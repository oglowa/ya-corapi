<?php

define('CSV_LINE_PDT_PROPERTY_HEADER', 'name;value,controls;document-id;document-url');
define('CSV_LINE_PDT_PROPERTY', "%s;%s;%s;%s;%s");

define('PDT_RES_MOD_STORAGE', 'storage');
define('PDT_RES_MOD_HTML', 'html');
define('PDT_RES_MOD_VALUE', 'value');

define('PDT_MEDIA_TYPE_JSON', 'json');
define('PDT_MEDIA_TYPE_XML', 'xml');

define('PDT_RESULT_START_INDEX', 0);
define('PDT_RESULT_PAGE_SIZE', 10);
define('PDT_RESULT_MAX_SIZE', 20);

define('PDT_DOCUMENT_URL', CONF_BASE_URL . '/rest/projectdoc/1/document');
define('PDT_PROPERTY_URL', PDT_DOCUMENT_URL . '/%s/property');

define('PDT_PROP_NAME', 'Name');
define('PDT_PROP_SHORT_NAME', 'Short Name');
define('PDT_PROP_SHORT_DESCRIPTION', 'Short Description');
define('PDT_PROP_PARENT', 'Parent');
define('PDT_PROP_TYPE', 'Type');
define('PDT_PROP_ITERATION', 'Iteration');
define('PDT_PROP_AUDIENCE', 'Audience');
define('PDT_PROP_CATEGORIES', 'Categories');
define('PDT_PROP_SUBJECT', 'Subject');
define('PDT_PROP_TAGS', 'Tags');
define('PDT_PROP_FLAGS', 'Flags');
define('PDT_PROP_SORT_KEY', 'Sort Key');

define(
    'PDT_PROP_ALL_DEFAULT',
    [
        PDT_PROP_NAME,
        PDT_PROP_SHORT_NAME,
        PDT_PROP_SHORT_DESCRIPTION,
        PDT_PROP_PARENT,
        PDT_PROP_TYPE,
        PDT_PROP_ITERATION,
        PDT_PROP_AUDIENCE,
        PDT_PROP_CATEGORIES,
        PDT_PROP_SUBJECT,
        PDT_PROP_TAGS,
        PDT_PROP_FLAGS,
        PDT_PROP_SORT_KEY,
    ]
);

function preparePdtDocumentReadUrl(
    array $propertyNames,
    string $spaceKey,
    string $where = "",
    $mediaType = PDT_MEDIA_TYPE_JSON,
    $resourceMode = PDT_RES_MOD_STORAGE
): string {
    $prepareUrl = sprintf(
        "%s.%s?resource-mode=%s&select=%s&from=%s",
        PDT_DOCUMENT_URL,
        $mediaType,
        $resourceMode,
        rawurlencode(implode(',', $propertyNames)),
        $spaceKey
    );
    if (!empty($where)) {
        $prepareUrl .= sprintf("&where=%s", urlencode("\"" . $where . "\""));
    }

    $prepareUrl .= sprintf("&max-hit-count=%s", PDT_RESULT_MAX_SIZE);
    $prepareUrl .= sprintf("&start-index=%s", PDT_RESULT_START_INDEX);
    $prepareUrl .= sprintf("&max-result=%s", PDT_RESULT_PAGE_SIZE);
    $prepareUrl .= sprintf("&expand=%s", 'property');

    return $prepareUrl;
}

function preparePdtPropertyReadUrl(string $pageId, $propertyName, $resourceMode = PDT_RES_MOD_STORAGE): string {
    $prepareUrl = sprintf("%s/%s?resource-mode=%s", sprintf(PDT_PROPERTY_URL, $pageId), rawurlencode($propertyName), $resourceMode);

    return $prepareUrl;
}

function checkDataPdtProperty($response, bool $isQuite = true): bool {
    if (is_array($response)) {
        $hasData = checkStatus($response);
        if ($hasData) {
            if (!(key_exists('name', $response) && key_exists('value', $response))) {
                if (!$isQuite) {
                    logFast("Property is empty or incomplete!");
                }
                $hasData = false;
            }
        }
    } else {
        logFast("Response is empty or null!");
        $hasData = false;
    }

    return $hasData;
}

function prepareCsvLinePdtProperty(array $response, string $propertyName = ""): string {
    $line = "";
    if (is_array($response) && key_exists('name', $response)) {
        $line .= prepareCsvLine(
            CSV_LINE_PDT_PROPERTY,
            $response['name'],
            $response['value'],
            $response['controls'],
            $response['document-id'],
            $response['document-url']
        );
    } elseif (!empty($propertyName)) {
        $line .= prepareCsvLine(CSV_LINE_PDT_PROPERTY, $propertyName, "", "", "", "");
    }

    return $line;
}

function showResultsPdt(array $response, $propertyName, ?int $idx = null): string {
    $line = '';

    if (isset($idx)) {
        $line = sprintf("%s;", $idx);
    }
    $line .= prepareCsvLinePdtProperty($response, $propertyName);
    logMe("%s\n", $line);

    return $line;
}