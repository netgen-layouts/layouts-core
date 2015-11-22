<?php

namespace Netgen\BlockManager\Tests\View\TemplateResolver;

use Netgen\BlockManager\Tests\View\Stubs\TemplateResolver;
use Netgen\BlockManager\Tests\View\Stubs\View;

class TemplateResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver\TemplateResolver::__construct
     * @covers \Netgen\BlockManager\View\TemplateResolver\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver\TemplateResolver::matches
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
                'definition_identifier' => $matcherMock,
            ),
            array(
                'api' => array(
                    'paragraph' => array(
                        'template' => 'some_template.html.twig',
                        'match' => array(
                            'definition_identifier' => 'paragraph',
                        ),
                    ),
                ),
            )
        );

        self::assertEquals('some_template.html.twig', $templateResolver->resolveTemplate($view));
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver\TemplateResolver::__construct
     * @covers \Netgen\BlockManager\View\TemplateResolver\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver\TemplateResolver::matches
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
                        'match' => array(),
                    ),
                ),
            )
        );

        self::assertEquals('some_template.html.twig', $templateResolver->resolveTemplate($view));
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver\TemplateResolver::__construct
     * @covers \Netgen\BlockManager\View\TemplateResolver\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver\TemplateResolver::matches
     */
    public function testResolveTemplateWithMultipleMatches()
    {
        $view = $this->getView();

        $templateResolver = new TemplateResolver(
            array(),
            array(
                'api' => array(
                    'paragraph' => array(
                        'template' => 'some_template.html.twig',
                        'match' => array(),
                    ),
                    'paragraph_other' => array(
                        'template' => 'some_other_template.html.twig',
                        'match' => array(),
                    ),
                ),
            )
        );

        self::assertEquals('some_template.html.twig', $templateResolver->resolveTemplate($view));
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver\TemplateResolver::resolveTemplate
     * @expectedException \RuntimeException
     */
    public function testResolveTemplateThrowsRuntimeExceptionIfNoContext()
    {
        $templateResolver = new TemplateResolver();
        $templateResolver->resolveTemplate($this->getView());
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver\TemplateResolver::resolveTemplate
     * @expectedException \RuntimeException
     */
    public function testResolveTemplateThrowsRuntimeExceptionIfEmptyContext()
    {
        $templateResolver = new TemplateResolver(
            array(),
            array('api' => array())
        );

        $templateResolver->resolveTemplate($this->getView());
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver\TemplateResolver::matches
     * @expectedException \RuntimeException
     */
    public function testResolveTemplateThrowsRuntimeExceptionIfNoMatch()
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
                'definition_identifier' => $matcherMock,
            ),
            array(
                'api' => array(
                    'title' => array(
                        'match' => array(
                            'definition_identifier' => 'title',
                        ),
                    ),
                ),
            )
        );

        $templateResolver->resolveTemplate($view);
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver\TemplateResolver::matches
     * @expectedException \RuntimeException
     */
    public function testResolveTemplateThrowsRuntimeExceptionIfNoMatcher()
    {
        $templateResolver = new TemplateResolver(
            array(),
            array(
                'api' => array(
                    'title' => array(
                        'match' => array(
                            'definition_identifier' => 'title',
                        ),
                    ),
                ),
            )
        );

        $templateResolver->resolveTemplate($this->getView());
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver\TemplateResolver::matches
     * @expectedException \RuntimeException
     */
    public function testResolveTemplateThrowsRuntimeExceptionIfNoMatcherInterface()
    {
        $view = $this->getView();

        $matcherMock = $this->getMock('DateTime');

        $templateResolver = new TemplateResolver(
            array(
                'definition_identifier' => $matcherMock,
            ),
            array(
                'api' => array(
                    'title' => array(
                        'match' => array(
                            'definition_identifier' => 'title',
                        ),
                    ),
                ),
            )
        );

        $templateResolver->resolveTemplate($view);
    }

    /**
     * Returns the view used for testing.
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    protected function getView()
    {
        $view = new View();
        $view->setContext('api');

        return $view;
    }
}
