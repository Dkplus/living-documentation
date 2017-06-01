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
        $this->beConstructedWith('My\\Stdlib\\stdClass', 'My\\Stdlib', [], [], []);
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
        $this->name()->shouldBe('My\\Stdlib\\stdClass');
    }

    function it_has_a_package()
    {
        $this->package()->shouldBe('My\\Stdlib');
    }

    public function it_may_implement_some_interfaces()
    {
        $this->beConstructedWith('className', 'packageName', ['OneInterface']);
        $this->implementations()->shouldBe(['OneInterface']);
    }

    public function it_may_extend_other_classes_or_interfaces()
    {
        $this->beConstructedWith('className', 'packageName', [], ['Foo\\Bar']);
        $this->extensions()->shouldBe(['Foo\\Bar']);
    }

    public function it_may_has_associations_to_other_classes()
    {
        $this->beConstructedWith('className', 'packageName', [], [], ['Foo\\Bar']);
        $this->associations()->shouldBe(['Foo\\Bar']);
    }
}
