<?php
declare(strict_types=1);

namespace spec\Dkplus\LivingDocumentation\SourceTree\Exception;

use Dkplus\LivingDocumentation\SourceTree\Exception\NodeNotFound;
use Dkplus\LivingDocumentation\SourceTree\Node;
use PhpSpec\ObjectBehavior;
use RuntimeException;

class NodeNotFoundSpec extends ObjectBehavior
{
    function let(Node $node)
    {
        $this->beConstructedWith($node);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(NodeNotFound::class);
    }

    function it_is_a_RuntimeException()
    {
        $this->shouldHaveType(RuntimeException::class);
    }

    function it_relates_to_a_Node(Node $node)
    {
        $this->relatedTo()->shouldBe($node);
    }
}
