<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Mapper;

use Netgen\Layouts\Parameters\Form\Mapper\UrlMapper;
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
}
