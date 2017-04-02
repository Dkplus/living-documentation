<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Rendering;

use Twig_Extension;
use Twig_SimpleFunction;

class LinkExtension extends Twig_Extension
{
    /** @var LinkResolver */
    private $linkResolver;

    public function setLinkResolver(LinkResolver $linkResolver): void
    {
        $this->linkResolver = $linkResolver;
    }

    public function getFunctions(): array
    {
        return [new Twig_SimpleFunction('path', [$this, 'resolveLink'])];
    }

    public function resolveLink(string $route): string
    {
        return $this->linkResolver->resolve($route);
    }
}
