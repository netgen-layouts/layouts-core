<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Input;

use Netgen\Layouts\Exception\InvalidArgumentException;

use function in_array;
use function sprintf;

final class ImportOptions
{
    public const MODE_COPY = 'copy';

    public const MODE_OVERWRITE = 'overwrite';

    public const MODE_SKIP = 'skip';

    /**
     * @var string[]
     */
    private static array $modes = [
        self::MODE_COPY,
        self::MODE_OVERWRITE,
        self::MODE_SKIP,
    ];

    private string $mode = self::MODE_COPY;

    public function setMode(string $mode): self
    {
        if (!in_array($mode, self::$modes, true)) {
            throw new InvalidArgumentException('mode', sprintf('Import mode "%s" is not supported', $mode));
        }

        $this->mode = $mode;

        return $this;
    }

    public function copyExisting(): bool
    {
        return $this->mode === self::MODE_COPY;
    }

    public function overwriteExisting(): bool
    {
        return $this->mode === self::MODE_OVERWRITE;
    }

    public function skipExisting(): bool
    {
        return $this->mode === self::MODE_SKIP;
    }
}
