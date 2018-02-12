<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Design\Twig;

use Twig\Loader\FilesystemLoader as BaseFilesystemLoader;

class LegacyFilesystemLoader extends BaseFilesystemLoader
{
    public function getSource($name)
    {
    }
}
