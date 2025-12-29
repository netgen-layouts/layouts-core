<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Node;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Twig\Compiler;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Node\Node;

use function mb_trim;
use function sprintf;

abstract class NodeTestBase extends TestCase
{
    /**
     * @return mixed[]
     */
    abstract public static function compileDataProvider(): iterable;

    #[DataProvider('compileDataProvider')]
    final public function testCompile(Node $node, string $source, ?Environment $environment = null, bool $isPattern = false): void
    {
        self::assertNodeCompilation($source, $node, $environment, $isPattern);
    }

    final public static function assertNodeCompilation(string $source, Node $node, ?Environment $environment = null, bool $isPattern = false): void
    {
        $compiler = self::getCompiler($environment);
        $compiler->compile($node);

        if ($isPattern) {
            self::assertStringMatchesFormat($source, mb_trim($compiler->getSource()));
        } else {
            self::assertSame($source, mb_trim($compiler->getSource()));
        }
    }

    final protected static function getCompiler(?Environment $environment = null): Compiler
    {
        return new Compiler($environment ?? self::getEnvironment());
    }

    final protected static function getEnvironment(): Environment
    {
        return new Environment(new ArrayLoader([]));
    }

    final protected static function getNodeGetter(string $name, int $lineNo = 0): string
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
