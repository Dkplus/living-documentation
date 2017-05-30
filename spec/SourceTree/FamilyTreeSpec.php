<?php
declare(strict_types=1);

namespace spec\Dkplus\LivingDocumentation\SourceTree;

use Dkplus\LivingDocumentation\SourceTree\Exception\IllegalRelationship;
use Dkplus\LivingDocumentation\SourceTree\Exception\NodeNotFound;
use Dkplus\LivingDocumentation\SourceTree\FamilyTree;
use Dkplus\LivingDocumentation\SourceTree\Node;
use Dkplus\LivingDocumentation\SourceTree\NodeHierarchy;
use Dkplus\LivingDocumentation\SourceTree\Project;
use PhpSpec\ObjectBehavior;

class FamilyTreeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(FamilyTree::class);
    }

    function it_is_a_NodeHierarchy()
    {
        $this->shouldImplement(NodeHierarchy::class);
    }

    function it_has_a_progenitor_node()
    {
        $this->progenitor()->shouldBeAnInstanceOf(Project::class);
    }

    function it_allows_the_adoption_of_a_Node(Node $child, Node $grandchild)
    {
        $this
            ->shouldNotThrow()
            ->during('adopt', [$this->progenitor(), $child]);

        $this
            ->shouldNotThrow()
            ->during('adopt', [$child, $grandchild]);
    }

    function it_also_allows_the_adoption_of_nodes_that_are_already_part_of_the_family_as_long_as_they_are_not_adopted_by_its_descendant(
        Node $child,
        Node $grandchild,
        Node $grandchildAndGrandGrandchild
    ) {
        $this->adopt($this->progenitor(), $child);
        $this->adopt($child, $grandchild);
        $this->adopt($child, $grandchildAndGrandGrandchild);
        $this->shouldNotThrow()->during('adopt', [$grandchild, $grandchildAndGrandGrandchild]);
    }

    function it_forbids_the_adoption_of_a_Node_that_is_an_ancestor_of_the_adoptive(Node $child, Node $grandchild)
    {
        $this->adopt($this->progenitor(), $child);
        $this->adopt($child, $grandchild);

        $this
            ->shouldThrow(IllegalRelationship::class)
            ->during('adopt', [$grandchild, $child]);
    }

    function it_forbids_the_adoption_of_a_Node_that_is_a_spouse_of_the_adoptive(Node $child, Node $stepchild)
    {
        $this->adopt($this->progenitor(), $child);
        $this->marry($child, $stepchild);

        $this
            ->shouldThrow(IllegalRelationship::class)
            ->during('adopt', [$stepchild, $child]);
    }

    function it_forbids_the_adoption_of_a_Node_by_a_Node_that_is_no_family_member(Node $unknown, Node $adopted)
    {
        $this
            ->shouldThrow(NodeNotFound::class)
            ->during('adopt', [$unknown, $adopted]);
    }

    function it_marries_nodes(Node $progenita)
    {
        $this->shouldNotThrow()->during('marry', [$this->progenitor(), $progenita]);
    }

    function it_allows_polygamy_between_nodes(
        Node $firstChild,
        Node $secondChild,
        Node $partnerOfBoth,
        Node $secondPartner
    ) {
        $this->adopt($this->progenitor(), $firstChild);
        $this->adopt($this->progenitor(), $secondChild);
        $this->marry($firstChild, $partnerOfBoth);
        $this->shouldNotThrow()->during('marry', [$secondChild, $partnerOfBoth]);
        $this->shouldNotThrow()->during('marry', [$secondChild, $secondPartner]);
    }

    function it_forbids_the_marriage_of_a_first_Node_that_is_not_part_of_the_family(Node $child, Node $unknown)
    {
        $this->adopt($this->progenitor(), $child);
        $this->shouldThrow(NodeNotFound::class)->during('marry', [$unknown, $child]);
    }

    function it_forbids_marriage_so_that_one_Node_would_be_an_ancestor_of_itself(Node $child, Node $grandchild)
    {
        $this->adopt($this->progenitor(), $child);
        $this->adopt($child, $grandchild);
        $this->shouldThrow(IllegalRelationship::class)->during('marry', [$grandchild, $child]);
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
