<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Provider;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\View\Provider\ParameterViewProvider;
use Netgen\Layouts\View\ViewInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ParameterViewProvider::class)]
final class ParameterViewProviderTest extends TestCase
{
    private ParameterViewProvider $parameterViewProvider;

    protected function setUp(): void
    {
        $this->parameterViewProvider = new ParameterViewProvider();
    }

    public function testProvideView(): void
    {
        $parameter = Parameter::fromArray(['value' => 42]);

        $view = $this->parameterViewProvider->provideView($parameter);

        self::assertSame($parameter, $view->parameterValue);
        self::assertSame(ViewInterface::CONTEXT_DEFAULT, $view->fallbackContext);
        self::assertNull($view->template);
        self::assertSame(
            [
                'parameter' => $parameter,
            ],
            $view->parameters,
        );
    }

    #[DataProvider('supportsDataProvider')]
    public function testSupports(object $value, bool $supports): void
    {
        self::assertSame($supports, $this->parameterViewProvider->supports($value));
    }

    /**
     * @return iterable<mixed>
     */
    public static function supportsDataProvider(): iterable
    {
        return [
            [new Value(), false],
            [new Block(), false],
            [new Parameter(), true],
        ];
    }
}
