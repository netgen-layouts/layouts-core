<?php

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\View\TemplateResolver;
use Netgen\BlockManager\Tests\View\Stubs\View;

class TemplateResolverTest extends \PHPUnit_Framework_TestCase
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

        $configurationMock = $this->getMock('Netgen\BlockManager\Configuration\ConfigurationInterface');
        $configurationMock
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->equalTo('view'))
            ->will(
                $this->returnValue(
                    array(
                        'context' => array(
                            'paragraph' => array(
                                'template' => 'some_template.html.twig',
                                'match' => array(
                                    'definition_identifier' => 'paragraph',
                                ),
                            ),
                        ),
                    )
                )
            );

        $templateResolver = new TemplateResolver(
            array(
                'definition_identifier' => $matcherMock,
            ),
            $configurationMock
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

        $configurationMock = $this->getMock('Netgen\BlockManager\Configuration\ConfigurationInterface');
        $configurationMock
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->equalTo('view'))
            ->will(
                $this->returnValue(
                    array(
                        'context' => array(
                            'paragraph' => array(
                                'template' => 'some_template.html.twig',
                                'match' => array(),
                            ),
                        ),
                    )
                )
            );

        $templateResolver = new TemplateResolver(
            array(),
            $configurationMock
        );

        self::assertEquals('some_template.html.twig', $templateResolver->resolveTemplate($view));
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::__construct
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     */
    public function testResolveTemplateWithMultipleMatches()
    {
        $view = $this->getView();

        $configurationMock = $this->getMock('Netgen\BlockManager\Configuration\ConfigurationInterface');
        $configurationMock
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->equalTo('view'))
            ->will(
                $this->returnValue(
                    array(
                        'context' => array(
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
                )
            );

        $templateResolver = new TemplateResolver(
            array(),
            $configurationMock
        );

        self::assertEquals('some_template.html.twig', $templateResolver->resolveTemplate($view));
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @expectedException \RuntimeException
     */
    public function testResolveTemplateThrowsRuntimeExceptionIfNoContext()
    {
        $configurationMock = $this->getMock('Netgen\BlockManager\Configuration\ConfigurationInterface');
        $configurationMock
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->equalTo('view'))
            ->will($this->returnValue(array()));

        $templateResolver = new TemplateResolver(array(), $configurationMock);
        $templateResolver->resolveTemplate($this->getView());
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @expectedException \RuntimeException
     */
    public function testResolveTemplateThrowsRuntimeExceptionIfEmptyContext()
    {
        $configurationMock = $this->getMock('Netgen\BlockManager\Configuration\ConfigurationInterface');
        $configurationMock
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->equalTo('view'))
            ->will($this->returnValue(array('context' => array())));

        $templateResolver = new TemplateResolver(
            array(),
            $configurationMock
        );

        $templateResolver->resolveTemplate($this->getView());
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
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

        $configurationMock = $this->getMock('Netgen\BlockManager\Configuration\ConfigurationInterface');
        $configurationMock
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->equalTo('view'))
            ->will(
                $this->returnValue(
                    array(
                        'context' => array(
                            'title' => array(
                                'match' => array(
                                    'definition_identifier' => 'title',
                                ),
                            ),
                        ),
                    )
                )
            );

        $templateResolver = new TemplateResolver(
            array(
                'definition_identifier' => $matcherMock,
            ),
            $configurationMock
        );

        $templateResolver->resolveTemplate($view);
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     * @expectedException \RuntimeException
     */
    public function testResolveTemplateThrowsRuntimeExceptionIfNoMatcher()
    {
        $configurationMock = $this->getMock('Netgen\BlockManager\Configuration\ConfigurationInterface');
        $configurationMock
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->equalTo('view'))
            ->will(
                $this->returnValue(
                    array(
                        'context' => array(
                            'title' => array(
                                'match' => array(
                                    'definition_identifier' => 'title',
                                ),
                            ),
                        ),
                    )
                )
            );

        $templateResolver = new TemplateResolver(
            array(),
            $configurationMock
        );

        $templateResolver->resolveTemplate($this->getView());
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     * @expectedException \RuntimeException
     */
    public function testResolveTemplateThrowsRuntimeExceptionIfNoMatcherInterface()
    {
        $view = $this->getView();

        $matcherMock = $this->getMock('DateTime');

        $configurationMock = $this->getMock('Netgen\BlockManager\Configuration\ConfigurationInterface');
        $configurationMock
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->equalTo('view'))
            ->will(
                $this->returnValue(
                    array(
                        'context' => array(
                            'title' => array(
                                'match' => array(
                                    'definition_identifier' => 'title',
                                ),
                            ),
                        ),
                    )
                )
            );

        $templateResolver = new TemplateResolver(
            array(
                'definition_identifier' => $matcherMock,
            ),
            $configurationMock
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
        $view->setContext('context');

        return $view;
    }
}
