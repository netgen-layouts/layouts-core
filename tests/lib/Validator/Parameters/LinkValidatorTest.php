<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Parameters;

use Netgen\Layouts\Parameters\Value\LinkValue;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use Netgen\Layouts\Validator\Constraint\Parameters\Link;
use Netgen\Layouts\Validator\Parameters\LinkValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class LinkValidatorTest extends ValidatorTestCase
{
    protected function setUp(): void
    {
        $this->constraint = new Link();

        parent::setUp();
    }

    /**
     * @param mixed[] $valueTypes
     *
     * @covers \Netgen\Layouts\Validator\Parameters\LinkValidator::validate
     *
     * @dataProvider validateDataProvider
     */
    public function testValidate(?LinkValue $value, bool $required, array $valueTypes, bool $isValid): void
    {
        $this->constraint->required = $required;
        $this->constraint->valueTypes = $valueTypes;

        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\Layouts\Validator\Parameters\LinkValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Validator\Constraint\Parameters\Link", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, new LinkValue());
    }

    /**
     * @covers \Netgen\Layouts\Validator\Parameters\LinkValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessageMatches('/^Expected argument of type "Netgen\\\Layouts\\\Parameters\\\Value\\\LinkValue", "int(eger)?" given$/');

        $this->assertValid(true, 42);
    }

    public static function validateDataProvider(): iterable
    {
        return [
            [null, true, [], true],
            [null, false, [], true],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => 'https://netgen.io', 'linkSuffix' => 'suffix']), true, [], true],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => 'https://netgen.io', 'newWindow' => true]), true, [], true],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => 'https://netgen.io', 'newWindow' => false]), true, [], true],
            [LinkValue::fromArray(['linkType' => '', 'link' => '']), true, [], true],
            [LinkValue::fromArray(['linkType' => '', 'link' => 'https://netgen.io']), true, [], false],
            [LinkValue::fromArray(['linkType' => '', 'link' => '']), false, [], true],
            [LinkValue::fromArray(['linkType' => '', 'link' => 'https://netgen.io']), false, [], false],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => '']), true, [], false],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => 'https://netgen.io']), true, [], true],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => '']), false, [], true],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => 'https://netgen.io']), false, [], true],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => 'invalid']), true, [], false],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => 'invalid']), false, [], false],
            [LinkValue::fromArray(['linkType' => 'email', 'link' => '']), true, [], false],
            [LinkValue::fromArray(['linkType' => 'email', 'link' => 'info@netgen.io']), true, [], true],
            [LinkValue::fromArray(['linkType' => 'email', 'link' => '']), false, [], true],
            [LinkValue::fromArray(['linkType' => 'email', 'link' => 'info@netgen.io']), false, [], true],
            [LinkValue::fromArray(['linkType' => 'email', 'link' => 'invalid']), true, [], false],
            [LinkValue::fromArray(['linkType' => 'email', 'link' => 'invalid']), false, [], false],
            [LinkValue::fromArray(['linkType' => 'phone', 'link' => '']), true, [], false],
            [LinkValue::fromArray(['linkType' => 'phone', 'link' => 'info@netgen.io']), true, [], true],
            [LinkValue::fromArray(['linkType' => 'phone', 'link' => '']), false, [], true],
            [LinkValue::fromArray(['linkType' => 'phone', 'link' => 'info@netgen.io']), false, [], true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => '']), true, [], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value://42']), true, [], true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => '']), false, [], true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value://42']), false, [], true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value']), true, [], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value']), false, [], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => '']), true, ['value'], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value://42']), true, ['value'], true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => '']), false, ['value'], true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value://42']), false, ['value'], true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value']), true, ['value'], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value']), false, ['value'], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => '']), true, ['other'], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value://42']), true, ['other'], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => '']), false, ['other'], true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value://42']), false, ['other'], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value']), true, ['other'], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value']), false, ['other'], false],
        ];
    }

    protected function getValidator(): ConstraintValidatorInterface
    {
        return new LinkValidator();
    }
}
