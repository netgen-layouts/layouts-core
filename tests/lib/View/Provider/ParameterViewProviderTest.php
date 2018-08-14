<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\View\Provider\ParameterViewProvider;
use Netgen\BlockManager\View\View\ParameterViewInterface;
use Netgen\BlockManager\View\ViewInterface;
use PHPUnit\Framework\TestCase;

final class ParameterViewProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    private $parameterViewProvider;

    public function setUp(): void
    {
        $this->parameterViewProvider = new ParameterViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\ParameterViewProvider::provideView
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
            $view->getParameters()
        );
    }

    /**
     * @param mixed $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\ParameterViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, bool $supports): void
    {
        self::assertSame($supports, $this->parameterViewProvider->supports($value));
    }

    public function supportsProvider(): array
    {
        return [
            [new Value(), false],
            [new Block(), false],
            [new Parameter(), true],
        ];
    }
}
