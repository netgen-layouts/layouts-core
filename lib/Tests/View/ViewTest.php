<?php

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Tests\View\Stubs\View;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\View\ViewInterface
     */
    protected $view;

    public function setUp()
    {
        $this->view = new View();
    }

    /**
     * @covers \Netgen\BlockManager\View\View::setContext
     * @covers \Netgen\BlockManager\View\View::getContext
     */
    public function testSetContext()
    {
        $this->view->setContext('context');

        self::assertEquals('context', $this->view->getContext());
    }

    /**
     * @covers \Netgen\BlockManager\View\View::setTemplate
     * @covers \Netgen\BlockManager\View\View::getTemplate
     */
    public function testSetTemplate()
    {
        $this->view->setTemplate('template.html.twig');

        self::assertEquals('template.html.twig', $this->view->getTemplate());
    }

    /**
     * @covers \Netgen\BlockManager\View\View::hasParameter
     */
    public function testHasParameter()
    {
        $this->view->setParameters(array('param' => 'value'));

        self::assertTrue($this->view->hasParameter('param'));
    }

    /**
     * @covers \Netgen\BlockManager\View\View::hasParameter
     */
    public function testHasParameterWithNoParam()
    {
        $this->view->setParameters(array('param' => 'value'));

        self::assertFalse($this->view->hasParameter('other_param'));
    }

    /**
     * @covers \Netgen\BlockManager\View\View::getParameter
     */
    public function testGetParameter()
    {
        $this->view->setParameters(array('param' => 'value'));

        self::assertEquals('value', $this->view->getParameter('param'));
    }

    /**
     * @covers \Netgen\BlockManager\View\View::getParameter
     * @expectedException \RuntimeException
     */
    public function testGetParameterThrowsRuntimeException()
    {
        $this->view->setParameters(array('param' => 'value'));

        $this->view->getParameter('other_param');
    }

    /**
     * @covers \Netgen\BlockManager\View\View::setParameters
     * @covers \Netgen\BlockManager\View\View::getParameters
     */
    public function testSetParameters()
    {
        $this->view->setParameters(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            )
        );

        self::assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View::addParameters
     */
    public function testAddParameters()
    {
        $this->view->setParameters(
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

        self::assertEquals(
            array(
                'some_param' => 'new_value',
                'some_other_param' => 'some_other_value',
                'third_param' => 'third_value',
            ),
            $this->view->getParameters()
        );
    }
}
