<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\TokenParser;

use Netgen\Bundle\LayoutsBundle\Templating\Twig\Node\DefaultContext as DefaultContextNode;
use Netgen\Bundle\LayoutsBundle\Templating\Twig\TokenParser\DefaultContext;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Loader\LoaderInterface;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\Variable\ContextVariable;
use Twig\Parser;
use Twig\Source;

#[CoversClass(DefaultContext::class)]
final class DefaultContextTest extends TestCase
{
    private Environment $environment;

    private Parser $parser;

    protected function setUp(): void
    {
        $this->environment = new Environment(
            self::createStub(LoaderInterface::class),
            [
                'cache' => false,
                'autoescape' => false,
                'optimizations' => 0,
            ],
        );

        $this->environment->addTokenParser(new DefaultContext());
        $this->parser = new Parser($this->environment);
    }

    #[DataProvider('compileDataProvider')]
    public function testCompile(string $source, DefaultContextNode $node, string $tag): void
    {
        $stream = $this->environment->tokenize(new Source($source, ''));

        $node->setNodeTag($tag);

        self::assertSame((string) $node, (string) $this->parser->parse($stream)->getNode('body')->getNode('0'));
    }

    public function testCompileThrowsTwigErrorSyntaxException(): void
    {
        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage('Unexpected token "string" of value "bar" ("end of statement block" expected) at line 1.');

        $stream = $this->environment->tokenize(
            new Source('{% nglayouts_default_context \'foo\' \'bar\' %}', ''),
        );

        $this->parser->parse($stream);
    }

    public static function compileDataProvider(): iterable
    {
        return [
            [
                '{% nglayouts_default_context foo %}',
                new DefaultContextNode(
                    new ContextVariable('foo', 1),
                    1,
                ),
                'nglayouts_default_context',
            ],
            [
                '{% nglayouts_default_context "foo" %}',
                new DefaultContextNode(
                    new ConstantExpression('foo', 1),
                    1,
                ),
                'nglayouts_default_context',
            ],
        ];
    }
}
