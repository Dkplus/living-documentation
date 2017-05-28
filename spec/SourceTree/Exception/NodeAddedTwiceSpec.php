<?php
declare(strict_types=1);

namespace spec\Dkplus\LivingDocumentation\SourceTree\Exception;

use Dkplus\LivingDocumentation\SourceTree\Exception\NodeAddedTwice;
use Dkplus\LivingDocumentation\SourceTree\Node;
use PhpSpec\ObjectBehavior;
use RuntimeException;

class NodeAddedTwiceSpec extends ObjectBehavior
{
    function let(Node $node)
    {
        $node->name()->willReturn('Project');
        $this->beConstructedWith($node);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(NodeAddedTwice::class);
    }

    function it_is_a_RuntimeException()
    {
        $this->shouldHaveType(RuntimeException::class);
    }

    function it_relates_to_a_Node(Node $node)
    {
        $this->relatedTo()->shouldBe($node);
    }

    function its_message_contains_the_name_of_the_related_note(Node $node)
    {
        $node->name()->willReturn('Project');
        $this->getMessage()->shouldContain('Project');
    }
}
