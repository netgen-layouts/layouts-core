<?php

declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;

return new Config()
    ->withProfile(
        new Profile('default')
            ->withSuite(
                new Suite('admin_managing_layouts')
                    ->withContexts(
                        # This service is responsible for clearing and recreating the database before each scenario,
                        # so that only data from the current scenario and its background is available
                        'netgen_layouts.behat.context.hook.doctrine_database',
                        # The transformer context services are responsible for all the transformations of data in steps
                        # For instance "layout called "My layout" should be available" transforms "(layout called "My layout")"
                        # to a proper Layout object, which is from now on available in the scope of the step
                        'netgen_layouts.behat.context.transform.layout',
                        # The setup contexts here are preparing the background, adding available layouts and users or administrators
                        # These contexts have steps like "I am logged in as an administrator" already implemented
                        'netgen_layouts.behat.context.setup.layout',
                        'netgen_layouts.behat.context.setup.admin_security',
                        # Those contexts are essential here we are placing all action steps like
                        # When I duplicate a layout called "Example layout" Then a layout called "Copy of example layout" should exist"
                        'netgen_layouts.behat.context.admin.managing_layouts',
                    )
                    ->withFilter(new TagFilter('@admin&&@managing_layouts')),
            )
            ->withSuite(
                new Suite('admin_managing_shared_layouts')
                    ->withContexts(
                        # This service is responsible for clearing and recreating the database before each scenario,
                        # so that only data from the current scenario and its background is available
                        'netgen_layouts.behat.context.hook.doctrine_database',
                        # The transformer context services are responsible for all the transformations of data in steps
                        # For instance "layout called "My layout" should be available" transforms "(layout called "My layout")"
                        # to a proper Layout object, which is from now on available in the scope of the step
                        'netgen_layouts.behat.context.transform.layout',
                        # The setup contexts here are preparing the background, adding available layouts and users or administrators
                        # These contexts have steps like "I am logged in as an administrator" already implemented
                        'netgen_layouts.behat.context.setup.layout',
                        'netgen_layouts.behat.context.setup.admin_security',
                        # Those contexts are essential here we are placing all action steps like
                        # When I duplicate a layout called "Example layout" Then a layout called "Copy of example layout" should exist"
                        'netgen_layouts.behat.context.admin.managing_shared_layouts',
                    )
                    ->withFilter(new TagFilter('@admin&&@managing_shared_layouts')),
            ),
    );
