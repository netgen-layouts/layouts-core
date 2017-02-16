<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\TokenParser;

use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Node\RenderZone as RenderZoneNode;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser\RenderZone;
use PHPUnit\Framework\TestCase;
use Twig_Environment;
use Twig_LoaderInterface;
use Twig_Node_Expression_Constant;
use Twig_Node_Expression_Name;
use Twig_Parser;
use Twig_Source;

class RenderZoneTest extends TestCase
{
    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @var \Twig_Parser
     */
    protected $parser;

    public function setUp()
    {
        $this->environment = new Twig_Environment(
            $this->createMock(Twig_LoaderInterface::class),
            array(
                'cache' => false,
                'autoescape' => false,
                'optimizations' => 0,
            )
        );

        $this->environment->addTokenParser(new RenderZone());
        $this->parser = new Twig_Parser($this->environment);
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
        $stream = $this->environment->tokenize(new Twig_Source($source, ''));

        $this->assertEquals($node, $this->parser->parse($stream)->getNode('body')->getNode(0));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser\RenderZone::parse
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser\RenderZone::getTag
     * @expectedException \Twig_Error_Syntax
     * @expectedExceptionMessage Unexpected token "name" of value "foo" at line 1.
     */
    public function testCompileThrowsTwigErrorSyntaxException()
    {
        $stream = $this->environment->tokenize(
            new Twig_Source('{% ngbm_render_zone zone foo=\'bar\' %}', '')
        );

        $this->parser->parse($stream);
    }

    public function compileProvider()
    {
        return array(
            array(
                '{% ngbm_render_zone zone %}',
                new RenderZoneNode(
                    new Twig_Node_Expression_Name('zone', 1),
                    null,
                    1,
                    'ngbm_render_zone'
                ),
            ),
            array(
                '{% ngbm_render_zone zone context="json" %}',
                new RenderZoneNode(
                    new Twig_Node_Expression_Name('zone', 1),
                    new Twig_Node_Expression_Constant('json', 1),
                    1,
                    'ngbm_render_zone'
                ),
            ),
        );
    }
}
