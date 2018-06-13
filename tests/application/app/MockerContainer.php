<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Kernel;

use Symfony\Component\DependencyInjection\Container;

class MockerContainer extends Container
{
    /**
     * @var array
     */
    private $originalServices = [];

    /**
     * @var array
     */
    private $mockedServices = [];

    public function mock($id, /* PHPUnit\Framework\MockObject\MockObject */ $mock)
    {
        // @deprecated Enable MockObject type hint when support for PHP 5.6 (and PHPUnit 5) ends

        if (!array_key_exists($id, $this->mockedServices)) {
            $this->originalServices[$id] = $this->get($id);
            $this->mockedServices[$id] = $this->services[$id] = $mock;
        }

        return $this->mockedServices[$id];
    }

    public function unmock($id)
    {
        $this->services[$id] = $this->originalServices[$id];
        unset($this->originalServices[$id], $this->mockedServices[$id]);
    }

    /**
     * @return array
     */
    public function getMockedServices()
    {
        return $this->mockedServices;
    }
}
