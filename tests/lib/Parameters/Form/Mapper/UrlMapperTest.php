<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Mapper;

use Netgen\Layouts\Parameters\Form\Mapper\UrlMapper;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType\UrlType as UrlParameterType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

#[CoversClass(UrlMapper::class)]
final class UrlMapperTest extends TestCase
{
    private UrlMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new UrlMapper();
    }

    public function testGetFormType(): void
    {
        self::assertSame(UrlType::class, $this->mapper->getFormType());
    }

    public function testMapOptions(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(
            [
                'type' => new UrlParameterType(),
            ],
        );

        self::assertSame(
            [
                'default_protocol' => null,
            ],
            $this->mapper->mapOptions($parameterDefinition),
        );
    }
}
