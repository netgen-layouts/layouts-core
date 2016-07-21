<?php

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\TemplateResolver;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\MatcherInterface;
use DateTime;
use PHPUnit\Framework\TestCase;

class TemplateResolverTest extends TestCase
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
     * @expectedException \RuntimeException
     */
    public function testConstructorThrowsRuntimeExceptionIfNoMatcherInterface()
    {
        $templateResolver = new TemplateResolver(
            array(
                'definition_identifier' => $this->createMock(DateTime::class),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::__construct
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     */
    public function testResolveTemplate()
    {
        $matcherMock = $this->createMock(MatcherInterface::class);
        $matcherMock
            ->expects($this->once())
            ->method('match')
            ->with($this->equalTo($this->view), $this->equalTo(array('text')))
            ->will($this->returnValue(true));

        $viewConfiguration = array(
            'view' => array(
                'context' => array(
                    'text' => array(
                        'template' => 'some_template.html.twig',
                        'match' => array(
                            'definition_identifier' => 'text',
                        ),
                    ),
                ),
            ),
        );

        $templateResolver = new TemplateResolver(
            array(
                'definition_identifier' => $matcherMock,
            ),
            $viewConfiguration
        );

        $this->assertEquals('some_template.html.twig', $templateResolver->resolveTemplate($this->view));
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     */
    public function testResolveTemplateWithEmptyMatchConfig()
    {
        $viewConfiguration = array(
            'view' => array(
                'context' => array(
                    'text' => array(
                        'template' => 'some_template.html.twig',
                        'match' => array(),
                    ),
                ),
            ),
        );

        $templateResolver = new TemplateResolver(
            array(),
            $viewConfiguration
        );

        $this->assertEquals('some_template.html.twig', $templateResolver->resolveTemplate($this->view));
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     */
    public function testResolveTemplateWithMultipleMatches()
    {
        $viewConfiguration = array(
            'view' => array(
                'context' => array(
                    'text' => array(
                        'template' => 'some_template.html.twig',
                        'match' => array(),
                    ),
                    'text_other' => array(
                        'template' => 'some_other_template.html.twig',
                        'match' => array(),
                    ),
                ),
            ),
        );

        $templateResolver = new TemplateResolver(
            array(),
            $viewConfiguration
        );

        $this->assertEquals('some_template.html.twig', $templateResolver->resolveTemplate($this->view));
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
        $matcherMock = $this->createMock(MatcherInterface::class);
        $matcherMock
            ->expects($this->once())
            ->method('match')
            ->with($this->equalTo($this->view), $this->equalTo(array('title')))
            ->will($this->returnValue(false));

        $viewConfiguration = array(
            'view' => array(
                'context' => array(
                    'title' => array(
                        'template' => 'some_template.html.twig',
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
            $viewConfiguration
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
        $viewConfiguration = array(
            'view' => array(
                'context' => array(
                    'title' => array(
                        'template' => 'some_template.html.twig',
                        'match' => array(
                            'definition_identifier' => 'title',
                        ),
                    ),
                ),
            ),
        );

        $templateResolver = new TemplateResolver(
            array(),
            $viewConfiguration
        );

        $templateResolver->resolveTemplate($this->view);
    }
}
