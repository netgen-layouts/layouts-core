<?php

declare(strict_types=1);

namespace Netgen\Layouts\Attribute;

use Attribute;

/**
 * Service tag to autoconfigure value converters.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class AsCmsValueConverter {}
