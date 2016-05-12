<?php

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\View\TemplateResolver;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\MatcherInterface;
use DateTime;

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

        $matcherMock = $this->getMock(MatcherInterface::class);
        $matcherMock
            ->expects($this->once())
            ->method('setConfig')
            ->with($this->equalTo(array('paragraph')));
        $matcherMock
            ->expects($this->once())
            ->method('match')
            ->with($this->equalTo($view))
            ->will($this->returnValue(true));

        $viewConfiguration = array(
            'view' => array(
                'context' => array(
                    'paragraph' => array(
                        'template' => 'some_template.html.twig',
                        'match' => array(
                            'definition_identifier' => 'paragraph',
                        ),
                    ),
                ),
            )
        );

        $templateResolver = new TemplateResolver(
            array(
                'definition_identifier' => $matcherMock,
            ),
            $viewConfiguration
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

        $viewConfiguration = array(
            'view' => array(
                'context' => array(
                    'paragraph' => array(
                        'template' => 'some_template.html.twig',
                        'match' => array(),
                    ),
                ),
            )
        );

        $templateResolver = new TemplateResolver(
            array(),
            $viewConfiguration
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

        $viewConfiguration = array(
            'view' => array(
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
        );

        $templateResolver = new TemplateResolver(
            array(),
            $viewConfiguration
        );

        self::assertEquals('some_template.html.twig', $templateResolver->resolveTemplate($view));
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @expectedException \RuntimeException
     */
    public function testResolveTemplateThrowsRuntimeExceptionIfNoContext()
    {
        $templateResolver = new TemplateResolver(array(), array('view' => array()));
        $templateResolver->resolveTemplate($this->getView());
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @expectedException \RuntimeException
     */
    public function testResolveTemplateThrowsRuntimeExceptionIfEmptyContext()
    {
        $templateResolver = new TemplateResolver(
            array(),
            array('view' => array('context' => array()))
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

        $matcherMock = $this->getMock(MatcherInterface::class);
        $matcherMock
            ->expects($this->once())
            ->method('setConfig')
            ->with($this->equalTo(array('title')));
        $matcherMock
            ->expects($this->once())
            ->method('match')
            ->with($this->equalTo($view))
            ->will($this->returnValue(false));

        $viewConfiguration = array(
            'view' => array(
                'context' => array(
                    'title' => array(
                        'match' => array(
                            'definition_identifier' => 'title',
                        ),
                    ),
                ),
            )
        );

        $templateResolver = new TemplateResolver(
            array(
                'definition_identifier' => $matcherMock,
            ),
            $viewConfiguration
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
        $viewConfiguration = array(
            'view' => array(
                'context' => array(
                    'title' => array(
                        'match' => array(
                            'definition_identifier' => 'title',
                        ),
                    ),
                ),
            )
        );

        $templateResolver = new TemplateResolver(
            array(),
            $viewConfiguration
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

        $matcherMock = $this->getMock(DateTime::class);

        $viewConfiguration = array(
            'view' => array(
                'context' => array(
                    'title' => array(
                        'match' => array(
                            'definition_identifier' => 'title',
                        ),
                    ),
                ),
            )
        );

        $templateResolver = new TemplateResolver(
            array(
                'definition_identifier' => $matcherMock,
            ),
            $viewConfiguration
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
