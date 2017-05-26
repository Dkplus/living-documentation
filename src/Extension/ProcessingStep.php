<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Extension;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface ProcessingStep
{
    public function process(InputInterface $input, OutputInterface $output): int;
}
