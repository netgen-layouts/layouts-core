<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use Netgen\Layouts\Validator\Constraint\RuleGroup;
use Netgen\Layouts\Validator\RuleGroupValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Stub;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

#[CoversClass(RuleGroupValidator::class)]
final class RuleGroupValidatorTest extends ValidatorTestCase
{
    private Stub&LayoutResolverService $layoutResolverServiceStub;

    protected function setUp(): void
    {
        $this->constraint = new RuleGroup();

        parent::setUp();
    }

    #[DataProvider('validateDataProvider')]
    public function testValidate(mixed $value, bool $isValid): void
    {
        if ($value !== null) {
            $this->layoutResolverServiceStub
                ->method('ruleGroupExists')
                ->with(self::equalTo(Uuid::fromString($value)))
                ->willReturn($isValid);
        }

        $this->assertValid($isValid, $value);
    }

    public function testValidateWithNonExistentValue(): void
    {
        $this->constraint = new RuleGroup(allowInvalid: true);

        $this->assertValid(true, 'ffffffff-ffff-ffff-ffff-ffffffffffff');
    }

    public function testValidateWithInvalidUuidFormat(): void
    {
        $this->assertValid(false, 'abc');
    }

    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Validator\Constraint\RuleGroup", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 42);
    }

    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "string", "int" given');

        $this->assertValid(true, 42);
    }

    public static function validateDataProvider(): iterable
    {
        return [
            ['81168ed3-86f9-55ea-b153-101f96f2c136', true],
            ['71cbe281-430c-51d5-8e21-c3cc4e656dac', false],
            [null, true],
        ];
    }

    protected function getValidator(): ConstraintValidatorInterface
    {
        $this->layoutResolverServiceStub = self::createStub(LayoutResolverService::class);

        return new RuleGroupValidator($this->layoutResolverServiceStub);
    }
}
