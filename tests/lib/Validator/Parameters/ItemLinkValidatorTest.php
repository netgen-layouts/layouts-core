<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator\Parameters;

use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Item\CmsItemLoaderInterface;
use Netgen\BlockManager\Item\NullCmsItem;
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
    private $cmsItemLoaderMock;

    public function setUp(): void
    {
        $this->constraint = new ItemLink();

        parent::setUp();
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
            $this->cmsItemLoaderMock
                ->expects(self::once())
                ->method('load')
                ->will(self::returnValue(new CmsItem()));
        }

        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Parameters\ItemLinkValidator::validate
     */
    public function testValidateWithInvalidItem(): void
    {
        $this->cmsItemLoaderMock
            ->expects(self::once())
            ->method('load')
            ->will(self::returnValue(new NullCmsItem('value')));

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
            ['', null, false],
            ['', null, false],
            ['value', null, false],
            ['other', null, false],
            ['value:', null, false],
            ['other:', null, false],
            ['value:/', null, false],
            ['other:/', null, false],
            ['value://', null, false],
            ['other://', null, false],
            ['value://42', null, true],
            ['other://42', null, false],
            ['value://null', null, true],
            ['other://null', null, false],
            ['value://0', null, true],
            ['other://0', null, false],
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

    protected function getValidator(): ConstraintValidatorInterface
    {
        $this->cmsItemLoaderMock = $this->createMock(CmsItemLoaderInterface::class);

        return new ItemLinkValidator($this->cmsItemLoaderMock);
    }
}
