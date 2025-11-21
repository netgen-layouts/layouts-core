<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View;

use Netgen\Bundle\LayoutsBundle\Configuration\ConfigurationInterface;
use Netgen\Layouts\Exception\View\TemplateResolverException;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\Stubs\Container;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\MatcherInterface;
use Netgen\Layouts\View\TemplateResolver;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(TemplateResolver::class)]
final class TemplateResolverTest extends TestCase
{
    private MockObject&ConfigurationInterface $configMock;

    private View $view;

    private Value $value;

    protected function setUp(): void
    {
        $this->configMock = $this->createMock(ConfigurationInterface::class);

        $this->value = new Value();

        $this->view = new View($this->value);
        $this->view->setContext('context');
    }

    public function testResolveTemplate(): void
    {
        $matcherMock = $this->createMock(MatcherInterface::class);

        $matcherMock
            ->method('match')
            ->willReturnMap(
                [
                    [$this->view, ['value'], false],
                    [$this->view, ['value2'], true],
                ],
            );

        $viewConfiguration = [
            'stub_view' => [
                'context' => [
                    'value' => [
                        'template' => 'template.html.twig',
                        'match' => [
                            'matcher' => 'value',
                        ],
                        'parameters' => [
                            'param' => 'value',
                            'param2' => '@=value',
                        ],
                    ],
                    'value2' => [
                        'template' => 'template2.html.twig',
                        'match' => [
                            'matcher' => 'value2',
                        ],
                        'parameters' => [
                            'param' => 'value2',
                            'param2' => '@=value',
                        ],
                    ],
                ],
            ],
        ];

        $this->configMock
            ->method('getParameter')
            ->with(self::identicalTo('view'))
            ->willReturn($viewConfiguration);

        $templateResolver = new TemplateResolver(
            $this->configMock,
            new Container(
                [
                    'matcher' => $matcherMock,
                ],
            ),
        );

        $templateResolver->resolveTemplate($this->view);

        self::assertSame('template2.html.twig', $this->view->getTemplate());

        self::assertTrue($this->view->hasParameter('param'));
        self::assertSame('value2', $this->view->getParameter('param'));

        self::assertTrue($this->view->hasParameter('param2'));
        self::assertSame($this->value, $this->view->getParameter('param2'));
    }

    public function testResolveTemplateWithEmptyMatchConfig(): void
    {
        $viewConfiguration = [
            'stub_view' => [
                'context' => [
                    'value' => [
                        'template' => 'template.html.twig',
                        'match' => [],
                        'parameters' => [
                            'param' => 'value',
                        ],
                    ],
                ],
            ],
        ];

        $this->configMock
            ->method('getParameter')
            ->with(self::identicalTo('view'))
            ->willReturn($viewConfiguration);

        $templateResolver = new TemplateResolver(
            $this->configMock,
            new Container(),
        );

        $templateResolver->resolveTemplate($this->view);

        self::assertSame('template.html.twig', $this->view->getTemplate());
        self::assertTrue($this->view->hasParameter('param'));
        self::assertSame('value', $this->view->getParameter('param'));
    }

    public function testResolveTemplateWithMultipleMatches(): void
    {
        $viewConfiguration = [
            'stub_view' => [
                'context' => [
                    'value' => [
                        'template' => 'template.html.twig',
                        'match' => [],
                        'parameters' => [],
                    ],
                    'text_other' => [
                        'template' => 'template2.html.twig',
                        'match' => [],
                        'parameters' => [],
                    ],
                ],
            ],
        ];

        $this->configMock
            ->method('getParameter')
            ->with(self::identicalTo('view'))
            ->willReturn($viewConfiguration);

        $templateResolver = new TemplateResolver(
            $this->configMock,
            new Container(),
        );

        $templateResolver->resolveTemplate($this->view);

        self::assertSame('template.html.twig', $this->view->getTemplate());
    }

