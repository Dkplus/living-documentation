<?php
declare(strict_types=1);

namespace spec\Dkplus\LivingDocumentation\Extension\SourceCode\Node;

use Dkplus\LivingDocumentation\Extension\SourceCode\Node\Method;
use PhpSpec\ObjectBehavior;

class MethodSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(Method::class, 'className');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Method::class);
    }

    function it_has_a_className()
    {
        $this->className()->shouldBe(Method::class);
    }

    function it_has_a_name()
    {
        $this->name()->shouldBe('className');
    }
}
