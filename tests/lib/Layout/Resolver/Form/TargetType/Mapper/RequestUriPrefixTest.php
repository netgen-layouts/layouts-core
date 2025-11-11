<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper\RequestUriPrefix;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

#[CoversClass(RequestUriPrefix::class)]
final class RequestUriPrefixTest extends TestCase
{
    private RequestUriPrefix $mapper;

    protected function setUp(): void
    {
        $this->mapper = new RequestUriPrefix();
    }

    public function testGetFormType(): void
    {
        self::assertSame(TextType::class, $this->mapper->getFormType());
    }
}
