<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener\Stubs;

use Exception as BaseException;
use Netgen\BlockManager\Exception\Exception;

final class ExceptionStub extends BaseException implements Exception
{
}
