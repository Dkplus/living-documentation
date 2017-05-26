<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\TwigExtension;

use Twig_Extension;
use Twig_SimpleFunction;

class PathResolverTwigExtension extends Twig_Extension
{
    /** @var PathResolver */
    private $pathResolver;

    public function __construct(PathResolver $pathResolver)
    {
        $this->pathResolver = $pathResolver;
    }

    public function getFunctions(): array
    {
        return [
            new Twig_SimpleFunction('is_active_page', [$this->pathResolver, 'isActive']),
            new Twig_SimpleFunction('asset', [$this->pathResolver, 'asset']),
            new Twig_SimpleFunction('path', [$this->pathResolver, 'resolve']),
        ];
    }
}
