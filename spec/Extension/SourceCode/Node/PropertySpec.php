<?php
declare(strict_types=1);

namespace spec\Dkplus\LivingDocumentation\Extension\SourceCode\Node;

use Dkplus\LivingDocumentation\Extension\SourceCode\Node\Property;
use Dkplus\LivingDocumentation\SourceTree\Node;
use PhpSpec\ObjectBehavior;

class PropertySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(Property::class, 'className');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Property::class);
    }

    function it_is_a_Node()
    {
        $this->shouldImplement(Node::class);
    }

    function it_has_a_className()
    {
        $this->className()->shouldBe(Property::class);
    }

    function it_has_a_name()
    {
        $this->name()->shouldBe('className');
    }
}
