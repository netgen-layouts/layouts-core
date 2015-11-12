<?php

namespace Netgen\BlockManager\View\Tests;

use Netgen\BlockManager\View\TemplateResolver;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\View\BlockView;
use PHPUnit_Framework_TestCase;

class TemplateResolverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::__construct
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     */
    public function testResolveTemplate()
    {
        $view = $this->getView();

        $matcherMock = $this->getMock('Netgen\BlockManager\View\Matcher\MatcherInterface');
        $matcherMock
            ->expects($this->once())
            ->method('setConfig')
            ->with($this->equalTo(array('paragraph')));
        $matcherMock
            ->expects($this->once())
            ->method('match')
            ->with($this->equalTo($view))
            ->will($this->returnValue(true));

        $templateResolver = new TemplateResolver(
            array(
                'definition_identifier' => $matcherMock
            ),
            array(
                'api' => array(
                    'paragraph' => array(
                        'template' => 'some_template.html.twig',
                        'match' => array(
                            'definition_identifier' => 'paragraph'
                        )
                    )
                )
            )
        );

        self::assertEquals('some_template.html.twig', $templateResolver->resolveTemplate($view));
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::__construct
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     */
    public function testResolveTemplateWithEmptyMatchConfig()
    {
        $view = $this->getView();

        $templateResolver = new TemplateResolver(
            array(),
            array(
                'api' => array(
                    'paragraph' => array(
                        'template' => 'some_template.html.twig',
                        'match' => array()
                    )
                )
            )
        );

        self::assertEquals('some_template.html.twig', $templateResolver->resolveTemplate($view));
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @expectedException \InvalidArgumentException
     */
    public function testResolveTemplateThrowsInvalidArgumentExceptionIfNoContext()
    {
        $templateResolver = new TemplateResolver();
        $templateResolver->resolveTemplate($this->getView());
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     * @expectedException \InvalidArgumentException
     */
    public function testResolveTemplateThrowsInvalidArgumentExceptionIfNoMatch()
    {
        $view = $this->getView();

        $matcherMock = $this->getMock('Netgen\BlockManager\View\Matcher\MatcherInterface');
        $matcherMock
            ->expects($this->once())
            ->method('setConfig')
            ->with($this->equalTo(array('title')));
        $matcherMock
            ->expects($this->once())
            ->method('match')
            ->with($this->equalTo($view))
            ->will($this->returnValue(false));

        $templateResolver = new TemplateResolver(
            array(
                'definition_identifier' => $matcherMock
            ),
            array(
                'api' => array(
                    'title' => array(
                        'match' => array(
                            'definition_identifier' => 'title'
                        )
                    )
                )
            )
        );

        $templateResolver->resolveTemplate($view);
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     * @expectedException \InvalidArgumentException
     */
    public function testResolveTemplateThrowsInvalidArgumentExceptionIfNoMatcher()
    {
        $templateResolver = new TemplateResolver(
            array(),
            array(
                'api' => array(
                    'title' => array(
                        'match' => array(
                            'definition_identifier' => 'title'
                        )
                    )
                )
            )
        );

        $templateResolver->resolveTemplate($this->getView());
    }

    /**
     * Returns the view used for testing.
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    protected function getView()
    {
        $block = new Block(
            array(
                'definitionIdentifier' => 'paragraph'
            )
        );

        $view = new BlockView();
        $view->setBlock($block);
        $view->setContext('api');

        return $view;
    }
}
