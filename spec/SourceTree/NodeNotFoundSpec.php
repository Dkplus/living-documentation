<?php
declare(strict_types=1);

namespace spec\Dkplus\LivingDocumentation\SourceTree;

use Dkplus\LivingDocumentation\SourceTree\Node;
use Dkplus\LivingDocumentation\SourceTree\NodeNotFound;
use PhpSpec\ObjectBehavior;
use RuntimeException;

class NodeNotFoundSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(NodeNotFound::class);
    }

    function it_is_a_RuntimeException()
    {
        $this->shouldHaveType(RuntimeException::class);
    }

    function it_occurs_when_an_ancestor_of_a_given_type_does_not_exist(Node $origin)
    {
        $origin->name()->willReturn('File.php');
        $this->beConstructedThrough('asAncestorOfClass', ['Node\\Fqcn', $origin]);

        $this->getMessage()->shouldContain('Node\\Fqcn');
        $this->getMessage()->shouldContain('File.php');
    }
}
