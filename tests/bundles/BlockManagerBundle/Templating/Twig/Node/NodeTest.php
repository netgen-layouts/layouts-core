<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Node;

abstract class NodeTest extends \Twig_Test_NodeTestCase
{
    protected function getNodeGetter($name, $line = false)
    {
        $line = $line > 0 ? "// line {$line}\n" : '';

        if (PHP_VERSION_ID >= 70000) {
            return sprintf('%s($context["%s"] ?? $this->getContext($context, "%s"))', $line, $name, $name);
        }

        return sprintf('%s(isset($context["%s"]) ? $context["%s"] : $this->getContext($context, "%s"))', $line, $name, $name, $name);
    }
}
