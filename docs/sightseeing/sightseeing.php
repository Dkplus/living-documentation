<?php
declare(strict_types=1);

use Dkplus\LivingDocs\Console\ConsoleApplication;
use Dkplus\LivingDocs\Sightseeing\ClassPointOfInterestDescription;
use Dkplus\LivingDocs\Sightseeing\TourDescription;

$tour = new TourDescription();
$tour->name('Sightseeing');
$tour->describe('Let me show you how sightseeing has been implemented in dkplus.');
$tour->appendPointOfInterest(ClassPointOfInterestDescription::withCode(ConsoleApplication::class, 'Starting in the Application', ''));
return $tour;
