<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\Stubs;

use Netgen\BlockManager\Exception\Exception;
use Exception as BaseException;

class ExceptionStub extends BaseException implements Exception
{
}
