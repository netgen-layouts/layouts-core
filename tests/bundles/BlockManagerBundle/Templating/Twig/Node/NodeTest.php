<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Node;

abstract class NodeTest extends \Twig_Test_NodeTestCase
{
    protected function getNodeGetter($name, $line = false)
    {
        $line = $line > 0 ? "// line {$line}\n" : '';

        if (PHP_VERSION_ID >= 70000) {
            return sprintf('%s(isset($context["%s"]) || array_key_exists("%s", $context) ? $context["%s"] : (function () { throw new Twig_Error_Runtime(\'Variable "%s" does not exist.\', 1, $this->getSourceContext()); })())', $line, $name, $name, $name, $name);
        }

        return sprintf('%s(isset($context["%s"]) ? $context["%s"] : $this->getContext($context, "%s"))', $line, $name, $name, $name);
    }
}
