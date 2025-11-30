<?php

declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Profile;
use Behat\MinkExtension\ServiceContainer\MinkExtension;
use FriendsOfBehat\SuiteSettingsExtension\ServiceContainer\SuiteSettingsExtension;
use FriendsOfBehat\SymfonyExtension\ServiceContainer\SymfonyExtension;
use FriendsOfBehat\VariadicExtension\ServiceContainer\VariadicExtension;
use Netgen\Layouts\Tests\App\Kernel;

return new Config()
    ->import('Resources/config/suites.php')
    ->withProfile(
        new Profile('default')
            ->withExtension(
                new Extension(
                    MinkExtension::class,
                    [
                        'base_url' => 'http://admin:admin@127.0.0.1:4242/',
                        'default_session' => 'symfony',
                        'javascript_session' => 'chrome',
                        'show_auto' => false,
                        'sessions' => [
                            'symfony' => [
                                'symfony' => null,
                            ],
                            'chrome' => [
                                'selenium2' => [
                                    'browser' => 'chrome',
                                    'wd_host' => 'http://127.0.0.1:9515',
                                    'capabilities' => [
                                        'browserName' => 'chrome',
                                        'browser' => 'chrome',
                                        'version' => '',
                                        'chrome' => [
                                            'switches' => [
                                                'no-sandbox',
                                                'disable-extensions',
                                                'disable-infobars',
                                                'start-fullscreen',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ),
            )
            ->withExtension(
                new Extension(
                    SymfonyExtension::class,
                    [
                        'kernel' => [
                            'class' => Kernel::class,
                        ],
                    ],
                ),
            )
            ->withExtension(new Extension(VariadicExtension::class))
            ->withExtension(
                new Extension(
                    SuiteSettingsExtension::class,
                    [
                        'paths' => [
                            'Resources/features',
                        ],
                    ],
                ),
            ),
    )
    ->withProfile(
        new Profile('headless')
            ->withExtension(
                new Extension(
                    MinkExtension::class,
                    [
                        'sessions' => [
                            'chrome' => [
                                'selenium2' => [
                                    'capabilities' => [
                                        'chrome' => [
                                            'switches' => [
                                                'headless',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ),
            ),
    );
