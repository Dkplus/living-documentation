<?php
declare(strict_types=1);

namespace test\Dkplus\LivingDocumentation\Extension\SourceCode\Node\Visitor;

use Dkplus\LivingDocumentation\Extension\SourceCode\Node\ClassAlike;
use Dkplus\LivingDocumentation\Extension\SourceCode\Node\File;
use Dkplus\LivingDocumentation\Extension\SourceCode\Node\Package;
use Dkplus\LivingDocumentation\Extension\SourceCode\Node\Visitor\NodeCollectingVisitor;
use Dkplus\LivingDocumentation\SourceTree\FamilyTree;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use test\Dkplus\LivingDocumentation\Extension\SourceCode\Node\Visitor\Fixtures\ClassWithoutDependencies;
use function file_get_contents;

/**
 * @covers NodeCollectingVisitor
 */
class NodeCollectingVisitorTest extends TestCase
{
    /** @test */
    function it_collects_classes()
    {
        $nodes = new FamilyTreeMock();
        $this->traverseFile(new File(__DIR__ . '/Fixtures/ClassWithoutDependencies.php'), $nodes);
        $this->assertHasAdopted($nodes, function ($argument) {
            return $argument instanceof ClassAlike
                && $argument->name() === ClassWithoutDependencies::class;
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
