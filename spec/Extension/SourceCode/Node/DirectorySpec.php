<?php
declare(strict_types=1);

namespace spec\Dkplus\LivingDocumentation\Extension\SourceCode\Node;

use Dkplus\LivingDocumentation\Extension\SourceCode\Node\Directory;
use Dkplus\LivingDocumentation\SourceTree\Node;
use Dkplus\LivingDocumentation\SourceTree\NodeHierarchy;
use PhpSpec\ObjectBehavior;

class DirectorySpec extends ObjectBehavior
{
    function let(NodeHierarchy $nodes)
    {
        $this->beConstructedWith('/path', $nodes);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Directory::class);
    }

    function it_is_a_Node()
    {
        $this->shouldImplement(Node::class);
    }

    function it_has_a_filePath(NodeHierarchy $nodes)
    {
        $this->beConstructedWith('/full/path/to/directory', $nodes);
        $this->path()->shouldBe('/full/path/to/directory');
    }
}
