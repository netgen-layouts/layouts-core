<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\TargetType;

use Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper\RequestUriPrefix as RequestUriPrefixMapper;
use Netgen\BlockManager\Layout\Resolver\TargetType\RequestUriPrefix;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RequestUriPrefixTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface
     */
    protected $targetType;

    public function setUp()
    {
        parent::setUp();

        $this->targetType = new RequestUriPrefix();
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new TargetType(
            array(
                'request_uri_prefix' => new RequestUriPrefixMapper(),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::buildForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper::mapOptions
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper::handleForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper\RequestUriPrefix::getFormType
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper\RequestUriPrefix::mapOptions
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'value' => '/some/route?id=42',
        );

        $updatedStruct = new TargetCreateStruct();
        $updatedStruct->value = '/some/route?id=42';

        $form = $this->factory->create(
            TargetType::class,
            new TargetCreateStruct(),
            array('targetType' => $this->targetType)
        );

        $valueFormConfig = $form->get('value')->getConfig();
        $this->assertInstanceOf(TextType::class, $valueFormConfig->getType()->getInnerType());

        $form->submit($submittedData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($updatedStruct, $form->getData());

        $this->assertArrayHasKey('value', $form->createView()->children);
    }
}
