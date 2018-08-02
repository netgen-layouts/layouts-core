<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterType\HtmlType;
use Netgen\BlockManager\Utils\HtmlPurifier;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class HtmlTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    public function setUp(): void
    {
        $this->type = new HtmlType(new HtmlPurifier());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\HtmlType::__construct
     * @covers \Netgen\BlockManager\Parameters\ParameterType\HtmlType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertSame('html', $this->type::getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\HtmlType::toHash
     */
    public function testToHash(): void
    {
        $unsafeHtml = <<<'HTML'
<h1>Title</h1>
<script src="https://cool-hacker.com/cool-hacking-script.js"></script>
<a onclick="alert('Haw-haw!');" href="http://www.google.com">Google</a>
HTML;

        $safeHtml = <<<'HTML'
<h1>Title</h1>
<a href="http://www.google.com">Google</a>
HTML;

        $this->assertSame($safeHtml, $this->type->toHash($this->getParameterDefinition(), $unsafeHtml));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\HtmlType::configureOptions
     * @dataProvider validOptionsProvider
     */
    public function testValidOptions(array $options, array $resolvedOptions): void
    {
        $parameter = $this->getParameterDefinition($options);
        $this->assertSame($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterType\HtmlType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidArgumentException
     * @dataProvider invalidOptionsProvider
     */
    public function testInvalidOptions(array $options): void
    {
        $this->getParameterDefinition($options);
    }

    public function validOptionsProvider(): array
    {
        return [
            [
                [],
                [],
            ],
        ];
    }

    public function invalidOptionsProvider(): array
    {
        return [
            [
                [
                    'undefined_value' => 'Value',
                ],
            ],
        ];
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Parameters\ParameterType\HtmlType::getRequiredConstraints
     * @covers \Netgen\BlockManager\Parameters\ParameterType\HtmlType::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, bool $isValid): void
    {
        $parameter = $this->getParameterDefinition();
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        $this->assertSame($isValid, $errors->count() === 0);
    }

    public function validationProvider(): array
    {
        return [
            ['test', true],
            [null, true],
            [12.3, false],
            [12, false],
            [true, false],
            [false, false],
            [[], false],
        ];
    }
}
