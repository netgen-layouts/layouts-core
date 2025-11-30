<?php

declare(strict_types=1);

use Behat\Config\Config;

return new Config()
    ->import(
        [
            'suites/admin/managing_layouts.php',
            'suites/admin/managing_shared_layouts.php',
        ],
    );
