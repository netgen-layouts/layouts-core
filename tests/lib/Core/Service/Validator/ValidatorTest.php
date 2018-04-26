<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\Core\Service\Validator\Validator;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class ValidatorTest extends TestCase
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $baseValidator;

    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\Validator
     */
    private $validator;

    public function setUp()
    {
        $this->baseValidator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $this->validator = $this->getMockForAbstractClass(Validator::class);
        $this->validator->setValidator($this->baseValidator);
    }

    /**
     * @param int|string $id
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::validateId
     * @dataProvider validateIdDataProvider
     */
    public function testValidateId($id, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->validator->validateId($id);
    }

    /**
     * @param string $identifier
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::validateIdentifier
     * @dataProvider validateIdentifierDataProvider
     */
    public function testValidateIdentifier($identifier, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->validator->validateIdentifier($identifier);
    }

    /**
     * @param int $position
     * @param bool $isRequired
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::validatePosition
     * @dataProvider validatePositionDataProvider
     */
    public function testValidatePosition($position, $isRequired, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->validator->validatePosition($position, null, $isRequired);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::validateOffsetAndLimit
     * @dataProvider validateOffsetAndLimitDataProvider
     */
    public function testValidateOffsetAndLimit($offset, $limit, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->validator->validateOffsetAndLimit($offset, $limit);
    }

    /**
     * @param string $locale
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::validateLocale
     * @dataProvider validateLocaleDataProvider
     */
    public function testValidateLocale($locale, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->validator->validateLocale($locale);
    }

    public function validateIdDataProvider()
    {
        return [
            [24, true],
            ['24', true],
            ['', false],
            [[], false],
            [null, false],
        ];
    }

    public function validateIdentifierDataProvider()
    {
        return [
            [24, false],
            [null, false],
            ['identifier', true],
            ['identifier_2', true],
            ['345identifier', false],
            ['other identifier', false],
            ['', false],
        ];
    }

    public function validatePositionDataProvider()
    {
        return [
            [-5, false, false],
            [-5, true, false],
            [0, false, true],
            [0, true, true],
            [24, false, true],
            [24, true, true],
            [null, false, true],
            [null, true, false],
            ['identifier', false, false],
            ['identifier', true, false],
        ];
    }

    public function validateOffsetAndLimitDataProvider()
    {
        return [
            [0, null, true],
            [5, null, true],
            ['5', null, false],
            [null, null, false],
            [0, 1, true],
            [5, 1, true],
            ['5', 1, false],
            [null, 1, false],
            [5, '5', false],
        ];
    }

    public function validateLocaleDataProvider()
    {
        return [
            ['en', true],
            ['en_US', true],
            ['pt', true],
            ['pt_PT', true],
            ['zh_Hans', true],
            ['fil_PH', true],
            // We do not allow non-canonicalized locales
            ['en-US', false],
            ['es-AR', false],
            ['fr_FR.utf8', false],
            ['EN', false],
            // Invalid locales
            ['foobar', false],
        ];
    }
}
