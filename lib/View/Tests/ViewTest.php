<?php

namespace Netgen\BlockManager\View\Tests;

use Netgen\BlockManager\View\Tests\Stubs\View;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\View::setContext
     * @covers \Netgen\BlockManager\View\View::getContext
     */
    public function testSetContext()
    {
        $view = new View();
        $view->setContext('api');

        self::assertEquals('api', $view->getContext());
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
