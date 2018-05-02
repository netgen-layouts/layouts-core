<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Controller\API\Kernel;

use Mockery;
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

    public function mock($id, ...$arguments)
    {
        if (!array_key_exists($id, $this->mockedServices)) {
            $this->originalServices[$id] = $this->get($id);
            $this->mockedServices[$id] = $this->services[$id] = Mockery::mock(...$arguments);
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
