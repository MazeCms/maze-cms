<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Route
 *
 * @author nick
 */

namespace root\expansion\exp_contents;

use maze\table\ContentType;
use maze\fields\FieldHelper;
use maze\table\FieldExp;
use maze\helpers\ArrayHelper;
use maze\table\DictionaryTerm;
use maze\table\Routes;
use maze\table\AccessRole;
use maze\table\ContentTypeView;
use maze\table\Contents;
use maze\db\Query;
use RC;
use root\expansion\exp_contents\model\ModelTerm;
use root\expansion\exp_contents\model\ModelContent;

/**
 * Создание маршрута контента
 * 
 * для каталога маршрут создается по правилу
 * 1) ссылка меню дочерней категории/целевая страница(материал, категория)
 * 2) ссылка на каталог/целевая страница(материал, категория)
 * 
 */
class Route extends \maze\url\RouteApp {

    /**
     * @var array dictionary - коллекция имен словаря (имя таблиц без префикса) 
     */
    protected $dictionary;

    /**
     * @var array term - коллекция терминов словаря которые являются каталогами (ключ=term_id)
     */
    protected $term;

    /**
     * @var array contents - коллекция  материалов (ключ=contents_id)
     */
    protected $contents;

    /**
     * @var array aliasContents - коллекция  материалов (ключ=alias)
     */
    protected $aliasContents;

    /**
     * @var array aliasTerms - коллекция терминов словаря которые являются каталогами (ключ=alias)
     */
    protected $aliasTerms;

    /**
     * Разбор маршрута
     * 
     * @return boolean|array
     */
    public function parseRoute() {
        $parsePath = explode('/', $this->path);
        $target = end($parsePath);
        $parsePathBase = count($parsePath) > 1 ? array_slice($parsePath, 0, -1) : null;
        $pathBase = $parsePathBase ? implode('/', $parsePathBase) : null;
        $contents = $this->getAliasContents();
        $terms = $this->getAliasTerms();

        // определяем цели
        if (isset($contents[$target])) {
            if ($pathBase) {
                if ($itemsMenu = $this->findPathMenuByAlias($pathBase)) {
                    if (defined("SITE"))
                        RC::app()->breadcrumbsArr = $itemsMenu->getBreadcrumbs();
                } else {
                    if (!($catalog = $this->findPathCatalogByAlias($pathBase))) {
                        return false;
                    }
                }
            }
            $model = ModelContent::find($contents[$target]->contents_id);
            if (!$model) {
                return false;
            }
            if (defined("SITE"))
                RC::app()->breadcrumbs = ['label' => $model->getTitle()];
            return ['/controller/contents/default', ['contents_id' => $contents[$target]->contents_id]];
        } elseif (isset($terms[$target])) {
            if ($pathBase) {
                if ($itemsMenu = $this->findPathMenuByAlias($pathBase)) {
                    if (defined("SITE"))
                        RC::app()->breadcrumbsArr = $itemsMenu->getBreadcrumbs();
                } else {
                    if (!($catalog = $this->findPathCatalogByAlias($pathBase))) {
                        return false;
                    }
                }
            }

            $model = ModelTerm::find($terms[$target]->term_id);
            if (defined("SITE"))
                RC::app()->breadcrumbs = ['label' => $model->getTitle()];
            return ['/category/category/default', ['term_id' => $terms[$target]->term_id]];
        }
    }

