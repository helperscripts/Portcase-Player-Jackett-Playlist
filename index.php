<?php
require 'vendor/autoload.php';

use \FeedIo\Factory;
use \Spatie\ArrayToXml\ArrayToXml;

$jackettUrl = 'https://FULL_URL_TO/jackett';
$jackettApiKey = 'JACKETT_API_KEY';
$playlistSearchUrl = 'https://FULL_URL_TO/playlist/?t=[tracker]&q=[query]';

$format = (!isset($_GET['f']) || empty($_GET['f'])) ? 'json' : $_GET['f'];
$tracker = (!isset($_GET['t']) || empty($_GET['t'])) ? 'all' : $_GET['t'];
$query = (!isset($_GET['q']) || empty($_GET['q'])) ? date('Y') . ' 1080p' : $_GET['q'];

$url = $jackettUrl . '/api/v2.0/indexers/' . $tracker . '/results/torznab/api?apikey=' . $jackettApiKey . '&t=search&q=' . $query;

$feedIo = Factory::create()->getFeedIo();
$result = $feedIo->read($url);

$playlistArr['name'] = $tracker == 'all' ? 'All Trackers' : $result->getFeed()->getTitle();
$playlistArr['version'] = '1.0';
$playlistArr['search-url'] = str_replace('[tracker]', $tracker, $playlistSearchUrl);

$xmlArr['torrent'] = array();

foreach ($result->getFeed() as $item)
{
    $itemArr = array(
        'name' => $item->getTitle(),
        'source' => $item->getLink());

    $itemArr['date'] = $item->getLastModified()->format("YmdHis");

    foreach ($item->getAllElements() as $element)
    {
        if ($element->getName() == 'size')
        {
            $itemArr['size'] = $element->getValue('value');
        }
        else
        {
            if ($element->getName() == 'torznab:attr')
            {
                if ($element->getAttribute('name') == 'peers' && !empty($element->getAttribute('value')))
                {
                    $itemArr['peers'] = $element->getAttribute('value');
                }
                else
                {
                    if ($element->getAttribute('name') == 'seeders' && !empty($element->getAttribute('value')))
                    {
                        $itemArr['seeds'] = $element->getAttribute('value');
                    }
                }
            }
        }
    }

    $xmlArr['torrent'][] = $itemArr;
}

header('Content-Type: text/plain');

if ($format == 'xml')
{
    echo ArrayToXml::convert($xmlArr, [
        'rootElementName' => 'playlist',
        '_attributes' => $playlistArr,
    ], true, 'UTF-8');
}
else
{
    echo json_encode(array(
        'Name' => $playlistArr['name'],
        'Version' => $playlistArr['version'],
        'SearchUrl' => $playlistArr['search-url'],
        'Torrents' => $xmlArr['torrent']));
}