<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Rendering\Page;

interface Page
{
    public function prefix(): ?string;
    public function identifier(): string;
    public function fileName(): string;
    public function template(): string;
    public function context(): array;
}
