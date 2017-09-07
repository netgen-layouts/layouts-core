<?php

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

class RenderZoneTest extends TestCase
{
    /**
     * @var \Twig\Environment
     */
    protected $environment;

    /**
     * @var \Parser
     */
    protected $parser;

    public function setUp()
    {
        $this->environment = new Environment(
            $this->createMock(LoaderInterface::class),
            array(
                'cache' => false,
                'autoescape' => false,
                'optimizations' => 0,
            )
        );

        $this->environment->addTokenParser(new RenderZone());
        $this->parser = new Parser($this->environment);
    }

    /**
     * @param string $source
     * @param \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone $node
     *
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser\RenderZone::parse
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser\RenderZone::getTag
     *
     * @dataProvider compileProvider
     */
    public function testCompile($source, $node)
    {
        $stream = $this->environment->tokenize(new Source($source, ''));

        $this->assertEquals($node, $this->parser->parse($stream)->getNode('body')->getNode(0));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser\RenderZone::parse
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser\RenderZone::getTag
     * @expectedException \Twig\Error\SyntaxError
     * @expectedExceptionMessage Unexpected token "name" of value "foo" at line 1.
     */
    public function testCompileThrowsTwigErrorSyntaxException()
    {
        $stream = $this->environment->tokenize(
            new Source('{% ngbm_render_zone zone foo=\'bar\' %}', '')
        );

        $this->parser->parse($stream);
    }

    public function compileProvider()
    {
        return array(
            array(
                '{% ngbm_render_zone zone %}',
                new RenderZoneNode(
                    new NameExpression('zone', 1),
                    null,
                    1,
                    'ngbm_render_zone'
                ),
            ),
            array(
                '{% ngbm_render_zone zone context="json" %}',
                new RenderZoneNode(
                    new NameExpression('zone', 1),
                    new ConstantExpression('json', 1),
                    1,
                    'ngbm_render_zone'
                ),
            ),
        );
    }
}
