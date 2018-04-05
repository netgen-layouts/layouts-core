<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\ConditionType\Type;

use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Type\TimeType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;

final class TimeTypeTest extends FormTestCase
{
    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new TimeType();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Type\TimeType::buildForm
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'from' => array(
                'datetime' => '2018-03-31T01:00',
                'timezone' => 'Antarctica/Casey',
            ),
            'to' => array(
                'datetime' => '2018-03-31T02:00',
                'timezone' => 'Antarctica/Casey',
            ),
        );

        $processedData = array(
            'from' => array(
                'datetime' => '2018-03-31 01:00:00',
                'timezone' => 'Antarctica/Casey',
            ),
            'to' => array(
                'datetime' => '2018-03-31 02:00:00',
                'timezone' => 'Antarctica/Casey',
            ),
        );

        $form = $this->factory->create(TimeType::class);

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($processedData, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Type\TimeType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        $this->assertEquals('ngbm_condition_type_time', $this->formType->getBlockPrefix());
    }
}
