<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Node;

use PHPUnit\Framework\TestCase;
use Twig\Compiler;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Node\Node;

use function sprintf;
use function trim;

abstract class NodeTestBase extends TestCase
{
    /**
     * @return mixed[]
     */
    abstract public static function getTests(): iterable;

    /**
     * @dataProvider getTests
     */
    public function testCompile(Node $node, string $source, ?Environment $environment = null, bool $isPattern = false): void
    {
        self::assertNodeCompilation($source, $node, $environment, $isPattern);
    }

    public static function assertNodeCompilation(string $source, Node $node, ?Environment $environment = null, bool $isPattern = false): void
    {
        $compiler = self::getCompiler($environment);
        $compiler->compile($node);

        if ($isPattern) {
            self::assertStringMatchesFormat($source, trim($compiler->getSource()));
        } else {
            self::assertSame($source, trim($compiler->getSource()));
        }
    }

    protected static function getCompiler(?Environment $environment = null): Compiler
    {
        return new Compiler($environment ?? self::getEnvironment());
    }

    protected static function getEnvironment(): Environment
    {
        return new Environment(new ArrayLoader([]));
    }

    protected static function getNodeGetter(string $name, int $lineNo = 0): string
    {
        $line = $lineNo > 0 ? sprintf("// line %d\n", $lineNo) : '';

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
