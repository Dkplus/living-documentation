<?php
declare(strict_types=1);

namespace spec\Dkplus\LivingDocumentation\Extension\SourceCode\Node;

use Dkplus\LivingDocumentation\Extension\SourceCode\Node\ClassAlike;
use Dkplus\LivingDocumentation\Extension\SourceCode\Node\File;
use Dkplus\LivingDocumentation\Extension\SourceCode\Node\PhpCollector;
use Dkplus\LivingDocumentation\SourceTree\FamilyTree;
use Dkplus\LivingDocumentation\SourceTree\NodeCollector;
use Dkplus\LivingDocumentation\SourceTree\Nodes;
use PhpSpec\ObjectBehavior;
use spec\Dkplus\LivingDocumentation\Extension\SourceCode\Node\Fixtures\OneClass;

class PhpCollectorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PhpCollector::class);
    }

    function it_is_a_NodeCollector()
    {
        $this->shouldImplement(NodeCollector::class);
    }

    function it_collects_ClassAlikes_for_all_php_Files(FamilyTree $nodes)
    {
        $file = new File(__DIR__ . '/Fixtures/OneClass.php');
        $nodes->findAncestorsOf($nodes->progenitor())->willReturn(new Nodes($file));
        $this->collectNodes($file, $nodes);

        $nodes->adopt($file, new ClassAlike(OneClass::class))->shouldHaveBeenCalled();
    }
}
