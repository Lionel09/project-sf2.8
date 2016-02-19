<?php

namespace Model;

use Model\om\BaseCmsType;

class CmsType extends BaseCmsType
{
    public function __toString()
    {
        return $this->getTitle();
    }
}
