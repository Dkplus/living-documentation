<?php
declare(strict_types=1);

namespace spec\Dkplus\LivingDocumentation\SourceTree\Exception;

use Dkplus\LivingDocumentation\SourceTree\Exception\IllegalRelationship;
use Dkplus\LivingDocumentation\SourceTree\Node;
use PhpSpec\ObjectBehavior;
use RuntimeException;

class IllegalRelationshipSpec extends ObjectBehavior
{
    function let(Node $node, Node $secondNode)
    {
        $this->beConstructedWith($node, $secondNode);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IllegalRelationship::class);
    }

    function it_is_a_RuntimeException()
    {
        $this->shouldHaveType(RuntimeException::class);
    }

    function it_involves_two_Nodes(Node $node, Node $secondNode)
    {
        $this->firstInvolved()->shouldBe($node);
        $this->secondInvolved()->shouldBe($secondNode);
    }
}
