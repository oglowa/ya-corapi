<?php

CONST REQP_LIGHT='expand=space,history,version';
CONST REQP_FULL= REQP_LIGHT.',body.storage';
CONST REQP_PERM='expand=read.restrictions.user,read.restrictions.group,update.restrictions.user,update.restrictions.group';

CONST REQP_SEARCH_LIGHT='expand=content.space,content.history,content.version';
CONST REQP_SEARCH_FULL= REQP_SEARCH_LIGHT.',content.body.storage';

if (!isset($confUrl)) {
    $confUrl = "";
}
if (!isset($scriptName)) {
    $scriptName = "TBD";
}
if (!isset($searchLimit)) {
    $searchLimit = 100;
}
if (!isset($tokenName)) {
    $tokenName = null;
}

$totalSize  = 0;
$currentPos = 0;
$nextPos    = 0;

$curlSession  = null;
$writeSession = null;

$inputRootDir  = sprintf("%s/input/", dirname(__FILE__));
$inputDir      = sprintf("%s%s", $inputRootDir, $scriptName);

$targetRootDir  = sprintf("%s/target/", dirname(__FILE__));
$targetDir      = sprintf("%s%s/%s", $targetRootDir, $scriptName, date("Ymd-His"));
$targetOrgDir   = sprintf("%s/org", $targetDir);
$targetModDir   = sprintf("%s/mod", $targetDir);

$restContentUrl = sprintf("%s/rest/api/content", $confUrl);
$restSearchUrl = sprintf("%s/rest/api/search", $confUrl);

/* --------------------------------------------------------------------------------------------- */
/*
 * CURL Functions
 */

/**
 * @param $tokenName
 *
 * @return false|resource
 */
function prepareCurl($tokenName) {
    $newSession = curl_init();

    $tokenValue = getenv($tokenName);
    echo strlen($tokenValue)<1 ? sprintf("\nToken '%s' is NOT set!\n", $tokenName):'';

    // Set cURL options
    curl_setopt($newSession, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($newSession, CURLOPT_CAINFO, sprintf("%s/cacert.pem", dirname(__FILE__)));

    //curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($newSession, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: Bearer {$tokenValue}"
    ]);
    curl_setopt($newSession, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

    return $newSession;
}

