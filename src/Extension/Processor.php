<?php
declare(strict_types=1);

namespace Dkplus\LivingDocumentation\Extension;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Processor
{
    /** @var ProcessingStep[] */
    private $steps = [];

    public function addStep(ProcessingStep $step): void
    {
        $this->steps[] = $step;
    }

    public function process(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->steps as $each) {
            $result = $each->process($input, $output);
            if ($result !== 0) {
                return $result;
            }
        }
        return 0;
    }
}
