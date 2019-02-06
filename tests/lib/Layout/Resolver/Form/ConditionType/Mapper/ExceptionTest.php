<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class ExceptionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\MapperInterface
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new Exception();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\Exception::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(ChoiceType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\Exception::buildErrorCodes
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\Exception::getFormOptions
     */
    public function testGetFormOptions(): void
    {
        $options = $this->mapper->getFormOptions();

        self::assertArrayHasKey('multiple', $options);
        self::assertArrayHasKey('required', $options);
        self::assertArrayHasKey('choices', $options);

        self::assertTrue($options['multiple']);
        self::assertFalse($options['required']);
        self::assertIsArray($options['choices']);

        foreach ($options['choices'] as $choiceLabel => $choice) {
            self::assertIsString($choiceLabel);
            self::assertIsInt($choice);
            self::assertGreaterThanOrEqual(400, $choice);
            self::assertLessThan(600, $choice);
        }
    }
}
