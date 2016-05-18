<?php

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\TemplateResolver;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\MatcherInterface;
use DateTime;

class TemplateResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\View\ViewInterface
     */
    protected $view;

    public function setUp()
    {
        $this->view = new View(new Value());
        $this->view->setContext('context');
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::__construct
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     */
    public function testResolveTemplate()
    {
        $matcherMock = $this->getMock(MatcherInterface::class);
        $matcherMock
            ->expects($this->once())
            ->method('setConfig')
            ->with($this->equalTo(array('paragraph')));
        $matcherMock
            ->expects($this->once())
            ->method('match')
            ->with($this->equalTo($this->view))
            ->will($this->returnValue(true));

        $this->viewConfiguration = array(
            'view' => array(
                'context' => array(
                    'paragraph' => array(
                        'template' => 'some_template.html.twig',
                        'match' => array(
                            'definition_identifier' => 'paragraph',
                        ),
                    ),
                ),
            ),
        );

        $templateResolver = new TemplateResolver(
            array(
                'definition_identifier' => $matcherMock,
            ),
            $this->viewConfiguration
        );

        self::assertEquals('some_template.html.twig', $templateResolver->resolveTemplate($this->view));
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::__construct
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     */
    public function testResolveTemplateWithEmptyMatchConfig()
    {
        $this->viewConfiguration = array(
            'view' => array(
                'context' => array(
                    'paragraph' => array(
                        'template' => 'some_template.html.twig',
                        'match' => array(),
                    ),
                ),
            ),
        );

        $templateResolver = new TemplateResolver(
            array(),
            $this->viewConfiguration
        );

        self::assertEquals('some_template.html.twig', $templateResolver->resolveTemplate($this->view));
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::__construct
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     */
    public function testResolveTemplateWithMultipleMatches()
    {
        $this->viewConfiguration = array(
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
            ),
        );

        $templateResolver = new TemplateResolver(
            array(),
            $this->viewConfiguration
        );

        self::assertEquals('some_template.html.twig', $templateResolver->resolveTemplate($this->view));
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @expectedException \RuntimeException
     */
    public function testResolveTemplateThrowsRuntimeExceptionIfNoContext()
    {
        $templateResolver = new TemplateResolver(array(), array('view' => array()));
        $templateResolver->resolveTemplate($this->view);
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

        $templateResolver->resolveTemplate($this->view);
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     * @expectedException \RuntimeException
     */
    public function testResolveTemplateThrowsRuntimeExceptionIfNoMatch()
    {
        $matcherMock = $this->getMock(MatcherInterface::class);
        $matcherMock
            ->expects($this->once())
            ->method('setConfig')
            ->with($this->equalTo(array('title')));
        $matcherMock
            ->expects($this->once())
            ->method('match')
            ->with($this->equalTo($this->view))
            ->will($this->returnValue(false));

        $this->viewConfiguration = array(
            'view' => array(
                'context' => array(
                    'title' => array(
                        'match' => array(
                            'definition_identifier' => 'title',
                        ),
                    ),
                ),
            ),
        );

        $templateResolver = new TemplateResolver(
            array(
                'definition_identifier' => $matcherMock,
            ),
            $this->viewConfiguration
        );

        $templateResolver->resolveTemplate($this->view);
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     * @expectedException \RuntimeException
     */
    public function testResolveTemplateThrowsRuntimeExceptionIfNoMatcher()
    {
        $this->viewConfiguration = array(
            'view' => array(
                'context' => array(
                    'title' => array(
                        'match' => array(
                            'definition_identifier' => 'title',
                        ),
                    ),
                ),
            ),
        );

        $templateResolver = new TemplateResolver(
            array(),
            $this->viewConfiguration
        );

        $templateResolver->resolveTemplate($this->view);
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     * @expectedException \RuntimeException
     */
    public function testResolveTemplateThrowsRuntimeExceptionIfNoMatcherInterface()
    {
        $matcherMock = $this->getMock(DateTime::class);

        $this->viewConfiguration = array(
            'view' => array(
                'context' => array(
                    'title' => array(
                        'match' => array(
                            'definition_identifier' => 'title',
                        ),
                    ),
                ),
            ),
        );

        $templateResolver = new TemplateResolver(
            array(
                'definition_identifier' => $matcherMock,
            ),
            $this->viewConfiguration
        );

        $templateResolver->resolveTemplate($this->view);
    }
}
