<?php

/* --------------------------------------------------------------------------------------------- */
/*
 * REST API Functions
 */

function prepareSearchUrl($searchTerm, ?String $spaceKey = null, $pageType = 'page', bool $withBody = false): string {
    return _prepareSearchUrl($searchTerm, $spaceKey, null, null, $pageType, $withBody);
}

function _prepareSearchUrl($searchTerm, $spaceKey, $searchFromPos, $searchLimit = SEARCH_LIMIT, $pageType = 'page', bool $withBody = false): string {
    $prepareUrl = sprintf("%s?cql=", CONF_SEARCH_URL);
    $prepareUrl .= sprintf("siteSearch~%s", urlencode("\"{$searchTerm}\""));
    $prepareUrl .= sprintf("+AND+space.type=%s", urlencode("global"));
    $prepareUrl .= sprintf("+AND+type=%s", urlencode("\"{$pageType}\""));
    if (!is_null($spaceKey)) {
        $prepareUrl .= sprintf("+AND+space=%s", urlencode("\"{$spaceKey}\""));
    }
    if (!is_null($searchFromPos)) {
        $prepareUrl .= sprintf("&start=%s&limit=%s", $searchFromPos, $searchLimit);
    }
    $prepareUrl .= sprintf("&%s", ($withBody ? REQP_SEARCH_FULL : REQP_SEARCH_LIGHT));

    return $prepareUrl;
}

function prepareBrowseUrl($filterTerm, $spaceKey = null): string {
    $prepareUrl = sprintf("%s?%s&%s", CONF_CONTENT_URL, $filterTerm, REQP_LIGHT);
    $prepareUrl = addSpaceFilter($spaceKey, $prepareUrl);

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

    return $prepareUrl;
}

function prepareLoadUrl($pageId): string {
    $prepareUrl = sprintf("%s/%s?%s", CONF_CONTENT_URL, $pageId, REQP_FULL);

    return $prepareUrl;
}

/* --------------------------------------------------------------------------------------------- */
/*
 * Other Functions
 */
