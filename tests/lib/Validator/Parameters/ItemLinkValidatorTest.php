<?php

namespace Netgen\BlockManager\Tests\Validator\Parameters;

use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\NullItem;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\Parameters\ItemLink;
use Netgen\BlockManager\Validator\Parameters\ItemLinkValidator;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ItemLinkValidatorTest extends ValidatorTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $itemLoaderMock;

    public function setUp()
    {
        $this->constraint = new ItemLink();

        parent::setUp();
    }

    /**
     * @return \Symfony\Component\Validator\ConstraintValidator
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
            ->will($this->returnValue(new NullItem(42)));

        $this->assertValid(false, 'value://42');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Parameters\ItemLinkValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Validator\Constraint\Parameters\ItemLink", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value://42');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Parameters\ItemLinkValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "string", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue()
    {
        $this->assertValid(true, 42);
    }

    public function validateDataProvider()
    {
        return [
            ['value://42', null, true],
            ['other://42', null, false],
            ['value://42', [], true],
            ['other://42', [], false],
            ['value://42', ['value'], true],
            ['other://42', ['value'], false],
            ['value://42', ['other'], false],
            ['other://42', ['other'], false],
            ['42', null, false],
            ['42', [], false],
            ['42', ['value'], false],
            ['42', ['other'], false],
            [null, null, true],
            [null, [], true],
            [null, ['value'], true],
            [null, ['other'], true],
        ];
    }
}
