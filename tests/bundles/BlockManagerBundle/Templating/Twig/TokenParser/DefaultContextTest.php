<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\TokenParser;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\DefaultContext as DefaultContextNode;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser\DefaultContext;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Loader\LoaderInterface;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Parser;
use Twig\Source;

final class DefaultContextTest extends TestCase
{
    /**
     * @var \Twig\Environment
     */
    private $environment;

    /**
     * @var \Twig\Parser
     */
    private $parser;

    public function setUp(): void
    {
        $this->environment = new Environment(
            $this->createMock(LoaderInterface::class),
            [
                'cache' => false,
                'autoescape' => false,
                'optimizations' => 0,
            ]
        );

        $this->environment->addTokenParser(new DefaultContext());
        $this->parser = new Parser($this->environment);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser\DefaultContext::getTag
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser\DefaultContext::parse
     *
     * @dataProvider compileProvider
     */
    public function testCompile(string $source, DefaultContextNode $node): void
    {
        $stream = $this->environment->tokenize(new Source($source, ''));

        self::assertEquals($node, $this->parser->parse($stream)->getNode('body')->getNode(0));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser\DefaultContext::getTag
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser\DefaultContext::parse
     */
    public function testCompileThrowsTwigErrorSyntaxException(): void
    {
        $this->expectException(SyntaxError::class);
        $this->expectExceptionMessage('Unexpected token "string" of value "bar" ("end of statement block" expected) at line 1.');

        $stream = $this->environment->tokenize(
            new Source('{% ngbm_default_context \'foo\' \'bar\' %}', '')
        );

        $this->parser->parse($stream);
    }

    public function compileProvider(): array
    {
        return [
            [
                '{% ngbm_default_context foo %}',
                new DefaultContextNode(
                    new NameExpression('foo', 1),
                    1,
                    'ngbm_default_context'
                ),
            ],
            [
                '{% ngbm_default_context "foo" %}',
                new DefaultContextNode(
                    new ConstantExpression('foo', 1),
                    1,
                    'ngbm_default_context'
                ),
            ],
        ];
    }
}
