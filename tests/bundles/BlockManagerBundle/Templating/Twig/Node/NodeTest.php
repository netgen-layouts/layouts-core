<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Node;

use Twig\Environment;
use Twig\Test\NodeTestCase;

abstract class NodeTest extends NodeTestCase
{
    protected function getNodeGetter($name, $line = false)
    {
        $line = $line > 0 ? "// line {$line}\n" : '';

        if (Environment::VERSION_ID >= 20000) {
            return sprintf('%s(isset($context["%s"]) || array_key_exists("%s", $context) ? $context["%s"] : (function () { throw new Twig_Error_Runtime(\'Variable "%s" does not exist.\', 1, $this->source); })())', $line, $name, $name, $name, $name);
        }

        if (\PHP_VERSION_ID >= 70000) {
            return sprintf('%s($context["%s"] ?? $this->getContext($context, "%s"))', $line, $name, $name);
        }

        return sprintf('%s(isset($context["%s"]) ? $context["%s"] : $this->getContext($context, "%s"))', $line, $name, $name, $name);
    }
}
