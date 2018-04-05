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

    /**
     * Sets up the test.
     */
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
     * @param bool $isRequired
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::validateIdentifier
     * @dataProvider validateIdentifierDataProvider
     */
    public function testValidateIdentifier($identifier, $isRequired, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->validator->validateIdentifier($identifier, null, $isRequired);
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
        return array(
            array(24, true),
            array('24', true),
            array('', false),
            array(array(), false),
            array(null, false),
        );
    }

    public function validateIdentifierDataProvider()
    {
        return array(
            array(24, false, false),
            array(24, true, false),
            array(null, false, true),
            array(null, true, false),
            array('identifier', false, true),
            array('identifier', true, true),
            array('identifier_1', false, true),
            array('identifier_2', true, true),
            array('123identifier', false, false),
            array('345identifier', true, false),
            array('an identifier', false, false),
            array('other identifier', true, false),
            array('', false, false),
            array('', true, false),
        );
    }

    public function validatePositionDataProvider()
    {
        return array(
            array(-5, false, false),
            array(-5, true, false),
            array(0, false, true),
            array(0, true, true),
            array(24, false, true),
            array(24, true, true),
            array(null, false, true),
            array(null, true, false),
            array('identifier', false, false),
            array('identifier', true, false),
        );
    }

    public function validateOffsetAndLimitDataProvider()
    {
        return array(
            array(0, null, true),
            array(5, null, true),
            array('5', null, false),
            array(null, null, false),
            array(0, 1, true),
            array(5, 1, true),
            array('5', 1, false),
            array(null, 1, false),
            array(5, '5', false),
        );
    }

    public function validateLocaleDataProvider()
    {
        return array(
            array('en', true),
            array('en_US', true),
            array('pt', true),
            array('pt_PT', true),
            array('zh_Hans', true),
            array('fil_PH', true),
            // We do not allow non-canonicalized locales
            array('en-US', false),
            array('es-AR', false),
            array('fr_FR.utf8', false),
            array('EN', false),
            // Invalid locales
            array('foobar', false),
        );
    }
}
