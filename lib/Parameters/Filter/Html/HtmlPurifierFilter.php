<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\Filter\Html;

use HTMLPurifier;
use HTMLPurifier_Config;
use Netgen\BlockManager\Parameters\ParameterFilterInterface;

/**
 * Filter used to remove all unsafe HTML from the provided value.
 */
final class HtmlPurifierFilter implements ParameterFilterInterface
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
        $this->purifier = new HTMLPurifier($this->config);
    }

    public function filter($value)
    {
        return $this->purifier->purify($value);
    }
}
