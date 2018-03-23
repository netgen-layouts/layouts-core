<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\ConditionType;

use Netgen\BlockManager\Layout\Resolver\ConditionType\Time;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use Netgen\BlockManager\Utils\DateTimeUtils;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

final class TimeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionType\Time
     */
    private $conditionType;

    public static function setUpBeforeClass()
    {
        ClockMock::register(DateTimeUtils::class);
    }

    /**
     * Sets up the route target tests.
     */
    public function setUp()
    {
        $this->conditionType = new Time();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\Time::getType
     */
    public function testGetType()
    {
        $this->assertEquals('time', $this->conditionType->getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\Time::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $errors = $validator->validate($value, $this->conditionType->getConstraints());
        $this->assertEquals($isValid, $errors->count() === 0);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\Time::matches
     *
     * @param mixed $value
     * @param bool $matches
     *
     * @dataProvider matchesProvider
     */
    public function testMatches($value, $matches)
    {
        /* Friday March 23, 2018 21:13:20, Antarctica/Casey */
        ClockMock::withClockMock(1521800000);

        $this->assertEquals($matches, $this->conditionType->matches(Request::create('/'), $value));

        ClockMock::withClockMock(false);
    }

    /**
     * Provider for testing condition type validation.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array(array('from' => array(), 'to' => array()), false),
            array(array('from' => null, 'to' => array()), false),
            array(array('from' => array(), 'to' => null), false),
            array(array('from' => null, 'to' => null), true),
            array(array('from' => array('datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey'), 'to' => null), true),
            array(array('from' => array('datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey'), 'to' => array()), false),
            array(array('from' => array('invalid'), 'to' => null), false),
            array(array('from' => array('invalid'), 'to' => array()), false),
            array(array('from' => null, 'to' => array('datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey')), true),
            array(array('from' => array(), 'to' => array('datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey')), false),
            array(array('from' => null, 'to' => array('invalid')), false),
            array(array('from' => array(), 'to' => array('invalid')), false),
            array(array('from' => array('datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey'), 'to' => array('datetime' => '2018-03-25 00:00:00', 'timezone' => 'Antarctica/Casey')), true),
            array(array('from' => array()), false),
            array(array('from' => null), false),
            array(array('to' => array()), false),
            array(array('to' => null), false),
            array(array(), false),
            array(null, false),
        );
    }

    /**
     * Provider for {@link self::testMatches}.
     *
     * @return array
     */
    public function matchesProvider()
    {
        return array(
            array(array('from' => array(), 'to' => array()), true),
            array(array('from' => null, 'to' => array()), true),
            array(array('from' => array(), 'to' => null), true),
            array(array('from' => null, 'to' => null), true),
            array(array('from' => array()), true),
            array(array('from' => null), true),
            array(array('to' => array()), true),
            array(array('to' => null), true),
            array(array('from' => array('datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey'), 'to' => array()), true),
            array(array('from' => array('datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey'), 'to' => null), true),
            array(array('from' => array('datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey')), true),
            array(array('from' => array('datetime' => '2018-03-26 00:00:00', 'timezone' => 'Antarctica/Casey'), 'to' => array()), false),
            array(array('from' => array('datetime' => '2018-03-26 00:00:00', 'timezone' => 'Antarctica/Casey'), 'to' => null), false),
            array(array('from' => array('datetime' => '2018-03-26 00:00:00', 'timezone' => 'Antarctica/Casey')), false),
            array(array('from' => array(), 'to' => array('datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey')), false),
            array(array('from' => null, 'to' => array('datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey')), false),
            array(array('to' => array('datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey')), false),
            array(array('from' => array(), 'to' => array('datetime' => '2018-03-26 00:00:00', 'timezone' => 'Antarctica/Casey')), true),
            array(array('from' => null, 'to' => array('datetime' => '2018-03-26 00:00:00', 'timezone' => 'Antarctica/Casey')), true),
            array(array('to' => array('datetime' => '2018-03-26 00:00:00', 'timezone' => 'Antarctica/Casey')), true),
            array(array('from' => array('datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey'), 'to' => array('datetime' => '2018-03-25 00:00:00', 'timezone' => 'Antarctica/Casey')), true),
            array(array('from' => array('datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey'), 'to' => array('datetime' => '2018-03-21 00:00:00', 'timezone' => 'Antarctica/Casey')), false),
            array(array('from' => array('datetime' => '2018-03-24 00:00:00', 'timezone' => 'Antarctica/Casey'), 'to' => array('datetime' => '2018-03-25 00:00:00', 'timezone' => 'Antarctica/Casey')), false),
            array(array('from' => array('datetime' => '2018-03-25 00:00:00', 'timezone' => 'Antarctica/Casey'), 'to' => array('datetime' => '2018-03-20 00:00:00', 'timezone' => 'Antarctica/Casey')), false),
            array(array(), true),
            array('not_array', false),
        );
    }
}
