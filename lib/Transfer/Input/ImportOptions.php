<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Input;

final class ImportOptions
{
    private ImportMode $mode = ImportMode::Copy;

    public function setMode(ImportMode $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    public function copyExisting(): bool
    {
        return $this->mode === ImportMode::Copy;
    }

    public function overwriteExisting(): bool
    {
        return $this->mode === ImportMode::Overwrite;
    }

    public function skipExisting(): bool
    {
        return $this->mode === ImportMode::Skip;
    }
}
