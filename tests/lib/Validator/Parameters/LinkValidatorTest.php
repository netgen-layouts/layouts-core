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
     * @param mixed[]|null $valueTypes
     *
     * @covers \Netgen\Layouts\Validator\Parameters\LinkValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate(?LinkValue $value, bool $required, ?array $valueTypes, bool $isValid): void
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
        $this->expectExceptionMessage('Expected argument of type "Netgen\\Layouts\\Validator\\Constraint\\Parameters\\Link", "Symfony\\Component\\Validator\\Constraints\\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, new LinkValue());
    }

    /**
     * @covers \Netgen\Layouts\Validator\Parameters\LinkValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessageMatches('/^Expected argument of type "Netgen\\\\Layouts\\\\Parameters\\\\Value\\\\LinkValue", "int(eger)?" given$/');

        $this->assertValid(true, 42);
    }

    public function validateDataProvider(): array
    {
        return [
            [null, true, null, true],
            [null, false, null, true],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => 'https://netgen.io', 'linkSuffix' => 'suffix']), true, null, true],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => 'https://netgen.io', 'newWindow' => true]), true, null, true],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => 'https://netgen.io', 'newWindow' => false]), true, null, true],
            [LinkValue::fromArray(['linkType' => null, 'link' => null]), true, null, true],
            [LinkValue::fromArray(['linkType' => null, 'link' => 'https://netgen.io']), true, null, false],
            [LinkValue::fromArray(['linkType' => null, 'link' => null]), false, null, true],
            [LinkValue::fromArray(['linkType' => null, 'link' => 'https://netgen.io']), false, null, false],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => null]), true, null, false],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => 'https://netgen.io']), true, null, true],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => null]), false, null, true],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => 'https://netgen.io']), false, null, true],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => 'invalid']), true, null, false],
            [LinkValue::fromArray(['linkType' => 'url', 'link' => 'invalid']), false, null, false],
            [LinkValue::fromArray(['linkType' => 'email', 'link' => null]), true, null, false],
            [LinkValue::fromArray(['linkType' => 'email', 'link' => 'info@netgen.io']), true, null, true],
            [LinkValue::fromArray(['linkType' => 'email', 'link' => null]), false, null, true],
            [LinkValue::fromArray(['linkType' => 'email', 'link' => 'info@netgen.io']), false, null, true],
            [LinkValue::fromArray(['linkType' => 'email', 'link' => 'invalid']), true, null, false],
            [LinkValue::fromArray(['linkType' => 'email', 'link' => 'invalid']), false, null, false],
            [LinkValue::fromArray(['linkType' => 'phone', 'link' => null]), true, null, false],
            [LinkValue::fromArray(['linkType' => 'phone', 'link' => 'info@netgen.io']), true, null, true],
            [LinkValue::fromArray(['linkType' => 'phone', 'link' => null]), false, null, true],
            [LinkValue::fromArray(['linkType' => 'phone', 'link' => 'info@netgen.io']), false, null, true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => null]), true, null, false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value://42']), true, null, true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => null]), false, null, true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value://42']), false, null, true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value']), true, null, false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value']), false, null, false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => null]), true, ['value'], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value://42']), true, ['value'], true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => null]), false, ['value'], true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value://42']), false, ['value'], true],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value']), true, ['value'], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value']), false, ['value'], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => null]), true, ['other'], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => 'value://42']), true, ['other'], false],
            [LinkValue::fromArray(['linkType' => 'internal', 'link' => null]), false, ['other'], true],
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
