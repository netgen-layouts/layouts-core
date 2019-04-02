<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Utils;

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

    public function __construct(?HTMLPurifier_Config $config = null)
    {
        if ($config === null) {
            $config = HTMLPurifier_Config::create(['Cache.DefinitionImpl' => null]);
            $config->set('HTML.Doctype', 'XHTML 1.0 Strict');
        }

        $this->purifier = new BaseHTMLPurifier($config);
    }

    public function purify(string $value): string
    {
        return $this->purifier->purify($value);
    }
}
