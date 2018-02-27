<?php

namespace Netgen\BlockManager\Tests\View;

use DateTime;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\TemplateResolver;
use PHPUnit\Framework\TestCase;

final class TemplateResolverTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\ViewInterface
     */
    private $view;

    public function setUp()
    {
        $this->view = new View(array('value' => new Value()));
        $this->view->setContext('context');
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::__construct
     * @expectedException \Netgen\BlockManager\Exception\InvalidInterfaceException
     * @expectedExceptionMessage Template matcher "DateTime" needs to implement "Netgen\BlockManager\View\Matcher\MatcherInterface" interface.
     */
    public function testConstructorThrowsInvalidInterfaceExceptionIfNoMatcherInterface()
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
     * @covers \Netgen\BlockManager\View\TemplateResolver::evaluateParameters
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
                            'param2' => '@=value',
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

        $this->assertTrue($this->view->hasParameter('param2'));
        $this->assertEquals(new Value(), $this->view->getParameter('param2'));
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     * @covers \Netgen\BlockManager\View\TemplateResolver::evaluateParameters
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
     * @covers \Netgen\BlockManager\View\TemplateResolver::evaluateParameters
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
     * @covers \Netgen\BlockManager\View\TemplateResolver::evaluateParameters
     */
    public function testResolveTemplateWithFallbackContext()
    {
        $this->view->setContext('context');
        $this->view->setFallbackContext('fallback');

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
     * @expectedException \Netgen\BlockManager\Exception\View\TemplateResolverException
     * @expectedExceptionMessage No template match could be found for "view" view and context "context".
     */
    public function testResolveTemplateThrowsTemplateResolverExceptionIfNoContext()
    {
        $templateResolver = new TemplateResolver(array(), array('view' => array()));
        $templateResolver->resolveTemplate($this->view);
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @expectedException \Netgen\BlockManager\Exception\View\TemplateResolverException
     * @expectedExceptionMessage No template match could be found for "view" view and context "context".
     */
    public function testResolveTemplateThrowsTemplateResolverExceptionIfEmptyContext()
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
     * @expectedException \Netgen\BlockManager\Exception\View\TemplateResolverException
     * @expectedExceptionMessage No template match could be found for "view" view and context "context".
     */
    public function testResolveTemplateThrowsTemplateResolverExceptionIfNoMatch()
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
     * @expectedException \Netgen\BlockManager\Exception\View\TemplateResolverException
     * @expectedExceptionMessage No template matcher could be found with identifier "definition_identifier".
     */
    public function testResolveTemplateThrowsTemplateResolverExceptionIfNoMatcher()
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
