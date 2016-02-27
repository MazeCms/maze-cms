<?php

namespace maze\base;


interface ViewContextInterface
{
    /**
     * @return string the view path that may be prefixed to a relative view name.
     */
    public function getViewPath();
}
