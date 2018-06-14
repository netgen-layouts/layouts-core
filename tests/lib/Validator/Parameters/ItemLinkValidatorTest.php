<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator\Parameters;

use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\NullItem;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\Parameters\ItemLink;
use Netgen\BlockManager\Validator\Parameters\ItemLinkValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class ItemLinkValidatorTest extends ValidatorTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $itemLoaderMock;

    public function setUp(): void
    {
        $this->constraint = new ItemLink();

        parent::setUp();
    }

    public function getValidator(): ConstraintValidatorInterface
    {
        $this->itemLoaderMock = $this->createMock(ItemLoaderInterface::class);

        return new ItemLinkValidator($this->itemLoaderMock);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Parameters\ItemLinkValidator::__construct
     * @covers \Netgen\BlockManager\Validator\Parameters\ItemLinkValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate(?string $value, ?array $valueTypes, bool $isValid): void
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
    public function testValidateWithInvalidItem(): void
    {
        $this->itemLoaderMock
            ->expects($this->once())
            ->method('load')
            ->will($this->returnValue(new NullItem('value')));

        $this->assertValid(false, 'value://42');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Parameters\ItemLinkValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Validator\Constraint\Parameters\ItemLink", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value://42');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Parameters\ItemLinkValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "string", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->assertValid(true, 42);
    }

    public function validateDataProvider(): array
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
