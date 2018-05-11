<?php

namespace Netgen\BlockManager\Behat\Exception;

use Exception;

final class LayoutException extends Exception
{
    public static function layoutWithNameExists($layoutName)
    {
        return new self(
            sprintf(
                'Layout with "%s" name exists.',
                $layoutName
            )
        );
    }
}
