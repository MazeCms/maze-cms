<?php
namespace maze\access;

use maze\base\Object;


class Permission extends Object
{
 
    /**
     * @var string expName -  имя расширения
     */
    public $expName;
    
    /**
     * @var string expNnameame -  имя разрешения
     */
    public $name;
    
    /**
     * @var string title -  название разрешения
     */
    public $title;
    
    /**
     * @var string ruleName -  имя правила
     */
    public $ruleName;

    /**
     * @var string description -  подсказка для правила
     */
    public $description;
}
