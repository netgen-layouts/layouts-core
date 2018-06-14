<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\TokenParser;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone as RenderZoneNode;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser\RenderZone;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\LoaderInterface;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Parser;
use Twig\Source;

final class RenderZoneTest extends TestCase
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

        $this->environment->addTokenParser(new RenderZone());
        $this->parser = new Parser($this->environment);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser\RenderZone::getTag
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser\RenderZone::parse
     *
     * @dataProvider compileProvider
     */
    public function testCompile(string $source, RenderZoneNode $node): void
    {
        $stream = $this->environment->tokenize(new Source($source, ''));

        $this->assertEquals($node, $this->parser->parse($stream)->getNode('body')->getNode(0));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser\RenderZone::getTag
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser\RenderZone::parse
     * @expectedException \Twig\Error\SyntaxError
     * @expectedExceptionMessage Unexpected token "name" of value "foo" at line 1.
     */
    public function testCompileThrowsTwigErrorSyntaxException(): void
    {
        $stream = $this->environment->tokenize(
            new Source('{% ngbm_render_zone zone foo=\'bar\' %}', '')
        );

        $this->parser->parse($stream);
    }

    public function compileProvider(): array
    {
        return [
            [
                '{% ngbm_render_zone zone %}',
                new RenderZoneNode(
                    new NameExpression('zone', 1),
                    null,
                    1,
                    'ngbm_render_zone'
                ),
            ],
            [
                '{% ngbm_render_zone zone context="json" %}',
                new RenderZoneNode(
                    new NameExpression('zone', 1),
                    new ConstantExpression('json', 1),
                    1,
                    'ngbm_render_zone'
                ),
            ],
        ];
    }
}
