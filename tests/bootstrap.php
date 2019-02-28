<?php

declare(strict_types=1);

use ApiTestCase\JsonApiTestCase;
use Lakion\ApiTestCase\JsonApiTestCase as LegacyJsonApiTestCase;
use Netgen\BlockManager\Utils\DateTimeUtils;
use Symfony\Bridge\PhpUnit\ClockMock;

require_once __DIR__ . '/../vendor/autoload.php';

if (class_exists(LegacyJsonApiTestCase::class)) {
    class_alias(LegacyJsonApiTestCase::class, JsonApiTestCase::class);
}

ClockMock::register(DateTimeUtils::class);
