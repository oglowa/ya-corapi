<?php
// Autoload files using the Composer autoloader.
require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../../../../common/functions.inc.php';

use CloudPlayDev\ConfluenceClient\ConfluenceClient;

$spaceKey   = 'NMAS';
$searchTerm = 'title=REST-API%2001';
$searchText = 'REST-API%2001';
$pageId     = 591855803;

/* @var $client CloudPlayDev\ConfluenceClient\ConfluenceClient */
$client = new ConfluenceClient(CONF_BASE_URL);

/* @var $myClient Http\Client\Common\HttpMethodsClient */
$myClient = $client->getHttpClient();

var_dump($myClient);

//$client->authenticate(getTokenValue());

//$resultContent = $client->content()->get($pageId);
//var_dump($resultContent);
//
//$resultContentInVersion2 = $client->content()->get($pageId, 2);
//var_dump($resultContentInVersion2)
//
//$searchResults = $client->content()->find([
//    'spaceKey' => $spaceKey,
//    'title' => $searchText
//]);
//
//$createdPage = $searchResults->getResultAt(0);
//var_dump($createdPage);
//
//$page = new ContentPage();
//$page->setId($pageId);
//
//$childContent = $client->content()->children($page, Content::CONTENT_TYPE_PAGE);
//var_dump($childContent);
//
//$historyData = $client->content()->history($pageId);
//var_dump($historyData);