function updatePage($pageId, $pageBody, $pageVersion, $pageTitle, $comment = "Update page without changes", $pageType = "page"): bool {
    global $writeSession, $tokenName;

    $success = false;

    if (is_numeric($pageVersion)) {
        $nextVersion = $pageVersion + 1;

        $writeSession = prepareCurl($tokenName);
        $updateURL    = prepareUpdateURL($pageId);

        $postUpdate = [
            "id"      => $pageId,
            "type"    => $pageType,
            "title"   => $pageTitle,
            //"space"   => ["key" => $spaceKey],
            "body"    => ["storage" => ["value" => $pageBody, "representation" => "storage"]],
            "version" => ["number" => $nextVersion, "message" => $comment],
        ];

        curl_setopt_array($writeSession, [
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS    => json_encode($postUpdate),
            //        CURLOPT_URL           => "http://localhost:6003/rest/api/content/65604",
            //        CURLOPT_PORT          => "6003",
            //        CURLOPT_ENCODING => "",
            //        CURLOPT_MAXREDIRS => 10,
            //        CURLOPT_TIMEOUT => 30,
            //        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        ]);

//    curl_close($writeSession);
//    echo sprintf("Exec updatePage %s\n", $pageId);
        $result  = execCurl($writeSession, $updateURL);
        $success = checkData($result);
        echo sprintf("Update page '%s' for '%s' is %s!\n", $pageId, $pageTitle, $success ? "successful" : "failed");
    } else {
        echo sprintf("No current version provided for '%s'!\n", $pageId);
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
        echo sprintf("\nError:%s", curl_error($execSession));
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
    global $targetRootDir, $targetDir, $targetOrgDir, $targetModDir;

    echo sprintf("target: %s\n", $targetDir);
    if (!file_exists($targetRootDir)) {
        mkdir($targetRootDir);
    }
    mkdir($targetDir, 0777, true);
    mkdir($targetOrgDir);
    mkdir($targetModDir);
}

/**
 * @param $page
 *
 * @return void
 */
function showResults($page, $idx = null): void {
    global $confUrl;
    $line='';
    if ($idx) {
        $line = sprintf("%s;", $idx);
    }
    $line .= sprintf("%s;%s;\"%s\";%s;%s%s",
        isset($page['content']['id']) ? $page['content']['id'] : $page['id'],
        isset($page['content']['space']['key']) ? $page['content']['space']['key'] : $page['space']['key'] ,
        isset($page['content']['title']) ? $page['content']['title'] : $page['title'],
        isset($page['content']['type']) ? $page['content']['type'] : $page['type'],
        isset($page['_links']['base']) ? $page['_links']['base'] : $confUrl,
        isset($page['content']['_links']['webui']) ? $page['content']['_links']['webui'] : (isset($page['url']) ? $page['url'] : $page['_links']['webui'])
    );
    echo sprintf("%s\n", $line);
}

/**
 * @param $targetDir
 * @param $page
 *
 * @return void
 */
function storeResults($targetDir, $page): void {
    global $confUrl;

    $output_file = sprintf("%s/risks-prod.csv", $targetDir);
    $pageId      = $page['id'];
    $line        = sprintf("%s;%s%s;%s", $pageId, $confUrl, $page['_links']['tinyui'], $page['title']);
    //echo sprintf("%s\n", $line);

    file_put_contents($output_file, sprintf("%s\n", $line), FILE_APPEND);
}

/**
 * @param $targetDir
 * @param $pageId
 * @param $body
 *
 * @return void
 */
function storeData($targetDir, $pageId, $body): void {
    $output_file = sprintf("%s/%s.xml", $targetDir, $pageId);
    echo sprintf("writing to '%s'\n", $output_file);

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
        echo sprintf("+++ file '%s' does not exists! +++\n", $fileName);
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
 * @param $responseData
 *
 * @return bool
 */
function checkData($responseData): bool {
    $hasData = true;

    if ($responseData) {
        if (key_exists('statusCode', $responseData)) {
            echo sprintf("Statuscode : %s\n", $responseData['statusCode']);
            var_dump($responseData);
            $hasData = false;
        } else {
            echo sprintf(
                "start: %s / size: %s / limit: %s / total: %s\n",
                isset($responseData['start']) ? $responseData['start'] : '-',
                isset($responseData['size']) ? $responseData['size'] : '-',
                isset($responseData['limit']) ? $responseData['limit'] : '-',
                isset($responseData['total']) ? $responseData['totalSize'] : '-'
            );
        }
    } else {
        echo "Nothing found\n";
        $hasData = false;
    }

    return $hasData;
}

function modifyData($orgDir, $responseData, $modDir, $searchText, $replaceText): void {
    if ($responseData && array_key_exists('results', $responseData)) {
        $idx = 0;
        foreach ($responseData['results'] as $page) {
            $pageId    = $page['id'];
            $pageTitle = $page['title'];
            $pageBody  = $page['body']['storage']['value'];
            echo sprintf("\n%s. %s\t%s", $idx, $pageTitle, $pageId);
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
        echo sprintf("\nNot changed '%s'", $id);
    }
}

/* --------------------------------------------------------------------------------------------- */
/*
 * Misc Functions
 */

/**
 * Prepare the Confluence API endpoint
 *
 * @param      $spaceKey
 * @param      $searchTerm
 * @param      $searchFromPos
 * @param      $searchLimit
 * @param bool $withBody
 *
 * @return string
 */
function prepareSearchUrl($spaceKey, $searchTerm, $searchFromPos, $searchLimit, bool $withBody = false): string {
    global $restSearchUrl;

    $prepareUrl = sprintf("%s?cql=", $restSearchUrl);
    $prepareUrl .= sprintf("siteSearch~%s", urlencode("\"{$searchTerm}\""));
    $prepareUrl .= sprintf("AND+type=%s+AND+space=%s", urlencode("\"page\""), urlencode("\"{$spaceKey}\""));
    $prepareUrl .= sprintf("&start=%s&limit=%s", $searchFromPos, $searchLimit);
    $prepareUrl .= sprintf("&%s", ($withBody ? REQP_SEARCH_FULL : REQP_SEARCH_LIGHT));
    echo sprintf("\n%s\n\n", $prepareUrl);

    return $prepareUrl;
}

function addSpaceFilter($spaceKey, $prepareUrl): string {
    if (isset($spaceKey) && strlen($spaceKey) > 0) {
        $prepareUrl .= sprintf("&spaceKey=%s", $spaceKey);
    }
    return $prepareUrl;
}

function prepareBrowseUrl($filterTerm, $spaceKey = null): string {
    global $restContentUrl;

    $prepareUrl = sprintf("%s?%s&%s", $restContentUrl, $filterTerm, REQP_LIGHT);
    $prepareUrl = addSpaceFilter($spaceKey, $prepareUrl);
    echo sprintf("\n%s\n\n", $prepareUrl);

    return $prepareUrl;
}

function prepareScanUrl($filterTerm, $spaceKey = null): string {
    global $restContentUrl;

    $prepareUrl = sprintf("%s/scan?%s&%s", $restContentUrl, $filterTerm, REQP_FULL);
    $prepareUrl = addSpaceFilter($spaceKey, $prepareUrl);
    echo sprintf("%s\n", $prepareUrl);

    return $prepareUrl;
}

function prepareApiByPageIdUrl($pageId): string {
    global $restContentUrl;

    $prepareUrl = sprintf("%s/%s?%s", $restContentUrl, $pageId, REQP_LIGHT);
    echo sprintf("\n%s\n\n", $prepareUrl);

    return $prepareUrl;
}


function prepareLoadUrl($pageId): string {
    global $restContentUrl;

    $prepareUrl = sprintf("%s/%s?%s", $restContentUrl, $pageId, REQP_FULL);
    echo sprintf("\n%s\n\n", $prepareUrl);

    return $prepareUrl;
}

function prepareUpdateURL($pageId): string {
    global $restContentUrl;

    $prepareUrl = sprintf("%s/%s", $restContentUrl, $pageId);
    echo sprintf("\n%s\n\n", $prepareUrl);

    return $prepareUrl;
}

function resultPosUpdate($startNow, $sizeNow, $totalSizeNow): void {
    global $currentPos, $nextPos, $totalSize;

    echo sprintf("CURRENT: currentPos: %s / nextPos: %s / totalSize: %s\n", $currentPos, $nextPos, $totalSize );
    $totalSize  = $totalSizeNow;
    $currentPos = $startNow;
    $nextPos    = $startNow + $sizeNow;
    echo sprintf("NEW    : currentPos: %s / nextPos: %s / totalSize: %s\n", $currentPos, $nextPos, $totalSize );
}

/**
 * Returns a single result as dummy.
 *
 * @return array[]
 * @noinspection PhpUnused
 */
function dummyResult(): array {
    return [0 => [0 => 543326561, 1 => null, 2 => "TEST"]];
}
