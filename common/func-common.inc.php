<?php

require_once __DIR__ . '/const.inc.php';
require_once __DIR__ . '/data-space.inc.php';
require_once __DIR__ . '/data-addonmacro.inc.php';
require_once __DIR__ . '/api-read.inc.php';
require_once __DIR__ . '/api-write.inc.php';
require_once __DIR__ . '/api-space.inc.php';
require_once __DIR__ . '/api-projectdoc.inc.php';
require_once __DIR__ . '/func-console.inc.php';
require_once __DIR__ . '/func-file.inc.php';
require_once __DIR__ . '/func-statistic.inc.php';
require_once __DIR__ . '/func-dummy.inc.php';

/* --------------------------------------------------------------------------------------------- */
/*
 * CURL Functions
 */

function getTokenValue(): string {
    $tokenValue = getenv(AUTH_TOKEN_NAME);

    logFast(strlen($tokenValue) < 1 ? sprintf("\nToken '%s' is NOT set!\n", AUTH_TOKEN_NAME) : '');

    return $tokenValue;
}

function getAuthValue(): string {
    logFast(MSG_NOT_IMPLEMENTED);

    return '$username:$password';
}

function prepareAuthorisation(&$newSession): void {
    $token = getTokenValue();
    if (!(empty($token))) {
        curl_setopt(
            $newSession,
            CURLOPT_HTTPHEADER,
            [
                'Accept: application/json',
                'Content-Type: application/json',
                "Authorization: Bearer " . $token,
            ]
        );
    } else {
        $auth = getAuthValue();
        curl_setopt($newSession, CURLOPT_USERPWD, $auth);
    }
}

function prepareCertificate(&$newSession): void {
    if (file_exists(MY_CERT_CA)) {
        curl_setopt($newSession, CURLOPT_CAINFO, MY_CERT_CA);
    } else {
        logMe("CA certificate not found: '%s'", MY_CERT_CA);
        curl_setopt($newSession, CURLOPT_SSL_VERIFYPEER, false); // NOSONAR php:S4830
    }
}

/**
 *
 * @return false|resource
 */
function prepareCurl() {
    $newSession = curl_init();

    // Set cURL options
    curl_setopt($newSession, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($newSession, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    prepareCertificate($newSession);
    prepareAuthorisation($newSession);

    return $newSession;
}

/**
 *
 * @return false|resource
 */
function prepareCurlWrite() {
    $newSession = prepareCurl();
    //curl_setopt($newSession, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($newSession, CURLOPT_POST, true);
    curl_setopt($newSession, CURLOPT_POSTFIELDS, '');

    return $newSession;
}

/**
 * Run the query
 *
 * @param      $execSession
 * @param      $execUrl
 *
 * @param bool $dryRun
 *
 * @return mixed
 */
function execCurl($execSession, $execUrl, bool $dryRun = false) {
    logMe("\nexecCurl: %s\n\n", $execUrl);

    curl_setopt($execSession, CURLOPT_URL, $execUrl);
    if ($dryRun) {
        return dummyResponse(true);
    } else {
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
}

/* --------------------------------------------------------------------------------------------- */
/*
 * Other Functions
 */

function checkStatus($response) {
    $statusOk = true;

    if (key_exists('statusCode', $response)) {
        logMe(MSG_CURL_ERROR, $response['statusCode'], $response['reason'], $response['message']);
        $statusOk = false;
    }

    return $statusOk;
}

function checkData($response, bool $isQuite = false): bool {
    if (is_array($response)) {
        $hasData = checkStatus($response);
        if ($hasData) {
            if (!$isQuite) {
                showTotals($response);
            }
            if (!key_exists('results', $response) || $response['size'] <= 0) {
                logFast("Results is empty or null!");
                $hasData = false;
            }
        }
    } else {
        logFast("Response is empty or null!");
        $hasData = false;
    }

    return $hasData;
}

function CheckDataWrite($response): mixed {
    if (is_array($response)) {
        $hasData = checkStatus($response);
        if ($hasData) {
            if (!key_exists('id', $response) || $response['id'] <= 0) {
                logFast("No pageId found or 0!");
                $hasData = false;
            } else {
                $pageId = $response['id'];
                logMe("\nWrite action on '%s' => %s%s\n", $pageId, WEB_SHOW_PAGEID, $pageId);
                $hasData = $pageId;
            }
        }
    } else {
        logFast("Response is empty or null!");
        $hasData = false;
    }

    return $hasData;
}

function addSpaceFilter($spaceKey, $prepareUrl): string {
    if (!empty($spaceKey)) {
        $prepareUrl .= sprintf("&spaceKey=%s", $spaceKey);
    }

    return $prepareUrl;
}
