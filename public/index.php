<?php

use GuzzleHttp\Client;
use Kawanamiyuu\HtbFeed\AtomGenerator;
use Kawanamiyuu\HtbFeed\Bookmark;
use Kawanamiyuu\HtbFeed\BookmarkExtractor;
use Kawanamiyuu\HtbFeed\EntryListLoader;
use Kawanamiyuu\HtbFeed\HtbClient;

require dirname(__DIR__) . '/bootstrap/bootstrap.php';
require dirname(__DIR__) . '/vendor/autoload.php';

const CATEGORY = 'it';
const MAX_PAGE = 10;
const FEED_TITLE = 'はてなブックマークの新着エントリー';
const FEED_URL = 'http://www.example.com/atom';

$minUsers = (int) ($_GET['users'] ?? '') ?: 50;

$loader = new EntryListLoader(new Client);
$extractor = new BookmarkExtractor;
$client = new HtbClient($loader, $extractor);

$bookmarks = $client->fetch(CATEGORY, MAX_PAGE);
$bookmarks = array_filter($bookmarks, function (Bookmark $bookmark) use ($minUsers) {
    return $bookmark->users >= $minUsers;
});

$generator = new AtomGenerator($bookmarks, FEED_TITLE, FEED_URL);

header('Content-Type: application/atom+xml; charset=UTF-8');
echo  $generator() . "\n";
