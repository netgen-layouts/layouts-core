<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View;

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

    public function setUp(): void
    {
        $this->view = new View(['value' => new Value()]);
        $this->view->setContext('context');
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::__construct
     * @covers \Netgen\BlockManager\View\TemplateResolver::evaluateParameters
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     */
    public function testResolveTemplate(): void
    {
        $matcherMock = $this->createMock(MatcherInterface::class);
        $matcherMock
            ->expects($this->once())
            ->method('match')
            ->with($this->equalTo($this->view), $this->equalTo(['text']))
            ->will($this->returnValue(true));

        $viewConfiguration = [
            'view' => [
                'context' => [
                    'text' => [
                        'template' => 'some_template.html.twig',
                        'match' => [
                            'definition_identifier' => 'text',
                        ],
                        'parameters' => [
                            'param' => 'value',
                            'param2' => '@=value',
                        ],
                    ],
                ],
            ],
        ];

        $templateResolver = new TemplateResolver(
            [
                'definition_identifier' => $matcherMock,
            ],
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
     * @covers \Netgen\BlockManager\View\TemplateResolver::evaluateParameters
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     */
    public function testResolveTemplateWithEmptyMatchConfig(): void
    {
        $viewConfiguration = [
            'view' => [
                'context' => [
                    'text' => [
                        'template' => 'some_template.html.twig',
                        'match' => [],
                        'parameters' => [
                            'param' => 'value',
                        ],
                    ],
                ],
            ],
        ];

        $templateResolver = new TemplateResolver(
            [],
            $viewConfiguration
        );

        $templateResolver->resolveTemplate($this->view);

        $this->assertEquals('some_template.html.twig', $this->view->getTemplate());
        $this->assertTrue($this->view->hasParameter('param'));
        $this->assertEquals('value', $this->view->getParameter('param'));
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::evaluateParameters
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     */
    public function testResolveTemplateWithMultipleMatches(): void
    {
        $viewConfiguration = [
            'view' => [
                'context' => [
                    'text' => [
                        'template' => 'some_template.html.twig',
                        'match' => [],
                        'parameters' => [],
                    ],
                    'text_other' => [
                        'template' => 'some_other_template.html.twig',
                        'match' => [],
                        'parameters' => [],
                    ],
                ],
            ],
        ];

        $templateResolver = new TemplateResolver(
            [],
            $viewConfiguration
        );

        $templateResolver->resolveTemplate($this->view);

        $this->assertEquals('some_template.html.twig', $this->view->getTemplate());
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::evaluateParameters
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     */
    public function testResolveTemplateWithFallbackContext(): void
    {
        $this->view->setContext('context');
        $this->view->setFallbackContext('fallback');

        $viewConfiguration = [
            'view' => [
                'fallback' => [
                    'text' => [
                        'template' => 'some_template.html.twig',
                        'match' => [],
                        'parameters' => [],
                    ],
                ],
            ],
        ];

        $templateResolver = new TemplateResolver(
            [],
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
    public function testResolveTemplateThrowsTemplateResolverExceptionIfNoContext(): void
    {
        $templateResolver = new TemplateResolver([], ['view' => []]);
        $templateResolver->resolveTemplate($this->view);
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @expectedException \Netgen\BlockManager\Exception\View\TemplateResolverException
     * @expectedExceptionMessage No template match could be found for "view" view and context "context".
     */
    public function testResolveTemplateThrowsTemplateResolverExceptionIfEmptyContext(): void
    {
        $templateResolver = new TemplateResolver(
            [],
            ['view' => ['context' => []]]
        );

        $templateResolver->resolveTemplate($this->view);
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @expectedException \Netgen\BlockManager\Exception\View\TemplateResolverException
     * @expectedExceptionMessage No template match could be found for "view" view and context "context".
     */
    public function testResolveTemplateThrowsTemplateResolverExceptionIfNoMatch(): void
    {
        $matcherMock = $this->createMock(MatcherInterface::class);
        $matcherMock
            ->expects($this->once())
            ->method('match')
            ->with($this->equalTo($this->view), $this->equalTo(['title']))
            ->will($this->returnValue(false));

        $viewConfiguration = [
            'view' => [
                'context' => [
                    'title' => [
                        'template' => 'some_template.html.twig',
                        'match' => [
                            'definition_identifier' => 'title',
                        ],
                    ],
                ],
            ],
        ];

        $templateResolver = new TemplateResolver(
            [
                'definition_identifier' => $matcherMock,
            ],
            $viewConfiguration
        );

        $templateResolver->resolveTemplate($this->view);
    }

    /**
     * @covers \Netgen\BlockManager\View\TemplateResolver::matches
     * @covers \Netgen\BlockManager\View\TemplateResolver::resolveTemplate
     * @expectedException \Netgen\BlockManager\Exception\View\TemplateResolverException
     * @expectedExceptionMessage No template matcher could be found with identifier "definition_identifier".
     */
    public function testResolveTemplateThrowsTemplateResolverExceptionIfNoMatcher(): void
    {
        $viewConfiguration = [
            'view' => [
                'context' => [
                    'title' => [
                        'template' => 'some_template.html.twig',
                        'match' => [
                            'definition_identifier' => 'title',
                        ],
                    ],
                ],
            ],
        ];

        $templateResolver = new TemplateResolver(
            [],
            $viewConfiguration
        );

        $templateResolver->resolveTemplate($this->view);
    }
}
