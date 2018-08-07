<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\LayoutName;
use Netgen\BlockManager\Validator\LayoutNameValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class LayoutNameValidatorTest extends ValidatorTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutServiceMock;

    public function setUp(): void
    {
        $this->constraint = new LayoutName();

        parent::setUp();
    }

    public function getValidator(): ConstraintValidatorInterface
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        return new LayoutNameValidator($this->layoutServiceMock);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LayoutNameValidator::__construct
     * @covers \Netgen\BlockManager\Validator\LayoutNameValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate(?string $value, bool $isValid): void
    {
        if ($value !== null) {
            $this->layoutServiceMock
                ->expects(self::once())
                ->method('layoutNameExists')
                ->with(self::identicalTo($value))
                ->will(self::returnValue(!$isValid));
        }

        self::assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LayoutNameValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Validator\Constraint\LayoutName", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->constraint = new NotBlank();
        self::assertValid(true, 'My layout');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LayoutNameValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "string", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        self::assertValid(true, 42);
    }

    public function validateDataProvider(): array
    {
        return [
            ['My layout', true],
            ['My layout', false],
            [null, true],
        ];
    }
}
