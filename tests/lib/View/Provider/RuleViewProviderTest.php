<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Provider;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\View\Provider\RuleViewProvider;
use Netgen\Layouts\View\View\RuleViewInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(RuleViewProvider::class)]
final class RuleViewProviderTest extends TestCase
{
    private RuleViewProvider $ruleViewProvider;

    protected function setUp(): void
    {
        $this->ruleViewProvider = new RuleViewProvider();
    }

    public function testProvideView(): void
    {
        $rule = Rule::fromArray(['id' => Uuid::uuid4()]);

        $view = $this->ruleViewProvider->provideView($rule);

        self::assertInstanceOf(RuleViewInterface::class, $view);

        self::assertSame($rule, $view->rule);
        self::assertNull($view->template);
        self::assertSame(
            [
                'rule' => $rule,
            ],
            $view->parameters,
        );
    }

    #[DataProvider('supportsDataProvider')]
    public function testSupports(mixed $value, bool $supports): void
    {
        self::assertSame($supports, $this->ruleViewProvider->supports($value));
    }

    public static function supportsDataProvider(): iterable
    {
        return [
            [new Value(), false],
            [new Block(), false],
            [new Rule(), true],
        ];
    }
}
