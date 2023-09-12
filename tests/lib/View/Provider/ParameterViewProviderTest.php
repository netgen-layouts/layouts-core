<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Provider;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\View\Provider\ParameterViewProvider;
use Netgen\Layouts\View\View\ParameterViewInterface;
use Netgen\Layouts\View\ViewInterface;
use PHPUnit\Framework\TestCase;

final class ParameterViewProviderTest extends TestCase
{
    private ParameterViewProvider $parameterViewProvider;

    protected function setUp(): void
    {
        $this->parameterViewProvider = new ParameterViewProvider();
    }

    /**
     * @covers \Netgen\Layouts\View\Provider\ParameterViewProvider::provideView
     */
    public function testProvideView(): void
    {
        $parameter = Parameter::fromArray(['value' => 42]);

        $view = $this->parameterViewProvider->provideView($parameter);

        self::assertInstanceOf(ParameterViewInterface::class, $view);

        self::assertSame($parameter, $view->getParameterValue());
        self::assertSame(ViewInterface::CONTEXT_DEFAULT, $view->getFallbackContext());
        self::assertNull($view->getTemplate());
        self::assertSame(
            [
                'parameter' => $parameter,
            ],
            $view->getParameters(),
        );
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\View\Provider\ParameterViewProvider::supports
     *
     * @dataProvider supportsDataProvider
     */
    public function testSupports($value, bool $supports): void
    {
        self::assertSame($supports, $this->parameterViewProvider->supports($value));
    }

    public static function supportsDataProvider(): iterable
    {
        return [
            [new Value(), false],
            [new Block(), false],
            [new Parameter(), true],
        ];
    }
}
