<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\ParameterType\Html;

use HTMLPurifier as BaseHTMLPurifier;
use HTMLPurifier_Config;

/**
 * Filter used to remove all unsafe HTML from the provided value.
 */
final class HtmlPurifier
{
    /**
     * @var \HTMLPurifier
     */
    private $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::create(['Cache.DefinitionImpl' => null]);
        $config->set('HTML.Doctype', 'XHTML 1.0 Strict');
        $this->purifier = new BaseHTMLPurifier($config);
    }

    public function purify(string $value): string
    {
        return $this->purifier->purify($value);
    }
}
