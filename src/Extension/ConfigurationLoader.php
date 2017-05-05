<?php
declare(strict_types=1);

namespace Dkplus\LivingDocs\Extension;

use RuntimeException;
use Symfony\Component\Yaml\Yaml;
use function dirname;
use function file_exists;
use function file_get_contents;
use function getcwd;
use function realpath;
use function var_dump;

class ConfigurationLoader
{
    /** @var string[] */
    private $autodetectPaths;

    /** @var ?string */
    private $path;

    public function __construct(array $autodetectPaths)
    {
        $this->autodetectPaths = $autodetectPaths;
    }

    public function setConfigurationFilePath(string $filePath): void
    {
        $this->path = $filePath;
    }

    public function getBasePath(): string
    {
        try {
            return dirname($this->detectConfigPath());
        } catch (RuntimeException $exception) {
        }
        return realpath(getcwd());
    }

    public function loadConfiguration(): array
    {
        $path = $this->path;
        if (! $path) {
            foreach ($this->autodetectPaths as $each) {
                if (file_exists($each)) {
                    $path = $each;
                    break;
                }
            }
        }

        if (! $path) {
            throw new RuntimeException('Could not find a configuration file');
        }
        return (array) Yaml::parse(file_get_contents($path));
    }

    private function detectConfigPath(): string
    {
        if ($this->path && file_exists($this->path)) {
            return $this->path;
        }

        foreach ($this->autodetectPaths as $each) {
            if (file_exists($each)) {
                return $each;
            }
        }

        throw new RuntimeException('Could not find a configuration file');
    }
}
