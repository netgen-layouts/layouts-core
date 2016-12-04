<?php

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        'concat_space' => ['spacing' => 'one'],
        'phpdoc_order' => true,
        'psr0' => false,
        'phpdoc_align' => false,
        'phpdoc_to_comment' => false,
        'cast_spaces' => false,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
            ->exclude(['vendor', 'node_modules'])
            ->files()->name('*.php')
    );
