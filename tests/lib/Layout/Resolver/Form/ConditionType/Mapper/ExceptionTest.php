<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper\Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

#[CoversClass(Exception::class)]
final class ExceptionTest extends TestCase
{
    private Exception $mapper;

    protected function setUp(): void
    {
        $this->mapper = new Exception();
    }

    public function testGetFormType(): void
    {
        self::assertSame(ChoiceType::class, $this->mapper->getFormType());
    }

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
