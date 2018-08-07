<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

final class ViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\ViewInterface
     */
    private $view;

    /**
     * @var \Netgen\BlockManager\Tests\Core\Stubs\Value
     */
    private $value;

    public function setUp(): void
    {
        $this->value = new Value();

        $this->view = new View($this->value);
    }

    /**
     * @covers \Netgen\BlockManager\View\View::getContext
     * @covers \Netgen\BlockManager\View\View::setContext
     */
    public function testSetContext(): void
    {
        $this->view->setContext('context');

        self::assertSame('context', $this->view->getContext());
    }

    /**
     * @covers \Netgen\BlockManager\View\View::getTemplate
     * @covers \Netgen\BlockManager\View\View::setTemplate
     */
    public function testSetTemplate(): void
    {
        $this->view->setTemplate('template.html.twig');

        self::assertSame('template.html.twig', $this->view->getTemplate());
    }

    /**
     * @covers \Netgen\BlockManager\View\View::getFallbackContext
     */
    public function testGetFallbackContext(): void
    {
        self::assertNull($this->view->getFallbackContext());
    }

    /**
     * @covers \Netgen\BlockManager\View\View::setFallbackContext
     */
    public function testSetFallbackContext(): void
    {
        $this->view->setFallbackContext('fallback');

        self::assertSame('fallback', $this->view->getFallbackContext());
    }

    /**
     * @covers \Netgen\BlockManager\View\View::getResponse
     */
    public function testGetDefaultResponse(): void
    {
        self::assertNull($this->view->getResponse());
    }

    /**
     * @covers \Netgen\BlockManager\View\View::getResponse
     * @covers \Netgen\BlockManager\View\View::setResponse
     */
    public function testSetResponse(): void
    {
        $response = new Response('response');

        $this->view->setResponse($response);

        self::assertSame($response, $this->view->getResponse());
    }

    /**
     * @covers \Netgen\BlockManager\View\View::addParameter
     * @covers \Netgen\BlockManager\View\View::hasParameter
     */
    public function testHasParameter(): void
    {
        $this->view->addParameter('param', 'value');

        self::assertTrue($this->view->hasParameter('param'));
    }

    /**
     * @covers \Netgen\BlockManager\View\View::addParameter
     * @covers \Netgen\BlockManager\View\View::hasParameter
     */
    public function testHasParameterWithNoParam(): void
    {
        $this->view->addParameter('param', 'value');

        self::assertFalse($this->view->hasParameter('other_param'));
    }

    /**
     * @covers \Netgen\BlockManager\View\View::addParameter
     * @covers \Netgen\BlockManager\View\View::getParameter
     */
    public function testGetParameter(): void
    {
        $this->view->addParameter('param', 'value');

        self::assertSame('value', $this->view->getParameter('param'));
    }

    /**
     * @covers \Netgen\BlockManager\View\View::addParameter
     * @covers \Netgen\BlockManager\View\View::getParameter
     * @expectedException \Netgen\BlockManager\Exception\View\ViewException
     * @expectedExceptionMessage Parameter with "other_param" name was not found in "Netgen\BlockManager\Tests\View\Stubs\View" view.
     */
    public function testGetParameterThrowsViewException(): void
    {
        $this->view->addParameter('param', 'value');

        $this->view->getParameter('other_param');
    }

    /**
     * @covers \Netgen\BlockManager\View\View::addParameters
     */
    public function testAddParameters(): void
    {
        $this->view->addParameters(
            [
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ]
        );

        $this->view->addParameters(
            [
                'some_param' => 'new_value',
                'third_param' => 'third_value',
            ]
        );

        self::assertSame(
            [
                'value' => $this->value,
                'some_param' => 'new_value',
                'third_param' => 'third_value',
                'some_other_param' => 'some_other_value',
            ],
            $this->view->getParameters()
        );
    }
}