    /**
     * Создание маршрута 
     * 
     * @param URI $path - текущий  оригинальный (/компонент/контроллер/вид/шаблон, параметры) URL
     * @param array $params - дополнительные не обязательные параметры 
     * @return string|boolean - путь к цели
     */
    public function createRoute($path, $params) {


        $pathUrl = $path->getPath();
        $pathParse = explode('/', trim($pathUrl, '/'));

        if (isset($pathParse[0])) {
            $component = $pathParse[0];
        }
        if (isset($pathParse[1])) {
            $controler = $pathParse[1];
        }
        if (isset($pathParse[2])) {
            $view = $pathParse[2];
        }
        if (isset($pathParse[3])) {
            $layout = $pathParse[3];
        }

        $menu = RC::getMenu();

        // маршрут для материала
        if (isset($controler) && $controler == 'controller') {
            $contents_id = $path->getVar('contents_id');
            $contents = $this->getContents();

            // маршрут для типа материала
            if (isset($params['type'])) {
                if (($itemMenu = $menu->findPath(['/contents/type/type/default', ['bundle' => $params['type']]]))) {

                    if (isset($contents[$contents_id])) {
                        return $itemMenu->getPath() . '/' . $contents[$contents_id]->route->alias;
                    }
                }
            }

            if (isset($params['term_id'])) {
                $term_id = $this->findCatalogContent($contents_id, $params['term_id']);
            } else {
                $term_id = $this->findCatalogContent($contents_id);
            }

            if ($term_id) {
                // поиск категории в меню
                $pathCat = $this->findCategoryMenu($term_id);
               
                if (!$pathCat) {
                    $pathCat = $this->getPathCatalog($term_id);
                }

                
                if (isset($contents[$contents_id])) {
                    return $pathCat . '/' . $contents[$contents_id]->route->alias;
                }
            } else {
                if (isset($contents[$contents_id])) {
                    return $contents[$contents_id]->route->alias;
                }
            }
        }
        //находим  пути категории
        elseif (isset($controler) && $controler == 'category') {

            $term_id = $path->getVar('term_id');
            $tepyCon = $path->getVar('type');
            $terms = $this->getTerm();
            
            if (isset($terms[$term_id])) {

                if ($terms[$term_id]->parent != 0) {
                    if (($itemMenu = $menu->findPath(['/contents/category/category/default', ['term_id' => $terms[$term_id]->parent]]))) {
                   
                        return $itemMenu->getPath() . '/' . $terms[$term_id]->route->alias;
                    }
                } else {
                    if (($itemMenu = $menu->findPath(['/contents/catalog/category/catalog', ['bundle' => $terms[$term_id]->bundle]]))) {                   
                        return $itemMenu->getPath() . '/' . $terms[$term_id]->route->alias;
                    }
                }
                
               
                return $this->getPathCatalog($term_id);
            }
        }
    }

    /**
     * Поиск термина материала
     * 
     * @param int $contents_id - id материала
     * @param int $category_id - id термина каталога (словаря)
     * @return int|boolean - id термина
     */
    protected function findCatalogContent($contents_id, $category_id = null) {
        $table = $this->getDictionary();
        if (!$table)
            return false;
        $result = false;
        foreach ($table as $t) {
            $result = RC::getDb()->cache(function($db) use ($contents_id, $category_id, $t) {
                $tableName = '{{%field_term_' . $t . '}}';
                $query = (new Query())->from($tableName)->where(['entry_id' => $contents_id]);
                if ($category_id) {
                    $query->andWhere(['term_id' => $category_id]);
                }
                return $query->one();
            }, null, 'exp_contents');

            if ($result) {
                break;
            }
        }
        return $result ? $result['term_id'] : false;
    }

    /**
     * Поиск категории в меню
     * 
     * @param int $category_id - id термина категории(словаря)
     * @return int|boolean - путь к категории
     */
    protected function findCategoryMenu($category_id) {
        $menu = RC::getMenu();

        if (($itemMenu = $menu->findPath(['/contents/category/category/default', ['term_id' => $category_id]]))) {
            return $itemMenu->getPath();
        }
        return false;
    }

    public function findPathMenuByAlias($path) {
        $menu = RC::getMenu();
        if (($itemMenu = $menu->findAlias($path))) {
            return $itemMenu;
        }
        return false;
    }

