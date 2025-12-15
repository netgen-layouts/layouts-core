<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper\RequestUriPrefixMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

#[CoversClass(RequestUriPrefixMapper::class)]
final class RequestUriPrefixMapperTest extends TestCase
{
    private RequestUriPrefixMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new RequestUriPrefixMapper();
    }

    public function testGetFormType(): void
    {
        self::assertSame(TextType::class, $this->mapper->getFormType());
    }
}
