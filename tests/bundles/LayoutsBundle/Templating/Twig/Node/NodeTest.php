<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Node;

use Twig\Test\NodeTestCase;

use function sprintf;

abstract class NodeTest extends NodeTestCase
{
    protected function getNodeGetter(string $name, int $lineNo = 0): string
    {
        $line = $lineNo > 0 ? "// line {$lineNo}\n" : '';

        return sprintf(
            '%s(isset($context["%s"]) || array_key_exists("%s", $context) ? $context["%s"] : (function () { throw new RuntimeError(\'Variable "%s" does not exist.\', 1, $this->source); })())',
            $line,
            $name,
            $name,
            $name,
            $name,
        );
    }
}
