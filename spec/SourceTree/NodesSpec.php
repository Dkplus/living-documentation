<?php
declare(strict_types=1);

namespace spec\Dkplus\LivingDocumentation\SourceTree;

use Dkplus\LivingDocumentation\SourceTree\Exception\NodeNotFound;
use Dkplus\LivingDocumentation\SourceTree\Node;
use Dkplus\LivingDocumentation\SourceTree\Nodes;
use Iterator;
use PhpSpec\ObjectBehavior;

/**
 * @method shouldIterateAs($iterable)
 */
class NodesSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Nodes::class);
    }

    function it_is_an_iterator()
    {
        $this->shouldImplement(Iterator::class);
    }

    function it_iterates_over_Nodes(Node $first, Node $second, Node $third)
    {
        $this->beConstructedWith($first, $second, $third);
        $this->shouldIterateAs([$first, $second, $third]);
    }

    function it_knows_whether_it_contains_a_Node(Node $contained, Node $notContained)
    {
        $this->beConstructedWith($contained);

        $this->contains($contained)->shouldBe(true);
        $this->contains($notContained)->shouldBe(false);
    }

    function it_can_create_new_instances_without_a_specific_Node(Node $contained, Node $notContained)
    {
        $notContained->name()->willReturn('Node is not contained');

        $this->beConstructedWith($contained);

        $this->without($contained)->shouldNotBe($this);
        $this->without($contained)->shouldIterateAs([]);

        $this->shouldThrow(NodeNotFound::class)->during('without', [$notContained]);
    }

    function it_can_create_new_instances_with_filtered_Nodes(Node $nodeA, Node $nodeB)
    {
        $nodeA->name()->willReturn('A');
        $nodeB->name()->willReturn('B');

        $this->beConstructedWith($nodeA, $nodeB);

        $filter = function (Node $node) {
            return $node->name() === 'B';
        };

        $this->filter($filter)->shouldNotBe($this);
        $this->filter($filter)->shouldBeAnInstanceOf(Nodes::class);
        $this->filter($filter)->shouldIterateAs([$nodeB]);
    }
}
