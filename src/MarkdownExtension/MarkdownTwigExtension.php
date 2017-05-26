<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\MarkdownExtension;

use Dkplus\LivingDocumentation\TwigExtension\TwigPageRenderer;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Converter;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use Twig_Extension;
use Twig_Filter;

class MarkdownTwigExtension extends Twig_Extension
{
    /** @var CommonMarkConverter */
    private $converter;

    public function setPageRenderer(TwigPageRenderer $pageRenderer): void
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addBlockParser(new MarkdownPageParser());
        $environment->addBlockRenderer(MarkdownPageBlock::class, new MarkdownPageRenderer($pageRenderer));
        $this->converter = new Converter(new DocParser($environment), new HtmlRenderer($environment));
    }

    public function getFilters(): array
    {
        return [
            new Twig_Filter('markdown', [$this, 'convertMarkdown'], ['is_safe' => ['html']]),
        ];
    }

    public function convertMarkdown(string $text): string
    {
        return $this->converter->convertToHtml($text);
    }
}
