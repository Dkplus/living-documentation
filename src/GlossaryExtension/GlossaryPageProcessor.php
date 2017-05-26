<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\GlossaryExtension;

use Assert\Assert;
use Dkplus\LivingDocumentation\Annotation\CoreConcept;
use Dkplus\LivingDocumentation\PagesExtension\Page;
use Dkplus\LivingDocumentation\PagesExtension\PageProcessor;
use Dkplus\LivingDocumentation\PagesExtension\ProcessedPage;
use Dkplus\LivingDocumentation\SourceCodeExtension\AnnotationListeners;
use Dkplus\LivingDocumentation\SourceCodeExtension\Listener\AnnotationSubscriber;
use ReflectionClass;
use function ksort;

class GlossaryPageProcessor implements PageProcessor, AnnotationSubscriber
{
    /** @var CoreConcept[] */
    private $coreConcepts = [];

    public function subscribe(AnnotationListeners $listeners): void
    {
        $listeners->notifyAboutClassAnnotation(CoreConcept::class, [$this, 'notifyAboutCoreConceptOnClass']);
    }

    public function notifyAboutCoreConceptOnClass(CoreConcept $concept, ReflectionClass $class): void
    {
        $this->coreConcepts[$class->getShortName()] = $concept;
    }

    public function preProcess(Page $page): void
    {
    }

    public function process(Page $page): ProcessedPage
    {
        /* @var $page GlossaryPage */
        Assert::that($page)->isInstanceOf(GlossaryPage::class);
        $glossary = [];
        foreach ($this->coreConcepts as $identifier => $each) {
            $glossary[$identifier] = $each->description;
        }
        ksort($glossary);

        return new ProcessedPage($page->getTitle(), 'glossary', ['title' => $page->getTitle(), 'items' => $glossary]);
    }
}
