<?php

defined('_CHECK_') or die("Access denied");

class Menu {

    private $current_time;
    
    protected $id_lang;
    
    /**
     * @var  maze\menu\Item  - текущая домашняя страница 
     */
    protected $_home;
    
    /**
     * @var array - все актиные пункты меню
     */
    protected $_items;

    /**
     * @var array - копилка путей
     */
    protected $_path = [];

    /**
     * @var array - копилка алисов 
     */
    protected $_alias = [];
    
    /**
     * @var array - копилка пунктов меню отсортированная по полю 
     */
    protected $sort;
    
    /**
     * @var array - копилка объектов пункта меню maze\menu\Item  
     */
    protected $objItems;


    private static $_instance;

    public static function instance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __construct() {
        $this->current_time = date("Y-m-d H:i:s");
        $this->id_lang = RC::app()->lang->getIdLang();
      
    }

    public function getMenuGroup($id_group) {
        return (new maze\table\Menu())->findAll(['id_group' => $id_group]);
    }

    public function getGroup() {
        return (new maze\table\MenuGroup())->find()->orderBy('ordering')->all();
    }

    public function getHome() {
        if ($this->_home == null) {
            $this->_home = false;
            $home_page =  RC::getDb()->cache(function($db){ 
                return (new maze\table\Menu())->find()
                    ->home()
                    ->enable()
                    ->langItems($this->id_lang)
                    ->activeDate()
                    ->orderBy('time_inactive, ordering')
                    ->all();
                }, null, 'fw_menu');
            $default_page = array();

            foreach ($home_page as $item) {
                $default_page[$item->id_lang] = $item;
            }
            
            
            if ($this->id_lang && isset($default_page[$this->id_lang])) {
                $this->_home = $this->createItem($default_page[$this->id_lang]);
            } elseif (isset($home_page[0])) {
                $this->_home = $this->createItem($home_page[0]);
            } 
        }
        
        return $this->_home;
    }

    protected function getItems() {
        if ($this->_items === null) {
            $access = RC::app()->access->getIdRole();

            $access = !empty($access) ? $access : 0;

            $this->_items = RC::getDb()->cache(function($db) use ($access){
               return  maze\table\Menu::find()         
                    ->from(['m' => maze\table\Menu::tableName()])
                    ->joinWith(['expansion', 'route', 'accessRole' => function($query) use($access) {
                            $query->andWhere(['or', ['in', 'ar.id_role', $access], 'ar.id_role is null']);
                        }])
                    ->where(['m.enabled' => '1'])
                    ->andWhere('(m.id_lang = :id_lang OR m.id_lang is NULL)', [':id_lang' => $this->id_lang])
                    ->andWhere('(m.time_active <= :time_active OR m.time_active is null)', [':time_active' => $this->current_time])
                    ->andWhere('(m.time_inactive >= :time_active OR m.time_inactive is null)', [':time_active' => $this->current_time])
                    ->orderBy('m.id_group, m.parent, m.ordering, m.id_exp')
                   ->all();         
         }, null, 'fw_menu');
        }
        return $this->_items;
    }
    
    protected function getItemsByAlias(){
        $items = $this->getItems();
        if(!isset($this->sort['alias'])){
            $this->sort['alias'] = [];
            foreach($items as $item){
                $this->sort['alias'][$item->route->alias] = $item;
            }             
        }
        
        return $this->sort['alias'];
    }
    
    protected function getItemsByIDs(){
        $items = $this->getItems();
        if(!isset($this->sort['id'])){
            $this->sort['id'] = [];
            foreach($items as $item){
                $this->sort['id'][$item->id_menu] = $item;
            }             
        }        
        return $this->sort['id'];
    }
    
