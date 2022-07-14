<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\App;

use Netgen\Layouts\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Container;

use function array_key_exists;
use function sprintf;

class MockerContainer extends Container
{
    /**
     * @var array<string, object>
     */
    private array $originalServices = [];

    /**
     * @var array<string, object>
     */
    private array $mockedServices = [];

    public function mock(string $id, object $mock): object
    {
        if (!array_key_exists($id, $this->mockedServices)) {
            $service = $this->get($id);
            if ($service === null) {
                throw new RuntimeException(sprintf('"%s" service does not exist.', $service));
            }

            $this->originalServices[$id] = $service;
            $this->mockedServices[$id] = $this->services[$id] = $mock;
        }

        return $this->mockedServices[$id];
    }

    public function unmock(string $id): void
    {
        $this->services[$id] = $this->originalServices[$id];
        unset($this->originalServices[$id], $this->mockedServices[$id]);
    }

    /**
     * @return array<string, object>
     */
    public function getMockedServices(): array
    {
        return $this->mockedServices;
    }
}
