<?php

namespace Netgen\BlockManager\Tests\Form;

use Netgen\BlockManager\Form\DateTimeType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Netgen\BlockManager\Utils\DateTimeUtils;
use Symfony\Bridge\PhpUnit\ClockMock;

final class DateTimeTypeTest extends FormTestCase
{
    public static function setUpBeforeClass()
    {
        ClockMock::register(DateTimeUtils::class);
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new DateTimeType();
    }

    /**
     * @covers \Netgen\BlockManager\Form\DateTimeType::buildForm
     * @covers \Netgen\BlockManager\Form\DateTimeType::getTimeZoneList
     */
    public function testSubmitValidData()
    {
        $submittedData = [
            'datetime' => '2018-03-31T01:00',
            'timezone' => 'Antarctica/Casey',
        ];

        $processedData = [
            'datetime' => '2018-03-31 01:00:00',
            'timezone' => 'Antarctica/Casey',
        ];

        $form = $this->factory->create(DateTimeType::class);

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
     * @covers \Netgen\BlockManager\Form\DateTimeType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        $this->assertEquals('ngbm_datetime', $this->formType->getBlockPrefix());
    }
}
