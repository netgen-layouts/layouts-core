<?php

namespace Netgen\BlockManager\Tests\View;

use DateTime;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\Tests\View\Stubs\ViewWithFallbackContext;
use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\TemplateResolver;
use PHPUnit\Framework\TestCase;

class TemplateResolverTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\ViewInterface
     */
    protected $view;

    public function setUp()
    {
        $this->view = new View(array('value' => new Value()));
        $this->view->setContext('context');
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::__construct
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Template matcher "DateTime" needs to implement MatcherInterface.
     */
    public function testConstructorThrowsRuntimeExceptionIfNoMatcherInterface()
    {
        new TemplateResolver(
            array(
                'definition_identifier' => new DateTime(),
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
                        'parameters' => array(
                            'param' => 'value',
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

        $this->assertEquals('some_template.html.twig', $this->view->getTemplate());
        $this->assertTrue($this->view->hasParameter('param'));
        $this->assertEquals('value', $this->view->getParameter('param'));
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
                        'parameters' => array(
                            'param' => 'value',
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

        $this->assertEquals('some_template.html.twig', $this->view->getTemplate());
        $this->assertTrue($this->view->hasParameter('param'));
        $this->assertEquals('value', $this->view->getParameter('param'));
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
                        'parameters' => array(),
                    ),
                    'text_other' => array(
                        'template' => 'some_other_template.html.twig',
                        'match' => array(),
                        'parameters' => array(),
                    ),
                ),
            ),
        );

        $templateResolver = new TemplateResolver(
            array(),
            $viewConfiguration
        );

        $templateResolver->resolveTemplate($this->view);

        $this->assertEquals('some_template.html.twig', $this->view->getTemplate());
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     */
    public function testResolveTemplateWithFallbackContext()
    {
        $this->view = new ViewWithFallbackContext(
            'fallback',
            array(
                'value' => new Value(),
            )
        );

        $this->view->setContext('context');

        $viewConfiguration = array(
            'view' => array(
                'fallback' => array(
                    'text' => array(
                        'template' => 'some_template.html.twig',
                        'match' => array(),
                        'parameters' => array(),
                    ),
                ),
            ),
        );

        $templateResolver = new TemplateResolver(
            array(),
            $viewConfiguration
        );

        $templateResolver->resolveTemplate($this->view);

        $this->assertEquals('some_template.html.twig', $this->view->getTemplate());
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage View context expected to be of string type, got integer.
     */
    public function testResolveTemplateThrowsRuntimeExceptionIfInvalidContext()
    {
        $this->view->setContext(42);
        $templateResolver = new TemplateResolver();
        $templateResolver->resolveTemplate($this->view);
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage No template match could be found for "view" view and context "context".
     */
    public function testResolveTemplateThrowsRuntimeExceptionIfNoContext()
    {
        $templateResolver = new TemplateResolver(array(), array('view' => array()));
        $templateResolver->resolveTemplate($this->view);
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage No template match could be found for "view" view and context "context".
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
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage No template match could be found for "view" view and context "context".
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
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage No template matcher could be found with identifier "definition_identifier".
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
