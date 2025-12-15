<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper\PathInfoPrefixMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

#[CoversClass(PathInfoPrefixMapper::class)]
final class PathInfoPrefixMapperTest extends TestCase
{
    private PathInfoPrefixMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new PathInfoPrefixMapper();
    }

    public function testGetFormType(): void
    {
        self::assertSame(TextType::class, $this->mapper->getFormType());
    }
}
