<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\PagesExtension;

use function array_keys;
use ArrayIterator;
use InvalidArgumentException;
use function array_diff;
use function array_filter;
use function array_map;
use function implode;
use function in_array;
use function mb_strpos;
use function preg_match;
use function preg_quote;
use function str_replace;

class ArrayProcessedPages implements ProcessedPages
{
    /** @var ProcessedPage[] */
    protected $pages;

    public function __construct(array $pages)
    {
        $this->pages = $pages;
    }

    public function withId(string $id): ProcessedPage
    {
        if (! isset($this->pages[$id])) {
            throw new InvalidArgumentException("Page $id does not exist");
        }
        return $this->pages[$id];
    }

    public function matching(array $matchers): ProcessedPages
    {
        $exactMatchers = array_filter($matchers, function (string $matcher): bool {
            return mb_strpos($matcher, '*') === false;
        });
        $regexMatchers = array_map(function (string $matcher): string {
            return str_replace('\\*', '.*', preg_quote($matcher, ''));
        }, array_diff($matchers, $exactMatchers));
        $regex = '/^(' . implode('|', $regexMatchers) . ')$/';
        $pages = array_filter($this->pages, function (string $id) use ($exactMatchers, $regex) {
            return in_array($id, $exactMatchers) || preg_match($regex, $id);
        }, ARRAY_FILTER_USE_KEY);
        return new ArrayProcessedPages($pages);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->pages);
    }

    public function ids(): array
    {
        return array_keys($this->pages);
    }
}
