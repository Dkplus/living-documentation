<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\MarkdownExtension;

use League\CommonMark\Block\Parser\AbstractBlockParser;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;
use function mb_strpos;
use function preg_match;
use function var_dump;

class MarkdownPageParser extends AbstractBlockParser
{
    public function parse(ContextInterface $context, Cursor $cursor): bool
    {
        if ($cursor->isIndented()) {
            return false;
        }

        if (mb_strpos($cursor->getLine(), 'page:') !== 0) {
            return false;
        }

        if (! preg_match('/^page:([^ ]+)$/i', $cursor->getLine(), $matches)) {
            return false;
        }
        $context->addBlock(new MarkdownPageBlock(trim($matches[1])));
        $context->setBlocksParsed(true);
        return true;
    }
}
