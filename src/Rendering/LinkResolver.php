<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Rendering;

use function array_map;
use function array_merge;
use function mb_strlen;

class LinkResolver
{
    /** @var string[] */
    private $resolvable = [];

    /** @var array */
    private $linksByPrefix = [];

    public function link(string $link, string $identifier, ?string $prefix): void
    {
        $prefixedIdentifier = $prefix !== null ? $prefix . '/' . $identifier : $identifier;
        $this->resolvable[$prefixedIdentifier] = $link;
        if ($prefix !== null) {
            $this->linksByPrefix[$prefix][$identifier] = $link;
        }
    }

    public function forPrefix(string $prefix): LinkResolver
    {
        $result = new LinkResolver();
        $prefixResolvables = [];
        foreach ($this->linksByPrefix as $eachPrefix => $identifiers) {
            if (mb_strpos($eachPrefix, $prefix) === 0) {
                $newPrefix = ltrim(mb_substr($eachPrefix, mb_strlen($prefix)), '/');
                $prefixResolvables[] = array_combine(array_map(function (string $identifier) use ($newPrefix) {
                    return $newPrefix . '/' . $identifier;
                }, array_keys($identifiers)), $identifiers);
            }
        }
        $result->resolvable = array_merge($this->resolvable, ...$prefixResolvables);
        return $result;
    }

    public function resolve(string $identifier): string
    {
        return $this->resolvable[$identifier];
    }
}
