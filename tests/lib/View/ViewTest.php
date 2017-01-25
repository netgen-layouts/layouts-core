<?php

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\ViewInterface
     */
    protected $view;

    public function setUp()
    {
        $this->view = new View(array('value' => new Value()));
    }

    /**
     * @covers \Netgen\BlockManager\View\View::setContext
     * @covers \Netgen\BlockManager\View\View::getContext
     */
    public function testSetContext()
    {
        $this->view->setContext('context');

        $this->assertEquals('context', $this->view->getContext());
    }

    /**
     * @covers \Netgen\BlockManager\View\View::setTemplate
     * @covers \Netgen\BlockManager\View\View::getTemplate
     */
    public function testSetTemplate()
    {
        $this->view->setTemplate('template.html.twig');

        $this->assertEquals('template.html.twig', $this->view->getTemplate());
    }

    /**
     * @covers \Netgen\BlockManager\View\View::getFallbackContext
     */
    public function testGetFallbackContext()
    {
        $this->assertNull($this->view->getFallbackContext());
    }

    /**
     * @covers \Netgen\BlockManager\View\View::getResponse
     */
    public function testGetDefaultResponse()
    {
        $this->assertEquals(new Response(), $this->view->getResponse());
    }

    /**
     * @covers \Netgen\BlockManager\View\View::setResponse
     * @covers \Netgen\BlockManager\View\View::getResponse
     */
    public function testSetResponse()
    {
        $response = new Response('response');

        $this->view->setResponse($response);

        $this->assertEquals($response, $this->view->getResponse());
    }

    /**
     * @covers \Netgen\BlockManager\View\View::addParameter
     * @covers \Netgen\BlockManager\View\View::hasParameter
     */
    public function testHasParameter()
    {
        $this->view->addParameter('param', 'value');

        $this->assertTrue($this->view->hasParameter('param'));
    }

    /**
     * @covers \Netgen\BlockManager\View\View::addParameter
     * @covers \Netgen\BlockManager\View\View::hasParameter
     */
    public function testHasParameterWithNoParam()
    {
        $this->view->addParameter('param', 'value');

        $this->assertFalse($this->view->hasParameter('other_param'));
    }

    /**
     * @covers \Netgen\BlockManager\View\View::addParameter
     * @covers \Netgen\BlockManager\View\View::getParameter
     */
    public function testGetParameter()
    {
        $this->view->addParameter('param', 'value');

        $this->assertEquals('value', $this->view->getParameter('param'));
    }

    /**
     * @covers \Netgen\BlockManager\View\View::addParameter
     * @covers \Netgen\BlockManager\View\View::getParameter
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetParameterThrowsOutOfBoundsException()
    {
        $this->view->addParameter('param', 'value');

        $this->view->getParameter('other_param');
    }

    /**
     * @covers \Netgen\BlockManager\View\View::addParameters
     */
    public function testAddParameters()
    {
        $this->view->addParameters(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            )
        );

        $this->view->addParameters(
            array(
                'some_param' => 'new_value',
                'third_param' => 'third_value',
            )
        );

        $this->assertEquals(
            array(
                'some_param' => 'new_value',
                'some_other_param' => 'some_other_value',
                'third_param' => 'third_value',
                'value' => new Value(),
            ),
            $this->view->getParameters()
        );
    }
}