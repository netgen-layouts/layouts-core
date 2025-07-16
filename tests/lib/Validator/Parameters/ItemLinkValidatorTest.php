<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Parameters;

use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Item\NullCmsItem;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use Netgen\Layouts\Validator\Constraint\Parameters\ItemLink;
use Netgen\Layouts\Validator\Parameters\ItemLinkValidator;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ItemLinkValidatorTest extends ValidatorTestCase
{
    private MockObject $cmsItemLoaderMock;

    protected function setUp(): void
    {
        $this->constraint = new ItemLink();

        parent::setUp();
    }

    /**
     * @param mixed[] $valueTypes
     *
     * @covers \Netgen\Layouts\Validator\Parameters\ItemLinkValidator::__construct
     * @covers \Netgen\Layouts\Validator\Parameters\ItemLinkValidator::validate
     *
     * @dataProvider validateDataProvider
     */
    public function testValidate(?string $value, array $valueTypes, bool $isValid): void
    {
        $this->constraint->valueTypes = $valueTypes;

        if ($value !== null && $value !== '' && $isValid) {
            $this->cmsItemLoaderMock
                ->expects(self::once())
                ->method('load')
                ->willReturn(new CmsItem());
        }

        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\Layouts\Validator\Parameters\ItemLinkValidator::validate
     */
    public function testValidateWithInvalidItem(): void
    {
        $this->cmsItemLoaderMock
            ->expects(self::once())
            ->method('load')
            ->willReturn(new NullCmsItem('value'));

        $this->assertValid(false, 'value://42');
    }

    /**
     * @covers \Netgen\Layouts\Validator\Parameters\ItemLinkValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Validator\Constraint\Parameters\ItemLink", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value://42');
    }

    /**
     * @covers \Netgen\Layouts\Validator\Parameters\ItemLinkValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessageMatches('/^Expected argument of type "string", "int(eger)?" given$/');

        $this->assertValid(true, 42);
    }

    public static function validateDataProvider(): iterable
    {
        return [
            ['', [], true],
            ['value', [], false],
            ['other', [], false],
            ['value:', [], false],
            ['other:', [], false],
            ['value:/', [], false],
            ['other:/', [], false],
            ['value://', [], false],
            ['other://', [], false],
            ['value://42', [], true],
            ['other://42', [], false],
            ['value://null', [], true],
            ['other://null', [], false],
            ['value://0', [], true],
            ['other://0', [], false],
            ['value://42', [], true],
            ['other://42', [], false],
            ['value://42', ['value'], true],
            ['other://42', ['value'], false],
            ['value://42', ['other'], false],
            ['other://42', ['other'], false],
            ['42', [], false],
            ['42', ['value'], false],
            ['42', ['other'], false],
            [null, [], true],
            [null, ['value'], true],
            [null, ['other'], true],
        ];
    }

    protected function getValidator(): ConstraintValidatorInterface
    {
        $this->cmsItemLoaderMock = $this->createMock(CmsItemLoaderInterface::class);

        return new ItemLinkValidator($this->cmsItemLoaderMock);
    }
}
