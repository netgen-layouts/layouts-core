<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class UrlMapper extends Mapper
{
    public function getFormType()
    {
        return UrlType::class;
    }
}
