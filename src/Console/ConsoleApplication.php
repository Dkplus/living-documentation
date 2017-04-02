<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Console;

use Symfony\Component\Console\Application;

class ConsoleApplication
{
    private $application;

    public function __construct()
    {
        $this->application = new Application('dkplus');
        $this->application->add(new RenderHtmlCommand());
    }

    public function run(): int
    {
        return $this->application->run();
    }
}
