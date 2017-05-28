<?php
declare(strict_types=1);

namespace spec\Dkplus\LivingDocumentation\SourceTree;

use Dkplus\LivingDocumentation\SourceTree\Exception\NodeAddedTwice;
use Dkplus\LivingDocumentation\SourceTree\Exception\NodeNotFound;
use Dkplus\LivingDocumentation\SourceTree\FamilyTree;
use Dkplus\LivingDocumentation\SourceTree\Node;
use Dkplus\LivingDocumentation\SourceTree\Project;
use PhpSpec\ObjectBehavior;

class FamilyTreeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(FamilyTree::class);
    }

    function it_has_a_progenitor_node()
    {
        $this->progenitor()->shouldBeAnInstanceOf(Project::class);
    }

    function it_allows_the_adoption_of_a_Node_that_is_not_part_of_the_family_tree_by_a_Node_that_is(
        Node $child,
        Node $unknown
    ) {
        $child->name()->willReturn('Child');
        $unknown->name()->willReturn('Not part of the family tree');

        $this
            ->shouldThrow(new NodeNotFound($unknown->getWrappedObject()))
            ->during('adopt', [$unknown, $child]);

        $this
            ->shouldNotThrow()
            ->during('adopt', [$this->progenitor(), $child]);

        $this
            ->shouldThrow(new NodeAddedTwice($child->getWrappedObject()))
            ->during('adopt', [$this->progenitor(), $child]);
    }

    function it_marries_a_Node_that_is_part_of_a_family_tree_with_another_Node_that_is_not(
        Node $progenitorPartner,
        Node $son,
        Node $stepdaughter,
        Node $unknown
    ) {
        $stepdaughter->name()->willReturn('Will be added twice');
        $unknown->name()->willReturn('Not part of the family tree');

        $this
            ->shouldNotThrow()
            ->during('marry', [$this->progenitor(), $progenitorPartner]);

        $this
            ->shouldThrow(new NodeNotFound($unknown->getWrappedObject()))
            ->during('marry', [$unknown, $son]);

        $this->adopt($this->progenitor(), $son);

        $this
            ->shouldNotThrow()
            ->during('marry', [$son, $stepdaughter]);

        $this
            ->shouldThrow(new NodeAddedTwice($stepdaughter->getWrappedObject()))
            ->during('marry', [$son, $stepdaughter]);
    }

    function it_finds_descendants_of_a_Node(Node $son, Node $stepdaughter, Node $grandchild)
    {
        $this->adopt($this->progenitor(), $son);
        $this->marry($son, $stepdaughter);
        $this->adopt($son, $grandchild);

        $this->findDescendantsOf($this->progenitor())->shouldIterateAs([$son, $stepdaughter, $grandchild]);
        $this->findDescendantsOf($son)->shouldIterateAs([$grandchild]);
        $this->findDescendantsOf($stepdaughter)->shouldIterateAs([$grandchild]);
        $this->findDescendantsOf($grandchild)->shouldIterateAs([]);
    }

    function it_find_ancestors_of_a_Node(Node $son, Node $stepdaughter, Node $grandchild)
    {
        $this->adopt($this->progenitor(), $son);
        $this->marry($son, $stepdaughter);
        $this->adopt($son, $grandchild);

        $this->findAncestorsOf($grandchild)->shouldIterateAs([$son, $stepdaughter, $this->progenitor()]);
        $this->findAncestorsOf($son)->shouldIterateAs([$this->progenitor()]);
        $this->findAncestorsOf($stepdaughter)->shouldIterateAs([$this->progenitor()]);
        $this->findAncestorsOf($this->progenitor())->shouldIterateAs([]);
    }

    function it_finds_spouses_of_a_Node(Node $son, Node $stepdaughter, Node $grandchild, Node $stepson)
    {
        $this->adopt($this->progenitor(), $son);
        $this->marry($son, $stepdaughter);
        $this->marry($stepdaughter, $stepson);
        $this->adopt($son, $grandchild);

        $this->findSpousesOf($this->progenitor())->shouldIterateAs([]);
        $this->findSpousesOf($stepdaughter)->shouldIterateAs([$son, $stepson]);
        $this->findSpousesOf($son)->shouldIterateAs([$stepdaughter, $stepson]);
        $this->findSpousesOf($stepson)->shouldIterateAs([$son, $stepdaughter]);
        $this->findSpousesOf($grandchild)->shouldIterateAs([]);
    }
}
