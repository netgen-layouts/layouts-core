<?php

namespace Netgen\BlockManager\Parameters\Filter\Html;

use Netgen\BlockManager\Parameters\ParameterFilterInterface;
use HTMLPurifier_Config;
use HTMLPurifier;

class HtmlPurifierFilter implements ParameterFilterInterface
{
    /**
     * @var \HTMLPurifier_Config
     */
    protected $config;

    /**
     * @var \HTMLPurifier
     */
    protected $purifier;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->config = HTMLPurifier_Config::createDefault();
        $this->config->set('HTML.Doctype', 'XHTML 1.0 Strict');
        $this->purifier = new HTMLPurifier($this->config);
    }

    /**
     * Filters the parameter value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function filter($value)
    {
        return $this->purifier->purify($value);
    }
}
