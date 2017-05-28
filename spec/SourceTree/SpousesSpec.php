<?php
declare(strict_types=1);

namespace spec\Dkplus\LivingDocumentation\SourceTree;

use Dkplus\LivingDocumentation\SourceTree\Node;
use Dkplus\LivingDocumentation\SourceTree\Nodes;
use Dkplus\LivingDocumentation\SourceTree\Spouses;
use PhpSpec\ObjectBehavior;

class SpousesSpec extends ObjectBehavior
{
    function let(Node $founder)
    {
        $this->beConstructedThrough('progenitor', [$founder]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Spouses::class);
    }

    function it_allows_polygamy(Node $firstPartner, Node $secondPartner)
    {
        $this->shouldNotThrow()->during('marry', [$firstPartner]);
        $this->shouldNotThrow()->during('marry', [$secondPartner]);
    }

    function it_provides_all_spouses(Node $founder, Node $partner)
    {
        $this->marry($partner);

        $this->all()->shouldBeAnInstanceOf(Nodes::class);
        $this->all()->shouldIterateAs([$founder, $partner]);
    }

    function its_ancestors_are_up_to_the_progenitor(Spouses $parents, Spouses $progenitor, Node $node)
    {
        $parents->ancestors()->willReturn([$progenitor]);
        $this->beConstructedThrough('adopted', [$parents, $node]);
        $this->ancestors()->shouldBe([$parents, $progenitor]);
    }

    function it_has_no_ancestors_if_it_is_the_progenitor(Node $founder)
    {
        $this->beConstructedThrough('progenitor', [$founder]);

        $this->ancestors()->shouldBe([]);
    }

    function it_adopts_other_nodes(Node $child)
    {
        $this->adopt($child)->shouldBeAnInstanceOf(Spouses::class);
    }

    function its_children_are_just_the_direct_descendants(Node $childNode, Node $anotherChildNode, Node $grandchildNode)
    {
        $child = $this->adopt($childNode);
        $anotherChild = $this->adopt($anotherChildNode);
        $child->adopt($grandchildNode);

        $this->children()->shouldBe([$child, $anotherChild]);
    }

    function its_descendants_are_up_to_the_newest_spouses(Node $childNode, Node $anotherChildNode, Node $grandchildNode)
    {
        $child = $this->adopt($childNode);
        $anotherChild = $this->adopt($anotherChildNode);
        $grandchild = $child->adopt($grandchildNode);

        $this->descendants()->shouldBeLike([$child, $anotherChild, $grandchild]);
    }
}
