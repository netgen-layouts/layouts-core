<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Parameters;

use Netgen\Layouts\Parameters\Value\LinkType;
use Netgen\Layouts\Parameters\Value\LinkValue;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use Netgen\Layouts\Validator\Constraint\Parameters\Link;
use Netgen\Layouts\Validator\Parameters\LinkValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

#[CoversClass(LinkValidator::class)]
final class LinkValidatorTest extends ValidatorTestCase
{
    protected function setUp(): void
    {
        $this->constraint = new Link();

        parent::setUp();
    }

    /**
     * @param mixed[] $valueTypes
     */
    #[DataProvider('validateDataProvider')]
    public function testValidate(?LinkValue $value, bool $required, array $valueTypes, bool $isValid): void
    {
        $this->constraint->required = $required;
        $this->constraint->valueTypes = $valueTypes;

        $this->assertValid($isValid, $value);
    }

    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Validator\Constraint\Parameters\Link", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, new LinkValue());
    }

    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Parameters\Value\LinkValue", "int" given');

        $this->assertValid(true, 42);
    }

    public static function validateDataProvider(): iterable
    {
        return [
            [null, true, [], true],
            [null, false, [], true],
            [LinkValue::fromArray(['linkType' => LinkType::Url, 'link' => 'https://netgen.io', 'linkSuffix' => 'suffix']), true, [], true],
            [LinkValue::fromArray(['linkType' => LinkType::Url, 'link' => 'https://netgen.io', 'newWindow' => true]), true, [], true],
            [LinkValue::fromArray(['linkType' => LinkType::Url, 'link' => 'https://netgen.io', 'newWindow' => false]), true, [], true],
            [LinkValue::fromArray(['linkType' => null, 'link' => '']), true, [], true],
            [LinkValue::fromArray(['linkType' => null, 'link' => 'https://netgen.io']), true, [], false],
            [LinkValue::fromArray(['linkType' => null, 'link' => '']), false, [], true],
            [LinkValue::fromArray(['linkType' => null, 'link' => 'https://netgen.io']), false, [], false],
            [LinkValue::fromArray(['linkType' => LinkType::Url, 'link' => '']), true, [], false],
            [LinkValue::fromArray(['linkType' => LinkType::Url, 'link' => 'https://netgen.io']), true, [], true],
            [LinkValue::fromArray(['linkType' => LinkType::Url, 'link' => '']), false, [], true],
            [LinkValue::fromArray(['linkType' => LinkType::Url, 'link' => 'https://netgen.io']), false, [], true],
            [LinkValue::fromArray(['linkType' => LinkType::Url, 'link' => 'invalid']), true, [], false],
            [LinkValue::fromArray(['linkType' => LinkType::Url, 'link' => 'invalid']), false, [], false],
            [LinkValue::fromArray(['linkType' => LinkType::Email, 'link' => '']), true, [], false],
            [LinkValue::fromArray(['linkType' => LinkType::Email, 'link' => 'info@netgen.io']), true, [], true],
            [LinkValue::fromArray(['linkType' => LinkType::Email, 'link' => '']), false, [], true],
            [LinkValue::fromArray(['linkType' => LinkType::Email, 'link' => 'info@netgen.io']), false, [], true],
            [LinkValue::fromArray(['linkType' => LinkType::Email, 'link' => 'invalid']), true, [], false],
            [LinkValue::fromArray(['linkType' => LinkType::Email, 'link' => 'invalid']), false, [], false],
            [LinkValue::fromArray(['linkType' => LinkType::Phone, 'link' => '']), true, [], false],
            [LinkValue::fromArray(['linkType' => LinkType::Phone, 'link' => 'info@netgen.io']), true, [], true],
            [LinkValue::fromArray(['linkType' => LinkType::Phone, 'link' => '']), false, [], true],
            [LinkValue::fromArray(['linkType' => LinkType::Phone, 'link' => 'info@netgen.io']), false, [], true],
            [LinkValue::fromArray(['linkType' => LinkType::Internal, 'link' => '']), true, [], false],
            [LinkValue::fromArray(['linkType' => LinkType::Internal, 'link' => 'value://42']), true, [], true],
            [LinkValue::fromArray(['linkType' => LinkType::Internal, 'link' => '']), false, [], true],
            [LinkValue::fromArray(['linkType' => LinkType::Internal, 'link' => 'value://42']), false, [], true],
            [LinkValue::fromArray(['linkType' => LinkType::Internal, 'link' => 'value']), true, [], false],
            [LinkValue::fromArray(['linkType' => LinkType::Internal, 'link' => 'value']), false, [], false],
            [LinkValue::fromArray(['linkType' => LinkType::Internal, 'link' => '']), true, ['value'], false],
            [LinkValue::fromArray(['linkType' => LinkType::Internal, 'link' => 'value://42']), true, ['value'], true],
            [LinkValue::fromArray(['linkType' => LinkType::Internal, 'link' => '']), false, ['value'], true],
            [LinkValue::fromArray(['linkType' => LinkType::Internal, 'link' => 'value://42']), false, ['value'], true],
            [LinkValue::fromArray(['linkType' => LinkType::Internal, 'link' => 'value']), true, ['value'], false],
            [LinkValue::fromArray(['linkType' => LinkType::Internal, 'link' => 'value']), false, ['value'], false],
            [LinkValue::fromArray(['linkType' => LinkType::Internal, 'link' => '']), true, ['other'], false],
            [LinkValue::fromArray(['linkType' => LinkType::Internal, 'link' => 'value://42']), true, ['other'], false],
            [LinkValue::fromArray(['linkType' => LinkType::Internal, 'link' => '']), false, ['other'], true],
            [LinkValue::fromArray(['linkType' => LinkType::Internal, 'link' => 'value://42']), false, ['other'], false],
            [LinkValue::fromArray(['linkType' => LinkType::Internal, 'link' => 'value']), true, ['other'], false],
            [LinkValue::fromArray(['linkType' => LinkType::Internal, 'link' => 'value']), false, ['other'], false],
        ];
    }

    protected function getValidator(): ConstraintValidatorInterface
    {
        return new LinkValidator();
    }
}
