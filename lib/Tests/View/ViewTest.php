<?php

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Tests\View\Stubs\View;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\View::setContext
     * @covers \Netgen\BlockManager\View\View::getContext
     */
    public function testSetContext()
    {
        $view = new View();
        $view->setContext('context');

        self::assertEquals('context', $view->getContext());
    }

    /**
     * @covers \Netgen\BlockManager\View\View::setTemplate
     * @covers \Netgen\BlockManager\View\View::getTemplate
     */
    public function testSetTemplate()
    {
        $view = new View();
        $view->setTemplate('template.html.twig');

        self::assertEquals('template.html.twig', $view->getTemplate());
    }

    /**
     * @covers \Netgen\BlockManager\View\View::hasParameter
     */
    public function testHasParameter()
    {
        $view = new View();
        $view->setParameters(array('param' => 'value'));

        self::assertEquals(true, $view->hasParameter('param'));
    }

    /**
     * @covers \Netgen\BlockManager\View\View::hasParameter
     */
    public function testHasParameterWithNoParam()
    {
        $view = new View();
        $view->setParameters(array('param' => 'value'));

        self::assertEquals(false, $view->hasParameter('other_param'));
    }

    /**
     * @covers \Netgen\BlockManager\View\View::getParameter
     */
    public function testGetParameter()
    {
        $view = new View();
        $view->setParameters(array('param' => 'value'));

        self::assertEquals('value', $view->getParameter('param'));
    }

    /**
     * @covers \Netgen\BlockManager\View\View::getParameter
     */
    public function testGetParameterWithNoParam()
    {
        $view = new View();
        $view->setParameters(array('param' => 'value'));

        self::assertEquals(null, $view->getParameter('other_param'));
    }

    /**
     * @covers \Netgen\BlockManager\View\View::setParameters
     * @covers \Netgen\BlockManager\View\View::getParameters
     */
    public function testSetParameters()
    {
        $view = new View();
        $view->setParameters(
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
            $view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View::addParameters
     */
    public function testAddParameters()
    {
        $view = new View();
        $view->setParameters(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            )
        );

        $view->addParameters(
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
            $view->getParameters()
        );
    }
}
