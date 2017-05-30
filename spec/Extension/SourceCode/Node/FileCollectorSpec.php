<?php
declare(strict_types=1);

namespace spec\Dkplus\LivingDocumentation\Extension\SourceCode\Node;

use Dkplus\LivingDocumentation\Extension\SourceCode\Node\Directory;
use Dkplus\LivingDocumentation\Extension\SourceCode\Node\File;
use Dkplus\LivingDocumentation\Extension\SourceCode\Node\FileCollector;
use Dkplus\LivingDocumentation\SourceTree\FamilyTree;
use Dkplus\LivingDocumentation\SourceTree\NodeCollector;
use Dkplus\LivingDocumentation\SourceTree\Project;
use PhpSpec\ObjectBehavior;

class FileCollectorSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([__FILE__]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FileCollector::class);
    }

    function it_is_a_NodeCollector()
    {
        $this->shouldImplement(NodeCollector::class);
    }

    function it_add_Nodes_for_all_directories_and_files_in_the_given_paths(FamilyTree $nodes)
    {
        $project = new Project($nodes->getWrappedObject());
        $this->beConstructedWith([__DIR__ . '/Fixtures', __FILE__]);
        $this->collectNodes($project, $nodes);

        $thisFile = new File(__FILE__);
        $fixturesDir = new Directory(__DIR__ . '/Fixtures');
        $firstSubDir = new Directory(__DIR__ . '/Fixtures/SubDir');
        $secondSubDir = new Directory(__DIR__ . '/Fixtures/SubDir/SubDir');
        $firstClass = new File(__DIR__ . '/Fixtures/OneClass.php');
        $secondClass = new File(__DIR__ . '/Fixtures/SubDir/SubDir/OneClass.php');

        $nodes->adopt($project, $thisFile)->shouldHaveBeenCalled();
        $nodes->adopt($project, $fixturesDir)->shouldHaveBeenCalled();
        $nodes->adopt($fixturesDir, $firstSubDir)->shouldHaveBeenCalled();
        $nodes->adopt($firstSubDir, $secondSubDir)->shouldHaveBeenCalled();
        $nodes->adopt($fixturesDir, $firstClass)->shouldHaveBeenCalled();
        $nodes->adopt($secondSubDir, $secondClass)->shouldHaveBeenCalled();
    }
}
