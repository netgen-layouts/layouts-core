<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver;

enum Status: string
{
    case Published = 'published';

    case Draft = 'draft';

    case Archived = 'archived';
}
