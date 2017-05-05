<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\TwigExtension;

use Dkplus\LivingDocs\ExportExtension\MenuItem;
use Dkplus\LivingDocs\Extension\RelativePathCalculator;
use RuntimeException;
use function in_array;

class PathResolver
{
    /** @var string[] */
    private $resolvableIds = [];

    /** @var ?string */
    private $currentId;
    /**
     * @var RelativePathCalculator
     */
    private $pathCalculator;

    public function __construct(RelativePathCalculator $pathCalculator)
    {
        $this->pathCalculator = $pathCalculator;
    }

    public function restrictResolvableIds(array $ids): void
    {
        $this->resolvableIds = $ids;
    }

    public function setBasePathId(string $id): void
    {
        $this->currentId = $id;
    }

    public function asset(string $path): string
    {
        $currentPath = $this->doResolve($this->currentId);
        return $this->pathCalculator->relativeToFile($path, $currentPath);
    }

    public function isActive(MenuItem $test): bool
    {
        if ($test->hasPageId() && $test->getPageId() === $this->currentId) {
            return true;
        }
        foreach ($test->getChildren() as $eachChild) {
            if ($this->isActive($eachChild)) {
                return true;
            }
        }
        return false;
    }

    public function resolve(string $id, bool $absolute = false): string
    {
        if (! in_array($id, $this->resolvableIds)) {
            throw new RuntimeException('There is no page with id ' . $id);
        }

        if ($absolute) {
            return ltrim($this->doResolve($id), '/');
        }
        return ltrim($this->pathCalculator->relativeToFile($this->doResolve($id), $this->doResolve($this->currentId)), '/');
    }

    private function doResolve(string $id): string
    {
        return str_replace('.', '/', $id) . '.html';
    }
}
