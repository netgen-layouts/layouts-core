<?php

// To support running PHP CS Fixer via PHAR file (e.g. in GitHub Actions)
require_once __DIR__ . '/vendor/netgen/layouts-coding-standard/lib/PhpCsFixer/Config.php';

return new Netgen\Layouts\CodingStandard\PhpCsFixer\Config()
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude(['vendor', 'node_modules', 'tests/application/public'])
            ->in(__DIR__)
    )
;
