<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values;

enum Status: int
{
    case Draft = 0;
    case Published = 1;
    case Archived = 2;
}