    public function testResolveTemplateWithFallbackContext(): void
    {
        $this->view->setContext('context');
        $this->view->setFallbackContext('fallback');

        $viewConfiguration = [
            'stub_view' => [
                'fallback' => [
                    'value' => [
                        'template' => 'template.html.twig',
                        'match' => [],
                        'parameters' => [],
                    ],
                ],
            ],
        ];

        $this->configMock
            ->method('getParameter')
            ->with(self::identicalTo('view'))
            ->willReturn($viewConfiguration);

        $templateResolver = new TemplateResolver(
            $this->configMock,
            new Container(),
        );

        $templateResolver->resolveTemplate($this->view);

        self::assertSame('template.html.twig', $this->view->getTemplate());
    }

    public function testResolveTemplateThrowsTemplateResolverExceptionIfNoContext(): void
    {
        $this->expectException(TemplateResolverException::class);
        $this->expectExceptionMessage('No template match could be found for "stub_view" view and context "context".');

        $viewConfiguration = ['stub_view' => []];

        $this->configMock
            ->method('getParameter')
            ->with(self::identicalTo('view'))
            ->willReturn($viewConfiguration);

        $templateResolver = new TemplateResolver(
            $this->configMock,
            new Container(),
        );

        $templateResolver->resolveTemplate($this->view);
    }

    public function testResolveTemplateThrowsTemplateResolverExceptionIfEmptyContext(): void
    {
        $this->expectException(TemplateResolverException::class);
        $this->expectExceptionMessage('No template match could be found for "stub_view" view and context "context".');

        $viewConfiguration = ['stub_view' => ['context' => []]];

        $this->configMock
            ->method('getParameter')
            ->with(self::identicalTo('view'))
            ->willReturn($viewConfiguration);

        $templateResolver = new TemplateResolver(
            $this->configMock,
            new Container(),
        );

        $templateResolver->resolveTemplate($this->view);
    }

    public function testResolveTemplateThrowsTemplateResolverExceptionIfNoMatch(): void
    {
        $this->expectException(TemplateResolverException::class);
        $this->expectExceptionMessage('No template match could be found for "stub_view" view and context "context".');

        $matcherMock = $this->createMock(MatcherInterface::class);
        $matcherMock
            ->expects(self::once())
            ->method('match')
            ->with(self::identicalTo($this->view), self::identicalTo(['value']))
            ->willReturn(false);

        $viewConfiguration = [
            'stub_view' => [
                'context' => [
                    'value' => [
                        'template' => 'template.html.twig',
                        'match' => [
                            'matcher' => 'value',
                        ],
                    ],
                ],
            ],
        ];

        $this->configMock
            ->method('getParameter')
            ->with(self::identicalTo('view'))
            ->willReturn($viewConfiguration);

        $templateResolver = new TemplateResolver(
            $this->configMock,
            new Container(
                [
                    'matcher' => $matcherMock,
                ],
            ),
        );

        $templateResolver->resolveTemplate($this->view);
    }

    public function testResolveTemplateThrowsTemplateResolverExceptionIfNoMatcher(): void
    {
        $this->expectException(TemplateResolverException::class);
        $this->expectExceptionMessage('No template matcher could be found with identifier "matcher".');

        $viewConfiguration = [
            'stub_view' => [
                'context' => [
                    'value' => [
                        'template' => 'template.html.twig',
                        'match' => [
                            'matcher' => 'value',
                        ],
                    ],
                ],
            ],
        ];

        $this->configMock
            ->method('getParameter')
            ->with(self::identicalTo('view'))
            ->willReturn($viewConfiguration);

        $templateResolver = new TemplateResolver(
            $this->configMock,
            new Container(),
        );

        $templateResolver->resolveTemplate($this->view);
    }

    public function testResolveTemplateThrowsTemplateResolverExceptionIfInvalidMatcher(): void
    {
        $this->expectException(TemplateResolverException::class);
        $this->expectExceptionMessage('No template matcher could be found with identifier "matcher".');

        $viewConfiguration = [
            'stub_view' => [
                'context' => [
                    'value' => [
                        'template' => 'template.html.twig',
                        'match' => [
                            'matcher' => 'value',
                        ],
                    ],
                ],
            ],
        ];

        $this->configMock
            ->method('getParameter')
            ->with(self::identicalTo('view'))
            ->willReturn($viewConfiguration);

        $templateResolver = new TemplateResolver(
            $this->configMock,
            new Container(
                [
                    'matcher' => new stdClass(),
                ],
            ),
        );

        $templateResolver->resolveTemplate($this->view);
    }
}
