<?php

namespace Netgen\BlockManager\Tests\Block\Form;

use Netgen\BlockManager\Layout\Form\CopyType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;

class CopyTypeTest extends FormTestCase
{
    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new CopyType();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\CopyType::buildForm
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'name' => 'New name',
        );
        $form = $this->factory->create(
            CopyType::class,
            array(
                'name' => 'Original name',
            )
        );

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals(array('name' => 'New name'), $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
