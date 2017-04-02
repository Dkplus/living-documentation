<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Gherkin;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\OutlineNode;
use Behat\Gherkin\Node\ScenarioInterface;
use Behat\Gherkin\Node\ScenarioNode;
use Dkplus\LivingDocs\Rendering\SinglePage;
use Exception;
use Michelf\Markdown;
use Twig_Environment;
use Twig_Filter;
use Twig_Function;
use Twig_Loader_Filesystem;
use function array_column;
use function array_map;
use function basename;
use function dirname;
use function get_class;
use function realpath;
use function str_replace;
use function strtolower;
use function urldecode;
use function var_dump;

final class Renderer
{
    /** @var Twig_Environment */
    private $twig;

    /** @var string */
    private $baseDirectory;

    public function __construct(string $templateDirectory, string $baseDirectory)
    {
        $this->twig = new Twig_Environment(new Twig_Loader_Filesystem([$templateDirectory]));
        $this->twig->addFilter(new Twig_Filter('markdown', function (string $content) {
            return Markdown::defaultTransform($content);
        }));
        $this->twig->addFilter(new Twig_Filter('slugifyFeature', function (FeatureNode $feature) {
            return $this->slugify(substr(basename($feature->getFile()), 0, -1 * strlen('.feature')));
        }));
        $this->twig->addFilter(new Twig_Filter('anchor', function (string $link) {
            return mb_substr($link, mb_strpos($link, '#'));
        }));
        $this->twig->addFunction(new Twig_Function('dump', function ($var) {
            var_dump($var);
        }));
        $this->baseDirectory = realpath($baseDirectory);
    }

    /**
     * @param FeatureNode[] $features
     * @return SinglePage[]
     */
    public function __invoke(array $features): array
    {
        $menu = $this->createLinks($features);

        $featuresByParentDirectory = [];
        foreach ($features as $each) {
            $featuresByParentDirectory[dirname($each->getFile())][] = $each;
        }
        $pages = [];
        foreach ($featuresByParentDirectory as $eachDir => $each) {
            $featuresAsStrings = array_map([$this, 'renderFeature'], $features);
            $pageLink = 'features' . substr($eachDir, strlen($this->baseDirectory)) . '.html';
            $pageMenu = $this->findCurrentPage($menu, $pageLink);
            $content = $this->twig->render(
                'feature-page.html.twig',
                ['features' => $featuresAsStrings, 'menu' => $menu, 'pageMenu' => $pageMenu]
            );
            $pages[] = new SinglePage($pageLink, $content);
        }
        return $pages;
    }

    private function findCurrentPage(array $menu, string $link): array
    {
        foreach ($menu as $each) {
            if (isset($each['link']) && $each['link'] === $link) {
                return $each['children'];
            }
            $subResult = $this->findCurrentPage($each['children'], $link);
            if (count($subResult) > 0) {
                return $subResult;
            }
        }
        return [];
    }

    private function renderFeature(FeatureNode $feature): string
    {
        $scenarios = array_map([$this, 'renderScenarioInterface'], $feature->getScenarios());
        return $this->twig->render('feature.html.twig', ['feature' => $feature, 'scenarios' => $scenarios]);
    }

    private function renderScenarioInterface(ScenarioInterface $scenario): string
    {
        if ($scenario instanceof ScenarioNode) {
            return $this->renderScenario($scenario);
        }
        if ($scenario instanceof OutlineNode) {
            return $this->renderOutline($scenario);
        }
        throw new Exception(get_class($scenario));
    }

    private function renderScenario(ScenarioNode $scenario): string
    {
        return $this->twig->render('scenario.html.twig', ['scenario' => $scenario]);
    }

    private function renderOutline(OutlineNode $outline): string
    {
        return $this->twig->render('outline.html.twig', ['outline' => $outline]);
    }

    private function createLinks(array $features): array
    {
        $links = [];
        foreach ($features as $each) {
            $relativeFilePathWithoutExtension = mb_substr(
                $each->getFile(),
                mb_strlen($this->baseDirectory) + 1,
                -1 * strlen('.feature')
            );
            $path = dirname($relativeFilePathWithoutExtension);
            $links[$path][] = [
                'label' => basename($relativeFilePathWithoutExtension),
                'path' => $path,
                'level' => 1,
                'link' => 'features/' . $this->slugify($path) . '.html#' . $this->slugify(basename($relativeFilePathWithoutExtension)),
                'children' => [],
            ];
        }
        $hasChanged = true;
        while ($hasChanged) {
            $hasChanged = false;
            foreach ($links as $key => $linksWithSamePath) {
                $path = current($linksWithSamePath)['path'];
                if ($path !== '.') {
                    $hasChanged = true;
                    unset($links[$key]);
                    $level = min(array_column($linksWithSamePath, 'level')) + 1;
                    $item = [
                        'label' => basename($path),
                        'path' => dirname($path),
                        'level' => $level,
                        'children' => $linksWithSamePath,
                    ];
                    if ($level === 2) {
                        $item['link'] = 'features/' . $this->slugify($path) . '.html';
                    }
                    $links[dirname($path)][] = $item;
                }
            }
        }
        return $links['.'];
    }

    private function slugify(string $value)
    {
        return urldecode(str_replace('', '-', strtolower($value)));
    }
}
