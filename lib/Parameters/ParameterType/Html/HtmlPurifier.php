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
     * @var \HTMLPurifier_Config
     */
    private $config;

    /**
     * @var \HTMLPurifier
     */
    private $purifier;

    public function __construct()
    {
        $this->config = HTMLPurifier_Config::createDefault();
        $this->config->set('HTML.Doctype', 'XHTML 1.0 Strict');
        $this->purifier = new BaseHTMLPurifier($this->config);
    }

    public function purify($value)
    {
        return $this->purifier->purify($value);
    }
}
