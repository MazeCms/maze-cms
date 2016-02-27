<?php

namespace maze\access;

use maze\base\Object;


abstract class Rule extends Object
{
    /**
     * @var string name уникальное имя правила
     */
    public $name;
    
     /**
     * @var string exp_name -  имя расширения
     */
    public $expName;
    
    /**
     * @var integer UNIX timestamp  время создания правила
     */
    public $createdAt;
    /**
     * @var integer UNIX timestamp  время обновления правило
     */
    public $updatedAt;


    /**
     * Выполнить правило the rule.
     *
     * @param integer $user -  id текущего пользователя 
     * @param maze\access\Permission $permission разрешение для текущего пользователя
     * @param array $params параметры разрешения.
     * @return boolean true - все ок  делай что задумал, false - тут делать тебе точно нечего 
     */
    abstract public function execute($user, $permission, $params);
}
