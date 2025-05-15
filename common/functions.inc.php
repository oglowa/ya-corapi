<?php

require_once __DIR__ . "/const.inc.php";
require_once __DIR__ . "/space.inc.php";
require_once __DIR__ . "/addonmacro.inc.php";
require_once __DIR__ . "/output.inc.php";
require_once __DIR__ . "/statistic.inc.php";

/* --------------------------------------------------------------------------------------------- */
/*
 * CURL Functions
 */

/**
 *
 * @return false|resource
 */
function prepareCurl() {
    $newSession = curl_init();

    // Set cURL options
    curl_setopt($newSession, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($newSession, CURLOPT_CAINFO, sprintf("%s/cacert.pem", __DIR__));

    //curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    curl_setopt(
        $newSession,
        CURLOPT_HTTPHEADER,
        [
            'Content-Type: application/json',
            "Authorization: Bearer " . getTokenValue(),
        ]
    );
    curl_setopt($newSession, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

    return $newSession;
}

function getTokenValue(): string {
    $tokenValue = getenv(AUTH_TOKEN_NAME);
    echo strlen($tokenValue) < 1 ? sprintf("\nToken '%s' is NOT set!\n", AUTH_TOKEN_NAME) : '';

    return $tokenValue;
}

function updatePage($pageId, $pageBody, $pageVersion, $pageTitle, $comment = "Update page without changes", $pageType = "page"): bool {
    $success = false;

    if (is_numeric($pageVersion)) {
        $nextVersion = $pageVersion + 1;

        $writeSession = prepareCurl();
        $updateURL    = prepareUpdateURL($pageId);

        $postUpdate = [
            "id"      => $pageId,
            "type"    => $pageType,
            "title"   => $pageTitle,
            //"space"   => ["key" => $spaceKey],
            "body"    => ["storage" => ["value" => $pageBody, "representation" => "storage"]],
            "version" => ["number" => $nextVersion, "message" => $comment],
        ];

        curl_setopt_array(
            $writeSession,
            [
                CURLOPT_CUSTOMREQUEST => "PUT",
                CURLOPT_POSTFIELDS    => json_encode($postUpdate),
                //        CURLOPT_URL           => "http://localhost:6003/rest/api/content/65604",
                //        CURLOPT_PORT          => "6003",
                //        CURLOPT_ENCODING => "",
                //        CURLOPT_MAXREDIRS => 10,
                //        CURLOPT_TIMEOUT => 30,
                //        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            ]
        );

//    curl_close($writeSession);
//    logMe("Exec updatePage %s\n", $pageId);
        $result  = execCurl($writeSession, $updateURL);
        $success = checkData($result);
        logMe("Update page '%s' for '%s' is %s!\n", $pageId, $pageTitle, $success ? "successful" : "failed");
    } else {
        logMe("No current version provided for '%s'!\n", $pageId);
    }

    return $success;
}

/**
 * Run the query
 *
 * @param $execSession
 * @param $execUrl
 *
 * @return mixed
 */
function execCurl($execSession, $execUrl) {
    curl_setopt($execSession, CURLOPT_URL, $execUrl);
    $response = curl_exec($execSession);

    // Check for errors
    if (curl_errno($execSession)) {
        logMe("\nError:%s", curl_error($execSession));
    }

    // Close the cURL session
    curl_close($execSession);

    // Decode the response
    return json_decode($response, true);
}

/* --------------------------------------------------------------------------------------------- */
/*
 * File Functions
 */

/**
 * @return void
 */
function prepareFilesystem(): void {
    logMe("TARGET_ROOT: '%s'\n", TARGET_ROOTDIR);
    logMe("TARGET_DIR : '%s'\n", TARGET_DIR);
    if (!file_exists(TARGET_ROOTDIR)) {
        mkdir(TARGET_ROOTDIR);
    }
    mkdir(TARGET_DIR, 0777, true);
    mkdir(TARGET_ORGDIR);
    mkdir(TARGET_MODDIR);
}

/**
 * @param TARGET_DIR
 * @param $page
 *
 * @return void
 */
function storeResults($targetDir, $page): void {
    $output_file = sprintf("%s%srisks-prod.csv", $targetDir, DIRECTORY_SEPARATOR);
    $pageId      = $page['id'];
    $line        = sprintf("%s;%s%s;%s", $pageId, CONF_BASE_URL, $page['_links']['tinyui'], $page['title']);
    //logMe("%s\n", $line);

    file_put_contents($output_file, sprintf("%s\n", $line), FILE_APPEND);
}

function storeCsv($pathToFile, $fileName, $content): void {
    $output_file = sprintf("%s%s%s.csv", $pathToFile, DIRECTORY_SEPARATOR, $fileName);
    logMe("Writing to '%s'\n", $output_file);

    file_put_contents($output_file, $content, FILE_APPEND);
}

function storeText($pathToFile, $fileName, $content): void {
    $output_file = sprintf("%s%s%s", $pathToFile, DIRECTORY_SEPARATOR, $fileName);
    logMe("Writing to '%s'\n", $output_file);

    file_put_contents($output_file, $content, FILE_APPEND);
}

/**
 * @param $targetDir
 * @param $pageId
 * @param $body
 *
 * @return void
 */
function storeData($targetDir, $pageId, $body): void {
    $output_file = sprintf("%s%s%s.xml", $targetDir, DIRECTORY_SEPARATOR, $pageId);
    logMe("Writing to '%s'\n", $output_file);

    file_put_contents($output_file, $body);
}

/**
 * @param $fileName
 *
 * @return array
 */
function readResultFile($fileName): array {
    $resultList = [];
    if (file_exists($fileName)) {
        $fHandle = fopen($fileName, "r");

        while ($line = fgets($fHandle, 1000)) {
            $nl           = mb_convert_encoding($line, 'UTF-8');
            $resultList[] = explode(";", $nl);
        }
        fclose($fHandle);
    } else {
        logMe("+++ file '%s' does not exists! +++\n", $fileName);
    }

    return $resultList;
}

/* --------------------------------------------------------------------------------------------- */
/*
 * Page Content Functions
 */

/**
 * Check, if there is data in the result
 *
 * @param      $responseData
 *
 * @param bool $isQuite
 *
 * @return bool
 */
function checkData($responseData, bool $isQuite = false): bool {
    $hasData = true;

    if (isset($responseData)) {
        if (key_exists('statusCode', $responseData)) {
            logMe("Statuscode : %s\n", $responseData['statusCode']);
            var_dump($responseData);
            $hasData = false;
        } else {
            if (!$isQuite) {
                showTotals($responseData);
            }
        }
    } else {
        echo "Nothing found\n";
        $hasData = false;
    }

    return $hasData;
}

function modifyData($orgDir, $responseData, $modDir, $searchText, $replaceText): void {
    if (isset($responseData) && array_key_exists('results', $responseData)) {
        $idx = 0;
        foreach ($responseData['results'] as $page) {
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

/* --------------------------------------------------------------------------------------------- */
/*
 * Misc Functions
 */

function prepareSearchUrl1($searchTerm, ?String $spaceKey = null, bool $withBody = false): string {
    return prepareSearchUrl2($spaceKey, $searchTerm, null, null, $withBody);
}

function prepareSearchUrl2($spaceKey, $searchTerm, $searchFromPos, $searchLimit = SEARCH_LIMIT, bool $withBody = false): string {
    $prepareUrl = sprintf("%s?cql=", CONF_SEARCH_URL);
    $prepareUrl .= sprintf("siteSearch~%s", urlencode("\"{$searchTerm}\""));
    $prepareUrl .= sprintf("+AND+space.type=%s", urlencode("global"));
    $prepareUrl .= sprintf("+AND+type=%s", urlencode("\"page\""));
    if (!is_null($spaceKey)) {
        $prepareUrl .= sprintf("+AND+space=%s", urlencode("\"{$spaceKey}\""));
    }
    if (!is_null($searchFromPos)) {
        $prepareUrl .= sprintf("&start=%s&limit=%s", $searchFromPos, $searchLimit);
    }
    $prepareUrl .= sprintf("&%s", ($withBody ? REQP_SEARCH_FULL : REQP_SEARCH_LIGHT));

    //logMe("\n%s\n\n", $prepareUrl);

    return $prepareUrl;
}

function addSpaceFilter($spaceKey, $prepareUrl): string {
    if (isset($spaceKey) && strlen($spaceKey) > 0) {
        $prepareUrl .= sprintf("&spaceKey=%s", $spaceKey);
    }

    return $prepareUrl;
}

function prepareBrowseUrl($filterTerm, $spaceKey = null): string {
    $prepareUrl = sprintf("%s?%s&%s", CONF_CONTENT_URL, $filterTerm, REQP_LIGHT);
    $prepareUrl = addSpaceFilter($spaceKey, $prepareUrl);
    logMe("\n%s\n\n", $prepareUrl);

    return $prepareUrl;
}

function prepareScanUrl($filterTerm, $spaceKey = null): string {
    $prepareUrl = sprintf("%s/scan?%s&%s", CONF_CONTENT_URL, $filterTerm, REQP_FULL);
    $prepareUrl = addSpaceFilter($spaceKey, $prepareUrl);
    logMe("%s\n", $prepareUrl);

    return $prepareUrl;
}

function prepareApiByPageIdUrl($pageId): string {
    $prepareUrl = sprintf("%s/%s?%s", CONF_CONTENT_URL, $pageId, REQP_LIGHT);
    logMe("\n%s\n\n", $prepareUrl);

    return $prepareUrl;
}

function prepareLoadUrl($pageId): string {
    $prepareUrl = sprintf("%s/%s?%s", CONF_CONTENT_URL, $pageId, REQP_FULL);
    logMe("\n%s\n\n", $prepareUrl);

    return $prepareUrl;
}

function prepareUpdateURL($pageId): string {
    $prepareUrl = sprintf("%s/%s", CONF_CONTENT_URL, $pageId);
    logMe("\n%s\n\n", $prepareUrl);

    return $prepareUrl;
}

function prepareSpaceListUrl($spaceType = "global", $limit = 25): string {
    $prepareUrl = sprintf("%s/%s", CONF_BASE_URL, "rest/api/space?expand=homepage,description.plain,metadata.labels&type={$spaceType}&limit={$limit}");
    logMe("\n%s\n\n", $prepareUrl);

    return $prepareUrl;
}

function resultPosUpdate($startNow, $sizeNow, $totalsNow): void {
    global $currentPos, $nextPos, $totalSize;

    logMe("CURRENT: currentPos: %s / nextPos: %s / totalSize: %s\n", $currentPos, $nextPos, $totalSize);
    $totalSize  = $totalsNow;
    $currentPos = $startNow;
    $nextPos    = $startNow + $sizeNow;
    logMe("NEW    : currentPos: %s / nextPos: %s / totalSize: %s\n", $currentPos, $nextPos, $totalSize);
}
