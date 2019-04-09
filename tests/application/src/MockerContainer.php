<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\App;

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

    public function mock(string $id, object $mock): object
    {
        if (!array_key_exists($id, $this->mockedServices)) {
            $this->originalServices[$id] = $this->get($id);
            $this->mockedServices[$id] = $this->services[$id] = $mock;
        }

        return $this->mockedServices[$id];
    }

    public function unmock(string $id): void
    {
        $this->services[$id] = $this->originalServices[$id];
        unset($this->originalServices[$id], $this->mockedServices[$id]);
    }

    public function getMockedServices(): array
    {
        return $this->mockedServices;
    }
}