    public function findPathCatalogByAlias($path) {
        $parse = explode('/', trim($path, '/'));
        $terms = $this->getAliasTerms();
        $result = [];
        $alias = [];
        $pathFind = [];
        $keyStop = 0;
        foreach ($parse as $i => $p) {

            if ($itemMenu = $this->findPathMenuByAlias(implode('/', array_slice($parse, 0, count($parse) - $i)))) {
                $pathFind = $itemMenu->getPath();
                $pathFind = explode('/', $pathFind);
                if (defined("SITE"))
                    RC::app()->breadcrumbsArr = $itemMenu->getBreadcrumbs();
                $keyStop = count($parse) - $i;
                break;
            }
        }

        for ($i = $keyStop; $i < count($parse); $i++) {
            $p = $parse[$i];
            if (isset($terms[$p])) {
                $alias[] = $terms[$p]->route->alias;
                $result[] = $terms[$p];
            }
        }
        $pathRes = array_merge($pathFind, $alias);
        $prefixPath = !empty($pathFind) ? implode('/', $pathFind) . '/' : '';

        if ($parse == $pathRes) {
            foreach ($result as $i => $term) {
                if ($model = ModelTerm::find($term->term_id)) {

                    $url = \Route::_($prefixPath . implode('/', array_slice($alias, 0, $i + 1)));
                    if (defined("SITE"))
                        RC::app()->breadcrumbs = ['label' => $model->getTitle(), 'url' => $url];
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Создание пути к целевому термину
     * 
     * @param int $category_id - id термина каталога(словаря)
     * @return string - путь к целевой категории
     */
    protected function getPathCatalog($category_id) {
        $terms = $this->getTerm();
        $result = [];
        if (isset($terms[$category_id])) {            
            
            $result[] = $terms[$category_id]->route->alias;
            $parent = $terms[$category_id]->parent;
            
            while (isset($terms[$parent])) {               
                if ($itemMenu = $this->findCategoryMenu($terms[$parent]->term_id)) {
                    $result = array_merge($result, array_reverse(explode('/', $itemMenu)));
                    break;
                }               
                
                $result[] = $terms[$parent]->route->alias;
                $parent = $terms[$parent]->parent;
            }
            if($itemMenu = RC::getMenu()->findPath(['/contents/catalog/category/catalog', ['bundle' => $terms[$category_id]->bundle]])){
                $result[] = $itemMenu->getPath();
            }
            
        }

        
        return implode('/', array_reverse($result));
    }

    protected function getTerm() {
        if ($this->term === null) {
            $this->term = RC::getDb()->cache(function($db) {
                return DictionaryTerm::find()
                                ->from(['dt' => DictionaryTerm::tableName()])
                                ->joinWith('route')
                                ->where(['dt.expansion' => 'dictionary'])->indexBy('term_id')->all();
            }, null, 'exp_contents');
        }
        return $this->term;
    }

    protected function getContents() {
        if ($this->contents === null) {
            $this->contents = RC::getDb()->cache(function($db) {
                return Contents::find()
                                ->from(['c' => Contents::tableName()])
                                ->joinWith('route')
                                ->where(['c.expansion' => 'contents'])->indexBy('contents_id')->all();
            }, null, 'exp_contents');
        }
        return $this->contents;
    }

    public function getAliasContents() {
        if ($this->aliasContents === null) {
            $contents = $this->getContents();
            foreach ($contents as $cont) {
                $this->aliasContents[$cont->route->alias] = $cont;
            }
        }
        return $this->aliasContents;
    }

    public function getAliasTerms() {
        if ($this->aliasTerms === null) {
            $contents = $this->getTerm();
            foreach ($contents as $cont) {
                $this->aliasTerms[$cont->route->alias] = $cont;
            }
        }
        return $this->aliasTerms;
    }

    /**
     * Поиск ссылок на термины словаря
     * 
     * @return array - название полей (таблиц) ссылками на термины
     */
    protected function getDictionary() {
        if ($this->dictionary === null) {
            $catalog = RC::getDb()->cache(function($db) {
                return FieldExp::find()
                                ->from(['fe' => FieldExp::tableName()])
                                ->joinWith(['typeFields'])
                                ->where(['fe.expansion' => 'contents', 'f.type' => 'term'])
                                ->all();
            }, null, 'exp_contents');

            $this->dictionary = [];
            foreach ($catalog as $cat) {
                if (isset($cat->param['dictionary'])) {
                    if (in_array($cat->param['dictionary'], $this->dictionary)) {
                        continue;
                    }
                    $this->dictionary[] = $cat->field_name;
                }
            }
        }

        return $this->dictionary;
    }

}
