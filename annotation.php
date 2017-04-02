<?php
namespace Application;

require_once __DIR__ . '/vendor/autoload.php';

$finder = new Finder();
$files = iterator_to_array($finder->files()->in(__DIR__ . '/../pushnotificationcore/')->name('*.php'));

