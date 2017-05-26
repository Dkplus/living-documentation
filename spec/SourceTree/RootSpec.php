<?php
declare(strict_types=1);

namespace spec\Dkplus\LivingDocumentation\SourceTree;

use Dkplus\LivingDocumentation\SourceTree\Node;
use Dkplus\LivingDocumentation\SourceTree\NodeNotFound;
use Dkplus\LivingDocumentation\SourceTree\Root;
use PhpSpec\ObjectBehavior;

class RootSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('/path/to/a/dir');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Root::class);
    }

    function it_is_a_Node()
    {
        $this->shouldImplement(Node::class);
    }

    function it_cannot_be_the_descendant_of_another_node(Node $node)
    {
        $this->isDescendantOf($node)->shouldBe(false);
    }

    function its_filePath_is_passed_as_constructor_argument()
    {
        $this->filePath()->shouldBe('/path/to/a/dir');
    }

    function its_name_corresponds_to_its_filePath()
    {
        $this->name()->shouldImplement('/path/to/a/dir');
    }

    function it_cannot_find_an_ancestor()
    {
        $this
            ->shouldThrow(NodeNotFound::asAncestorOfClass(Node::class, $this->getWrappedObject()))
            ->during('findAncestorOfClass', [Node::class]);
    }
}
