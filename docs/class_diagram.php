<?php
declare(strict_types=1);

use Dkplus\LivingDocs\ClassDiagramExtension\ClassDiagramDescription;
use Dkplus\LivingDocs\SightseeingExtension\ClassPointOfInterestDescription;
use Dkplus\LivingDocs\SightseeingExtension\FunctionPointOfInterestDescription;
use Dkplus\LivingDocs\SightseeingExtension\MethodPointOfInterestDescription;
use Dkplus\LivingDocs\SightseeingExtension\PointOfInterest;
use Dkplus\LivingDocs\SightseeingExtension\PointOfInterestDescription;
use Dkplus\LivingDocs\SightseeingExtension\SightseeingExtension;
use Dkplus\LivingDocs\SightseeingExtension\SightseeingPage;
use Dkplus\LivingDocs\SightseeingExtension\SightseeingPageProcessor;
use Dkplus\LivingDocs\SightseeingExtension\Tour;
use Dkplus\LivingDocs\SightseeingExtension\TourDescription;

return ClassDiagramDescription::ofClasses('Sightseeing', [
    ClassPointOfInterestDescription::class,
    FunctionPointOfInterestDescription::class,
    MethodPointOfInterestDescription::class,
    PointOfInterest::class,
    PointOfInterestDescription::class,
    SightseeingExtension::class,
    SightseeingPage::class,
    SightseeingPageProcessor::class,
    Tour::class,
    TourDescription::class,
])
    ->addExternalActor('Configuration')->thatDependsOn(TourDescription::class, '«provide»');
