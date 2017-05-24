<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Extension;

use Dkplus\LivingDocs\ClassDiagramExtension\ClassDiagramExtension;
use Dkplus\LivingDocs\ExportExtension\ExportExtension;
use Dkplus\LivingDocs\GlossaryExtension\GlossaryExtension;
use Dkplus\LivingDocs\MarkdownExtension\MarkdownExtension;
use Dkplus\LivingDocs\PagesExtension\PagesExtension;
use Dkplus\LivingDocs\SightseeingExtension\SightseeingExtension;
use Dkplus\LivingDocs\SourceCodeExtension\SourceCodeExtension;
use Dkplus\LivingDocs\TwigExtension\TwigExtension;
use const DIRECTORY_SEPARATOR;
use function getcwd;

class ApplicationFactory
{
    public function createApplication(): Application
    {
        $configLoader = new ConfigurationLoader($this->getFallbackConfigPaths());
        $extensionManager = new ExtensionManager($this->createDefaultExtensions());
        return new Application('dkplus', '0.1', $configLoader, $extensionManager);
    }

    private function getFallbackConfigPaths(): array
    {
        $cwd = rtrim(getcwd(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $docsDir = $cwd . 'docs' . DIRECTORY_SEPARATOR;
        $resourceDocsDir = $cwd . 'resources' . DIRECTORY_SEPARATOR . 'docs';
        $configDir = $cwd . 'config' . DIRECTORY_SEPARATOR;
        $resourceConfigDir = $cwd . 'resources' . DIRECTORY_SEPARATOR . 'config';

        return [
            $cwd . 'dkplus.yml',
            $docsDir . 'dkplus.yml',
            $configDir . 'dkplus.yml',
            $resourceDocsDir . 'dkplus.yml',
            $resourceConfigDir . 'dkplus.yml',

            $cwd . 'dkplus.yml.dist',
            $docsDir . 'dkplus.yml.dist',
            $configDir . 'dkplus.yml.dist',
            $resourceDocsDir . 'dkplus.yml.dist',
            $resourceConfigDir . 'dkplus.yml.dist',
        ];
    }

    /** @return Extension[] */
    private function createDefaultExtensions(): array
    {
        return [
            new CoreExtension(),
            new SourceCodeExtension(),
            new PagesExtension(),
            new ExportExtension(),
            new TwigExtension(),
            new GlossaryExtension(),
            new SightseeingExtension(),
            new ClassDiagramExtension(),
            new MarkdownExtension(),
        ];
    }
}
