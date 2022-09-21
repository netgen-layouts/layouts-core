<?php

// To support running PHP CS Fixer via PHAR file (e.g. in GitHub Actions)
require_once __DIR__ . '/vendor/netgen/layouts-coding-standard/lib/PhpCsFixer/Config.php';

return (new Netgen\Layouts\CodingStandard\PhpCsFixer\Config())
    ->addRules([
        // Makes sure "time" function is not imported to make time sensitive
        // tests working by overriding the function, which would not be possible
        // if function was imported from global namespace
        'native_function_invocation' => ['include' => ['@all'], 'exclude' => ['time']],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude(['vendor', 'node_modules', 'tests/application'])
            ->in(__DIR__)
    )
;
