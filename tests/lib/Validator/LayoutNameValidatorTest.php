<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\LayoutName;
use Netgen\BlockManager\Validator\LayoutNameValidator;
use Symfony\Component\Validator\Constraints\NotBlank;

final class LayoutNameValidatorTest extends ValidatorTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutServiceMock;

    public function setUp()
    {
        $this->constraint = new LayoutName();

        parent::setUp();
    }

    /**
     * @return \Symfony\Component\Validator\ConstraintValidator
     */
    public function getValidator()
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        return new LayoutNameValidator($this->layoutServiceMock);
    }

    /**
     * @param string $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\LayoutNameValidator::__construct
     * @covers \Netgen\BlockManager\Validator\LayoutNameValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($value, $isValid)
    {
        if ($value !== null) {
            $this->layoutServiceMock
                ->expects($this->once())
                ->method('layoutNameExists')
                ->with($this->equalTo($value))
                ->will($this->returnValue(!$isValid));
        }

        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LayoutNameValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Validator\Constraint\LayoutName", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'My layout');
    }

    /**
     * @covers \Netgen\BlockManager\Validator\LayoutNameValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "string", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue()
    {
        $this->assertValid(true, 42);
    }

    public function validateDataProvider()
    {
        return array(
            array('My layout', true),
            array('My layout', false),
            array(null, true),
        );
    }
}
