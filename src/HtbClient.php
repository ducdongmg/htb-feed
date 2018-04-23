<?php

namespace Kawanamiyuu\HtbFeed;

use function GuzzleHttp\Promise\all;

class HtbClient
{
    /**
     * @var EntryListLoader
     */
    private $entryListLoader;

    /**
     * @var BookmarkExtractor
     */
    private $bookmarkExtractor;

    /**
     * @param EntryListLoader   $entryListLoader
     * @param BookmarkExtractor $bookmarkExtractor
     */
    public function __construct(EntryListLoader $entryListLoader, BookmarkExtractor $bookmarkExtractor)
    {
        $this->entryListLoader = $entryListLoader;
        $this->bookmarkExtractor = $bookmarkExtractor;
    }

    /**
     * @param string $category
     * @param int    $pages
     *
     * @return array
     */
    public function fetch(string $category, int $pages): array
    {
        $loader = $this->entryListLoader;
        $extractor = $this->bookmarkExtractor;

        $promises = [];
        foreach (range(1, $pages) as $page) {
            $promises[] = $loader($category, $page)->then(function ($html) use($extractor) {
                return $extractor($html);
            });
        }

        /* @var Bookmark[][] $results */
        $results = all($promises)->wait();

        return array_merge(...$results);
    }
}
