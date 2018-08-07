<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper\UrlMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

final class UrlMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Mapper\UrlMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new UrlMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper\UrlMapper::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(UrlType::class, $this->mapper->getFormType());
    }
}
