<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Extension;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends BaseCommand
{
    /** @var Processor */
    private $processor;

    public function __construct(string $name, Processor $processor)
    {
        parent::__construct($name);
        $this->processor = $processor;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->processor->process($input, $output);
    }
}
