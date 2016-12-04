<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Exception\InvalidItemException;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\Parameters\ItemLink;
use Netgen\BlockManager\Validator\Parameters\ItemLinkValidator;
use Symfony\Component\Validator\Constraints\NotBlank;

class ItemLinkValidatorTest extends ValidatorTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemLoaderMock;

    public function setUp()
    {
        $this->constraint = new ItemLink();

        parent::setUp();
    }

    /**
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    public function getValidator()
    {
        $this->itemLoaderMock = $this->createMock(ItemLoaderInterface::class);

        return new ItemLinkValidator($this->itemLoaderMock);
    }

    /**
     * @param string $value
     * @param array $valueTypes
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\Parameters\ItemLinkValidator::__construct
     * @covers \Netgen\BlockManager\Validator\Parameters\ItemLinkValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($value, $valueTypes, $isValid)
    {
        $this->constraint->valueTypes = $valueTypes;

        if ($value !== null && $isValid) {
            $this->itemLoaderMock
                ->expects($this->once())
                ->method('load')
                ->will($this->returnValue(new Item()));
        }

        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Parameters\ItemLinkValidator::validate
     */
    public function testValidateWithInvalidItem()
    {
        $this->itemLoaderMock
            ->expects($this->once())
            ->method('load')
            ->will($this->throwException(new InvalidItemException()));

        $this->assertValid(false, 'value://42');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Parameters\ItemLinkValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value://42');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Parameters\ItemLinkValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue()
    {
        $this->assertValid(true, 42);
    }

    public function validateDataProvider()
    {
        return array(
            array('value://42', null, true),
            array('other://42', null, false),
            array('value://42', array(), true),
            array('other://42', array(), false),
            array('value://42', array('value'), true),
            array('other://42', array('value'), false),
            array('value://42', array('other'), false),
            array('other://42', array('other'), false),
            array('42', null, false),
            array('42', array(), false),
            array('42', array('value'), false),
            array('42', array('other'), false),
            array(null, null, true),
            array(null, array(), true),
            array(null, array('value'), true),
            array(null, array('other'), true),
        );
    }
}
