<?php
declare(strict_types=1);

namespace spec\Dkplus\LivingDocumentation\Extension\SourceCode\Node;

use Dkplus\LivingDocumentation\Extension\SourceCode\Node\File;
use Dkplus\LivingDocumentation\SourceTree\Node;
use PhpSpec\ObjectBehavior;

class FileSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('/path');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(File::class);
    }

    function it_is_a_Node()
    {
        $this->shouldImplement(Node::class);
    }

    function it_has_a_filePath()
    {
        $this->beConstructedWith('/full/path/to/file');
        $this->path()->shouldBe('/full/path/to/file');
    }

    function it_can_have_an_extension()
    {
        $this->beConstructedWith('/full/path/to/file.ext');
        $this->extension()->shouldBe('ext');
    }

    function it_can_have_no_extension()
    {
        $this->beConstructedWith('/full/path/to/file');
        $this->extension()->shouldBe('');
    }
}
