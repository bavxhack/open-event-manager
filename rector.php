<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;

use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;

return RectorConfig::configure()
    // Passe die Pfade an dein Projekt an:
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    // Optional: parallele Verarbeitung
    ->withParallel()

    // Sets: Symfony + Doctrine Modernisierung + Annotation->Attribute
    ->withSets([
        // generelle PHP/Symfony Modernisierung (optional, aber hilfreich)
        SetList::PHP_82,
        SymfonySetList::SYMFONY_64,          // oder SYMFONY_70, falls vorhanden in deiner Rector-Version
        DoctrineSetList::DOCTRINE_ORM_214,   // je nach Doctrine-Version anpassen

        // DAS ist der Kern: Annotationen -> Attributes
        DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
        SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
    ])

    // Sinnvoll für Controller/Services: Constructor Injection modernisieren (optional)
    ->withRules([
        ClassPropertyAssignToConstructorPromotionRector::class,
        // MoveRepositoryFromParentToConstructorRector::class, // optional, wenn du viele Repository-Parents hast
    ])

    // Falls du (noch) Doctrine Annotations via doctrine/annotations benutzt und loswerden willst,
    // kann Rector/Upgrade-Flow das später vereinfachen. Erstmal migrieren, dann aufräumen.
    ;
