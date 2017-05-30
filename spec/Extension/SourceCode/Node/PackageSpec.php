<?php
declare(strict_types=1);

namespace spec\Dkplus\LivingDocumentation\Extension\SourceCode\Node;

use Dkplus\LivingDocumentation\Extension\SourceCode\Node\Package;
use Dkplus\LivingDocumentation\SourceTree\Node;
use PhpSpec\ObjectBehavior;

class PackageSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('My\\Package');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Package::class);
    }

    function it_is_a_Node()
    {
        $this->shouldImplement(Node::class);
    }

    function it_has_a_name()
    {
        $this->name()->shouldBe('My\\Package');
    }
}
