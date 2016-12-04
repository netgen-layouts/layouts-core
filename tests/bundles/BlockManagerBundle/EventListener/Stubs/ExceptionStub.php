<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\Stubs;

use Exception as BaseException;
use Netgen\BlockManager\Exception\Exception;

class ExceptionStub extends BaseException implements Exception
{
}
