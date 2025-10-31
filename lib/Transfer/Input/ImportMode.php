<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Input;

enum ImportMode: string
{
    case Copy = 'copy';

    case Overwrite = 'overwrite';

    case Skip = 'skip';
}
