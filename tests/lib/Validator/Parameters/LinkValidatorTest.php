<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator\Parameters;

use Netgen\BlockManager\Parameters\Value\LinkValue;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\Parameters\Link;
use Netgen\BlockManager\Validator\Parameters\LinkValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class LinkValidatorTest extends ValidatorTestCase
{
    public function setUp(): void
    {
        $this->constraint = new Link();

        parent::setUp();
    }

    public function getValidator(): ConstraintValidatorInterface
    {
        return new LinkValidator();
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Parameters\LinkValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate(?LinkValue $value, bool $required, ?array $valueTypes, bool $isValid): void
    {
        $this->constraint->required = $required;
        $this->constraint->valueTypes = $valueTypes;

        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Parameters\LinkValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Validator\Constraint\Parameters\Link", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, new LinkValue());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Parameters\LinkValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Parameters\Value\LinkValue", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->assertValid(true, 42);
    }

    public function validateDataProvider(): array
    {
        return [
            [null, true, null, true],
            [null, false, null, true],
            [new LinkValue(['linkType' => 'url', 'link' => 'http://a.com', 'linkSuffix' => 'suffix']), true, null, true],
            [new LinkValue(['linkType' => 'url', 'link' => 'http://a.com', 'newWindow' => true]), true, null, true],
            [new LinkValue(['linkType' => 'url', 'link' => 'http://a.com', 'newWindow' => false]), true, null, true],
            [new LinkValue(['linkType' => null, 'link' => null]), true, null, true],
            [new LinkValue(['linkType' => null, 'link' => 'http://a.com']), true, null, false],
            [new LinkValue(['linkType' => null, 'link' => null]), false, null, true],
            [new LinkValue(['linkType' => null, 'link' => 'http://a.com']), false, null, false],
            [new LinkValue(['linkType' => 'url', 'link' => null]), true, null, false],
            [new LinkValue(['linkType' => 'url', 'link' => 'http://a.com']), true, null, true],
            [new LinkValue(['linkType' => 'url', 'link' => null]), false, null, true],
            [new LinkValue(['linkType' => 'url', 'link' => 'http://a.com']), false, null, true],
            [new LinkValue(['linkType' => 'url', 'link' => 'invalid']), true, null, false],
            [new LinkValue(['linkType' => 'url', 'link' => 'invalid']), false, null, false],
            [new LinkValue(['linkType' => 'email', 'link' => null]), true, null, false],
            [new LinkValue(['linkType' => 'email', 'link' => 'a@a.com']), true, null, true],
            [new LinkValue(['linkType' => 'email', 'link' => null]), false, null, true],
            [new LinkValue(['linkType' => 'email', 'link' => 'a@a.com']), false, null, true],
            [new LinkValue(['linkType' => 'email', 'link' => 'invalid']), true, null, false],
            [new LinkValue(['linkType' => 'email', 'link' => 'invalid']), false, null, false],
            [new LinkValue(['linkType' => 'phone', 'link' => null]), true, null, false],
            [new LinkValue(['linkType' => 'phone', 'link' => 'a@a.com']), true, null, true],
            [new LinkValue(['linkType' => 'phone', 'link' => null]), false, null, true],
            [new LinkValue(['linkType' => 'phone', 'link' => 'a@a.com']), false, null, true],
            [new LinkValue(['linkType' => 'internal', 'link' => null]), true, null, false],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value://42']), true, null, true],
            [new LinkValue(['linkType' => 'internal', 'link' => null]), false, null, true],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value://42']), false, null, true],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value']), true, null, false],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value']), false, null, false],
            [new LinkValue(['linkType' => 'internal', 'link' => null]), true, ['value'], false],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value://42']), true, ['value'], true],
            [new LinkValue(['linkType' => 'internal', 'link' => null]), false, ['value'], true],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value://42']), false, ['value'], true],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value']), true, ['value'], false],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value']), false, ['value'], false],
            [new LinkValue(['linkType' => 'internal', 'link' => null]), true, ['other'], false],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value://42']), true, ['other'], false],
            [new LinkValue(['linkType' => 'internal', 'link' => null]), false, ['other'], true],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value://42']), false, ['other'], false],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value']), true, ['other'], false],
            [new LinkValue(['linkType' => 'internal', 'link' => 'value']), false, ['other'], false],
        ];
    }
}
