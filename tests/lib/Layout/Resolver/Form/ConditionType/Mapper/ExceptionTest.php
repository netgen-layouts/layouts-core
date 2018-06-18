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
        $this->assertSame(ChoiceType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\Exception::buildErrorCodes
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\Exception::getFormOptions
     */
    public function testGetFormOptions(): void
    {
        $options = $this->mapper->getFormOptions();

        $this->assertArrayHasKey('multiple', $options);
        $this->assertArrayHasKey('required', $options);
        $this->assertArrayHasKey('choices', $options);

        $this->assertTrue($options['multiple']);
        $this->assertFalse($options['required']);
        $this->assertInternalType('array', $options['choices']);
    }
}
