<?php
declare(strict_types=1);

namespace test\Dkplus\LivingDocumentation\Extension\SourceCode\Node\Visitor;

use Dkplus\LivingDocumentation\Extension\SourceCode\Node\ClassAlike;
use Dkplus\LivingDocumentation\Extension\SourceCode\Node\File;
use Dkplus\LivingDocumentation\Extension\SourceCode\Node\Package;
use Dkplus\LivingDocumentation\Extension\SourceCode\Node\Visitor\NodeCollectingVisitor;
use Dkplus\LivingDocumentation\SourceTree\FamilyTree;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use test\Dkplus\LivingDocumentation\Extension\SourceCode\Node\Visitor\Fixtures\ClassWithMultipleProperties;
use test\Dkplus\LivingDocumentation\Extension\SourceCode\Node\Visitor\Fixtures\ClassWithoutDependencies;
use test\Dkplus\LivingDocumentation\Extension\SourceCode\Node\Visitor\Fixtures\InterfaceExtendingTwoInterfaces;
use test\Dkplus\LivingDocumentation\Extension\SourceCode\Node\Visitor\Fixtures\OneInterface;
use test\Dkplus\LivingDocumentation\Extension\SourceCode\Node\Visitor\Fixtures\OneTrait;
use function file_get_contents;

/**
 * @covers \Dkplus\LivingDocumentation\Extension\SourceCode\Node\Visitor\NodeCollectingVisitor
 * @covers \Dkplus\LivingDocumentation\Extension\SourceCode\Node\Visitor\MethodDependencyVisitor
 * @covers \Dkplus\LivingDocumentation\Extension\SourceCode\Node\Visitor\TraitUsageVisitor
 */
class NodeCollectingVisitorTest extends TestCase
{
    /** @test */
    function it_collects_classes_as_ClassAlike()
    {
        $nodes = new FamilyTreeMock();
        $this->traverseFile(new File(__DIR__ . '/Fixtures/ClassWithoutDependencies.php'), $nodes);
        $this->assertHasAdopted($nodes, function ($argument) {
            return $argument instanceof ClassAlike
                && $argument->name() === ClassWithoutDependencies::class;
        });
    }

    /** @test */
    function it_collects_interfaces_as_ClassAlike()
    {
        $nodes = new FamilyTreeMock();
        $this->traverseFile(new File(__DIR__ . '/Fixtures/OneInterface.php'), $nodes);
        $this->assertHasAdopted($nodes, function ($argument) {
            return $argument instanceof ClassAlike
                && $argument->name() === OneInterface::class;
        });
    }

    /** @test */
    function it_collects_traits_as_ClassAlike()
    {
        $nodes = new FamilyTreeMock();
        $this->traverseFile(new File(__DIR__ . '/Fixtures/OneTrait.php'), $nodes);
        $this->assertHasAdopted($nodes, function ($argument) {
            return $argument instanceof ClassAlike
                && $argument->name() === OneTrait::class;
        });
    }

    /** @test */
    function it_computes_the_implemented_interfaces_of_each_ClassAlike()
    {
        $nodes = new FamilyTreeMock();
        $this->traverseFile(new File(__DIR__ . '/Fixtures/ClassImplementingTwoInterfaces.php'), $nodes);
        $this->assertHasAdopted($nodes, function ($argument) {
            return $argument instanceof ClassAlike
                && $argument->implementations() === ['Traversable', 'Serializable'];
        });
    }

    /** @test */
    function it_computes_the_extended_classes_of_each_class()
    {
        $nodes = new FamilyTreeMock();
        $this->traverseFile(new File(__DIR__ . '/Fixtures/ClassExtendingAnotherClass.php'), $nodes);
        $this->assertHasAdopted($nodes, function ($argument) {
            return $argument instanceof ClassAlike
                && $argument->extensions() === ['stdClass', OneTrait::class];
        });
    }

    /** @test */
    function it_computes_the_extended_classes_of_each_interface()
    {
        $nodes = new FamilyTreeMock();
        $this->traverseFile(new File(__DIR__ . '/Fixtures/InterfaceExtendingTwoInterfaces.php'), $nodes);
        $this->assertHasAdopted($nodes, function ($argument) {
            return $argument instanceof ClassAlike
                && $argument->extensions() === [OneInterface::class, 'AnotherInterface'];
        });
    }

