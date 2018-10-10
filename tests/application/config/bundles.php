<?php

declare(strict_types=1);

use Symfony\Bundle\WebServerBundle\WebServerBundle;

$bundles = [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class => ['all' => true],

    // Other dependencies

    Knp\Bundle\MenuBundle\KnpMenuBundle::class => ['all' => true],

    // Netgen Layouts

    Netgen\Bundle\ContentBrowserBundle\NetgenContentBrowserBundle::class => ['all' => true],
    Netgen\Bundle\ContentBrowserUIBundle\NetgenContentBrowserUIBundle::class => ['all' => true],
    Netgen\Bundle\BlockManagerBundle\NetgenBlockManagerBundle::class => ['all' => true],
    Netgen\Bundle\BlockManagerUIBundle\NetgenBlockManagerUIBundle::class => ['all' => true],
    Netgen\Bundle\BlockManagerAdminBundle\NetgenBlockManagerAdminBundle::class => ['all' => true],
    Netgen\Bundle\BlockManagerFixturesBundle\NetgenBlockManagerFixturesBundle::class => ['all' => true],
    Netgen\Bundle\BlockManagerStandardBundle\NetgenBlockManagerStandardBundle::class => ['all' => true],
];

// @deprecated Remove class_exists check when support for Symfony 2.8 ends
if (class_exists(WebServerBundle::class)) {
    $bundles[WebServerBundle::class] = ['all' => true];
}

return $bundles;
