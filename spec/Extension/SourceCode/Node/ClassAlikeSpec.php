<?php
declare(strict_types=1);

namespace spec\Dkplus\LivingDocumentation\Extension\SourceCode\Node;

use Dkplus\LivingDocumentation\Extension\SourceCode\Node\ClassAlike;
use Dkplus\LivingDocumentation\SourceTree\Node;
use PhpSpec\ObjectBehavior;

class ClassAlikeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('stdClass');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ClassAlike::class);
    }

    function it_is_a_Node()
    {
        $this->shouldImplement(Node::class);
    }

    function it_has_a_name()
    {
        $this->name()->shouldBe('stdClass');
    }
}