    public function getItemsByIDMenu($id) {
        $items = $this->getItems();
        if(!isset($this->sort['id_group'][$id])){
            $this->sort['id_group'][$id] = [];
            foreach($items as $item){
                if($id == $item->id_group){
                     $this->sort['id_group'][$id][] = $this->createItem($item);
                }
            }             
        }        
        return $this->sort['id_group'][$id];
    }
    
    
    /**
     * Создание пункта меню
     * 
     * @param maze\table\Menu $item - объект ActiveRecorde
     * @return maze\menu\Item - экземпляр класса пункта меню
     */
    protected function createItem(maze\table\Menu $item){
        if(!isset($this->objItems[$item->id_menu])){
            $attr = $item->attributes;
            unset($attr['id_group'], $attr['time_active'], $attr['time_inactive'], $attr['ordering'], $attr['enabled'], $attr['id_lang'], $attr['routes_id']);
            $attr['alias'] = $item->route->alias;
            $attr['meta_title'] =$item->route->meta_title;
            $attr['meta_robots'] =$item->route->meta_robots;
            $attr['meta_key'] = $item->route->meta_keywords;
            $attr['meta_des'] = $item->route->meta_description;
            $this->objItems[$item->id_menu] = RC::createObject(array_merge(['class' => 'maze\menu\Item', 'menu'=>$this], $attr));
        }
        
        return $this->objItems[$item->id_menu];
    }

    

    /**
     * Получить пункт меню по алису
     * 
     * @param string $alias - синноним URL
     * @return maze\menu\Item|false - экземпляр класса пункта меню
     */    
    public function getItemByAlias($alias){
        $items = $this->getItemsByAlias();
        if(isset($items[$alias])){    
            return $this->createItem($items[$alias]);
        }
        return false;
    }
    
    /**
     * Получить пункт меню по ID
     * 
     * @param int $id - ID пункта меню
     * @return maze\menu\Item|false - экземпляр класса пункта меню
     */    
    public function getItemByID($id){
        $items = $this->getItemsByIDs();
        if(isset($items[$id])){
            return $this->createItem($items[$id]);
        }
        return false;
    }

    /**
     * Поиск целевого пункта меню по алису
     * 
     * @param type $alias - путь к пункту меню 
     *  например path/mypath/target
     *  RC::getMenu()->findAlias('reguser/settings/login')
     */
    public function findAlias($alias) {
        
        if(!isset($this->_alias[$alias])){
            $this->_alias[$alias] = false;
            $target = explode("/", trim(trim($alias), '/'));
            $target = end($target);        
            if($result = $this->getItemByAlias($target)){
                if($alias == $result->path){
                    $this->_alias[$alias] = $result;
                }
            }
        }

        return $this->_alias[$alias];
    }
    
    
    /**
     * Поиск пункта меню РАСШИРЕНИЯ по параметрам пути 
     * 
     * @param string|array $path - путь к текущему расширению
     *   ввиде строки 'component/controller/view/layout' 
     *   ввиде массива ['component/controller/view/layout', ['run'=>'action', 'id'=>$id]]
     *   RC::getMenu()->findPath('user/controller/user/default);
     * @return maze\menu\Item - экземпляр класса пункта меню
     */
    public function findPath($path){
        if(is_array($path)){
            $key = (new URI($path))->toString(['path', 'query', 'fragment']);
        }else{
            $key = $path;
        }

        if(!isset($this->_path[$key])){
            $this->_path[$key] = false;
            $paramLink = ['url_param'=>null];
            $url_param = null;
            
            if(is_string($path)){
                $pathReal = (new URI($path))->toString(['path']);
                $paramLink['url_param'] = (new URI($path))->getQuery(true);
               
            }elseif(is_array($path)){
                $pathReal = $path[0];
                if(isset($path[1])){
                    $paramLink['url_param'] = $path[1];
                }            
            }
            
        
            if(isset($pathReal)){
                $pathReal = explode('/',  trim(trim($pathReal), '/'));
             
                if(isset($pathReal[0])){
                    $paramLink['component'] = $pathReal[0];
                }            
                if(isset($pathReal[1])){
                    $paramLink['controller'] = $pathReal[1];
                }            
                if(isset($pathReal[2])){
                    $paramLink['view'] = $pathReal[2];
                }
                if(isset($pathReal[3])){
                    $paramLink['layout'] = $pathReal[3];
                }
                 
                $items = $this->getItems(); 
                
                foreach($items as $item){
                     
                    if($item->typeLink == 'expansion'){
                        if($paramLink == $item->paramLink){
                            $this->_path[$key] = $this->createItem($item);
                            break;
                        }
                    }
                }
                
            } 
        }

        return $this->_path[$key];
    }


   

}

?>