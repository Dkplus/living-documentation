<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Section;

use Dkplus\LivingDocs\Documentation;
use Dkplus\LivingDocs\Rendering\Configuration;

class Section
{
    /** @var string */
    private $identifier;

    /** @var Documentation[] */
    private $sections;

    public function __construct(string $identifier, Documentation ...$sections)
    {
        $this->identifier = $identifier;
        $this->sections = $sections;
    }

    public function decoratePages(Configuration $pages): Configuration
    {
        return new SectionConfigurationDecorator($pages, $this->identifier);
    }

    /** @return Documentation[] */
    public function getParts(): array
    {
        return $this->sections;
    }
}
