<?php

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Parameters\ParameterValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\Provider\ParameterViewProvider;
use Netgen\BlockManager\View\View\ParameterViewInterface;
use Netgen\BlockManager\View\ViewInterface;
use PHPUnit\Framework\TestCase;

class ParameterViewProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    private $parameterViewProvider;

    public function setUp()
    {
        $this->parameterViewProvider = new ParameterViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\ParameterViewProvider::provideView
     */
    public function testProvideView()
    {
        $parameterValue = new ParameterValue(array('value' => 42));

        /** @var \Netgen\BlockManager\View\View\ParameterViewInterface $view */
        $view = $this->parameterViewProvider->provideView($parameterValue);

        $this->assertInstanceOf(ParameterViewInterface::class, $view);

        $this->assertEquals($parameterValue, $view->getParameterValue());
        $this->assertEquals(ViewInterface::CONTEXT_DEFAULT, $view->getFallbackContext());
        $this->assertNull($view->getTemplate());
        $this->assertEquals(
            array(
                'parameter' => $parameterValue,
            ),
            $view->getParameters()
        );
    }

    /**
     * @param \Netgen\BlockManager\API\Values\Value $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\ParameterViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, $supports)
    {
        $this->assertEquals($supports, $this->parameterViewProvider->supports($value));
    }

    /**
     * Provider for {@link self::testSupports}.
     *
     * @return array
     */
    public function supportsProvider()
    {
        return array(
            array(new Value(), false),
            array(new Block(), false),
            array(new ParameterValue(), true),
        );
    }
}
