<?php
declare(strict_types=1);

use Dkplus\LivingDocs\Extension\Application;
use Dkplus\LivingDocs\SightseeingExtension\ClassPointOfInterestDescription;
use Dkplus\LivingDocs\SightseeingExtension\MethodPointOfInterestDescription;
use Dkplus\LivingDocs\SightseeingExtension\TourDescription;

$tour = new TourDescription();
$tour->name('Sightseeing');
$tour->describe('Let me show you how sightseeing has been implemented in dkplus.');
$tour->appendPointOfInterest(ClassPointOfInterestDescription::withCode(
    Application::class,
    'Starting in the Application',
    'Description here'
));
$tour->appendPointOfInterest(MethodPointOfInterestDescription::withCode(
    Application::class, 'run',
    'Running the application',
    'Description here'
));
return $tour;
