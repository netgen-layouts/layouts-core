<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Utils;

use HTMLPurifier as BaseHTMLPurifier;
use HTMLPurifier_Config;
use HTMLPurifier_HTML5Config;

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
            $config = HTMLPurifier_HTML5Config::create(['Cache.DefinitionImpl' => null]);
            $config->set('Attr.AllowedFrameTargets', ['_blank']);
        }

        $this->purifier = new BaseHTMLPurifier($config);
    }

    public function purify(string $value): string
    {
        return $this->purifier->purify($value);
    }
}
