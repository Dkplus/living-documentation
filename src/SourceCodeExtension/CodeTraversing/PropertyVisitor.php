<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\SourceCodeExtension\CodeTraversing;

use PhpParser\Comment\Doc;
use PhpParser\NodeVisitorAbstract;
use Zend\Code\Reflection\DocBlock\Tag\GenericTag;
use Zend\Code\Reflection\DocBlockReflection;

class PropertyVisitor extends NodeVisitorAbstract
{
    /** @var array */
    private $properties = [];

    public function foundProperties()
    {

    }

    private function getDependenciesFromDocBlock(?Doc $docBlock): array
    {
        if (! $docBlock) {
            return [];
        }
        $tags = (new DocBlockReflection($docBlock->getReformattedText()))->getTags();

        /* @var $tags GenericTag[] */
        $tags = array_filter($tags, function ($tag) {
            return $tag instanceof GenericTag
                && in_array($tag->getName(), ['var', 'return', 'param', 'throws']);
        });
        $ignoredValues = [
            'string',
            'int',
            'integer',
            'float',
            'double',
            'bool',
            'boolean',
            'object',
            'mixed',
            'resource',
            'callable',
            'callback',
        ];
        $result = [];
        foreach ($tags as $each) {
            $tagValues = explode('|', $each->getContent());
            $tagValues = array_diff($tagValues, $ignoredValues);
            foreach ($tagValues as $eachClassName) {
                if (($firstWhitespace = mb_strpos(' ', $eachClassName)) !== false) {
                    $eachClassName = mb_substr($eachClassName, 0, $firstWhitespace);
                }
                $multiplicity = '1';
                if (mb_substr($eachClassName, -2) === '[]') {
                    $multiplicity = '*';
                    $eachClassName = trim($eachClassName, '[]');
                }
                if (mb_strpos($eachClassName, '\\') === 0) {
                    $result[$eachClassName] = $multiplicity;
                    continue;
                }
                $result[$this->uses[$eachClassName] ?? $this->namespace . '\\' . $eachClassName] = $multiplicity;
            }
        }
        return $result;
    }
}
