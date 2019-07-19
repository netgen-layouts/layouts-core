<?php

declare(strict_types=1);

return [
    // Symfony

    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],

    // Other dependencies

    Knp\Bundle\MenuBundle\KnpMenuBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
    Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class => ['all' => true],

    // Netgen Layouts

    Netgen\Bundle\ContentBrowserBundle\NetgenContentBrowserBundle::class => ['all' => true],
    Netgen\Bundle\ContentBrowserUIBundle\NetgenContentBrowserUIBundle::class => ['all' => true],
    Netgen\Bundle\LayoutsBundle\NetgenLayoutsBundle::class => ['all' => true],
    Netgen\Bundle\LayoutsUIBundle\NetgenLayoutsUIBundle::class => ['all' => true],
    Netgen\Bundle\LayoutsAdminBundle\NetgenLayoutsAdminBundle::class => ['all' => true],
    Netgen\Bundle\LayoutsStandardBundle\NetgenLayoutsStandardBundle::class => ['all' => true],

    // Test dependencies

    FriendsOfBehat\SymfonyExtension\Bundle\FriendsOfBehatSymfonyExtensionBundle::class => ['all' => true],
];