    /** @test */
    function it_recognizes_properties_of_ClassAlikes_as_associations()
    {
        $nodes = new FamilyTreeMock();
        $this->traverseFile(new File(__DIR__ . '/Fixtures/ClassWithMultipleProperties.php'), $nodes);
        $this->assertHasAdopted($nodes, function ($argument) {
            return $argument instanceof ClassAlike
                && $argument->associations() === [
                    OneInterface::class,
                    OneTrait::class,
                    InterfaceExtendingTwoInterfaces::class,
                    ClassWithMultipleProperties::class,
                    Node::class
                ];
        });
    }

    /** @test */
    function it_let_each_file_adopt_the_classes_it_contains()
    {
        $nodes = new FamilyTreeMock();
        $file = new File(__DIR__ . '/Fixtures/ClassWithoutDependencies.php');

        $this->traverseFile($file, $nodes);

        $this->assertHasAdopted($nodes, $file, function ($node) {
            return $node instanceof ClassAlike
                && $node->name() === ClassWithoutDependencies::class;
        });
    }

    /** @test */
    function it_collects_packages_as_direct_children_of_the_project()
    {
        $nodes = new FamilyTreeMock();
        $this->traverseFile(new File(__DIR__ . '/Fixtures/ClassWithoutExplicitPackageDeclaration.php'), $nodes);

        $this->assertHasAdopted($nodes, $nodes->progenitor(), function ($node) {
            return $node instanceof Package;
        });
    }

    /** @test */
    function it_uses_the_namespace_as_package_name_if_no_package_annotation_is_available()
    {
        $nodes = new FamilyTreeMock();
        $this->traverseFile(new File(__DIR__ . '/Fixtures/ClassWithoutExplicitPackageDeclaration.php'), $nodes);

        $this->assertHasAdopted($nodes, $nodes->progenitor(), function ($node) {
            return $node instanceof Package
                && $node->name() === 'test\Dkplus\LivingDocumentation\Extension\SourceCode\Node\Visitor\Fixtures';
        });
    }

    /** @test */
    function it_uses_a_package_annotation_if_such_exist()
    {
        $nodes = new FamilyTreeMock();
        $this->traverseFile(new File(__DIR__ . '/Fixtures/ClassWithExplicitPackageDeclaration.php'), $nodes);

        $this->assertHasAdopted($nodes, $nodes->progenitor(), function ($node) {
            return $node instanceof Package
                && $node->name() === 'Fixtures';
        });
    }

    /** @test */
    function it_also_uses_a_subpackage_annotation_if_such_exist()
    {
        $nodes = new FamilyTreeMock();
        $this->traverseFile(new File(__DIR__ . '/Fixtures/ClassWithExplicitSubpackageDeclaration.php'), $nodes);

        $this->assertHasAdopted($nodes, $nodes->progenitor(), function ($node) {
            return $node instanceof Package
                && $node->name() === 'Fixtures\\Subpackage';
        });
    }

    /** @test */
    function it_let_each_package_adopt_its_subpackages()
    {
        $nodes = new FamilyTreeMock();
        $this->traverseFiles([
            new File(__DIR__ . '/Fixtures/ClassWithExplicitPackageDeclaration.php'),
            new File(__DIR__ . '/Fixtures/ClassWithExplicitSubpackageDeclaration.php'),
        ], $nodes);

        $this->assertHasAdopted($nodes, function ($node) {
            return $node instanceof Package
                && $node->name() === 'Fixtures';
        }, function ($node) {
            return $node instanceof Package
                && $node->name() === 'Fixtures\\Subpackage';
        });
    }

    private function traverseFile(File $file, FamilyTree $nodes): NodeCollectingVisitor
    {
        return $this->traverseFiles([$file], $nodes);
    }

    /** @param File[] $files */
    private function traverseFiles(array $files, FamilyTree $nodes): NodeCollectingVisitor
    {
        $tested = new NodeCollectingVisitor($nodes);

        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor($tested);

        foreach ($files as $each) {
            $tested->beforeFile($each);
            $traverser->traverse($parser->parse(file_get_contents($each->path())));
            $tested->afterFile();
        }

        return $tested;
    }

    private function assertHasAdopted(FamilyTreeMock $familyTree, $adoptiveOrAdoptedFilter, $adoptedFilter = null)
    {
        if (! $adoptedFilter) {
            $adoptedFilter = $adoptiveOrAdoptedFilter;
            $adoptiveOrAdoptedFilter = null;
        }
        $this->assertTrue($familyTree->hasAdopted($adoptedFilter, $adoptiveOrAdoptedFilter));
    }
}
