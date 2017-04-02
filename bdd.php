<?php

namespace Application;

require_once __DIR__ . '/vendor/autoload.php';

use Behat\Gherkin\Gherkin;
use Behat\Gherkin\Keywords\ArrayKeywords;
use Behat\Gherkin\Lexer;
use Behat\Gherkin\Loader\DirectoryLoader;
use Behat\Gherkin\Loader\GherkinFileLoader;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Parser;
use Dkplus\LivingDocs\Gherkin\Renderer;
use function dirname;
use function file_put_contents;

$keywords = new ArrayKeywords(include __DIR__ . '/vendor/behat/gherkin/i18n.php');
$parser = new Parser(new Lexer($keywords));

$gherkin = new Gherkin();
$directoryLoader = new DirectoryLoader($gherkin);
$gherkin->addLoader($directoryLoader);
$gherkin->addLoader(new GherkinFileLoader($parser));

$templatePath = __DIR__ . '/resources/templates';
$sourcePath = __DIR__ . '/example-specs';
$targetPath = __DIR__ . '/build';

$renderer = new Renderer($templatePath, $sourcePath);
/* @var FeatureNode[] $features */
$features = $gherkin->load($sourcePath);
$renderedPages = $renderer($features);
foreach ($renderedPages as $each) {
    $eachTarget = $targetPath . '/' . $each->getLink();
    if (! is_dir(dirname($eachTarget))) {
        mkdir(dirname($eachTarget), 0777, true);
    }
    file_put_contents($targetPath . '/' . $each->getLink(), $each->getContent());
}
