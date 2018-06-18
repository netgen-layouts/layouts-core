<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
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
        $parameter = new Parameter(['value' => 42]);

        /** @var \Netgen\BlockManager\View\View\ParameterViewInterface $view */
        $view = $this->parameterViewProvider->provideView($parameter);

        $this->assertInstanceOf(ParameterViewInterface::class, $view);

        $this->assertSame($parameter, $view->getParameterValue());
        $this->assertSame(ViewInterface::CONTEXT_DEFAULT, $view->getFallbackContext());
        $this->assertNull($view->getTemplate());
        $this->assertSame(
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
        $this->assertSame($supports, $this->parameterViewProvider->supports($value));
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
