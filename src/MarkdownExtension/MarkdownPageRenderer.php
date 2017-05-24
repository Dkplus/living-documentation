<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\MarkdownExtension;

use Dkplus\LivingDocs\TwigExtension\TwigPageRenderer;
use InvalidArgumentException;
use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use function get_class;
use function var_dump;

class MarkdownPageRenderer implements BlockRendererInterface
{
    /** @var TwigPageRenderer */
    private $pageRenderer;

    public function __construct(TwigPageRenderer $pageRenderer)
    {
        $this->pageRenderer = $pageRenderer;
    }

    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, $inTightList = false)
    {
        if (! $block instanceof MarkdownPageBlock) {
            throw new InvalidArgumentException('Incompatible block type: ' . get_class($block));
        }

        var_dump($block->getPageId());

        return new HtmlElement(
            'div',
            ['id' => $block->getPageId()],
            $this->pageRenderer->renderBodyOfPage($block->getPageId())
        );
    }
}
