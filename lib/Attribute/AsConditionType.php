<?php

declare(strict_types=1);

namespace Netgen\Layouts\Attribute;

use Attribute;

/**
 * Service tag to autoconfigure condition types.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class AsConditionType {}
