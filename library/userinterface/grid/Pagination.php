<?php

/*
 * выводит ссылки постраничной навигации
 */

/**
 * Пагинатор
 *
 * @author nick
 */

namespace ui\grid;

use ui\Elements;
use maze\helpers\Html;
use RC;
use URI;

class Pagination extends Elements {

    /**
     * @var int - текушая страница по умолчанию
     */
    public $page = 1;

    /**
     * @var string|array - url адрес страницы по умочанию используется текущий
     */
    public $url;
    
    /**
     * @var array ulOptions - опции тега ul 
     */
    public $ulOptions = ['class' => 'pagination'];

    /**
     * @var array linkOptions - опции ссылки
     */
    public $linkOptions = [];

    /**
     * @var array activeOptions - опции тега li активной ссылки
     */
    public $activeOptions = ['class' => 'active'];

    /**
     * @var array disabledOptions - опции тега li не активной ссылки
     */
    public $disabledOptions = ['class' => 'disabled'];

    /**
     * @var int sizeLink - число ссылок слева и спарва от активной ссылки
     */
    public $sizeLink = 3;

    /**
     * @var string labelNext - следующая страница
     */
    public $labelNext = '<span aria-hidden="true">></span>';

    /**
     * @var string labelPrevious - предыдущая страница
     */
    public $labelPrevious = '<span aria-hidden="true"><</span>';

    /**
     * @var string labelStar - начало списка
     */
    public $labelStar = '<span aria-hidden="true"><<</span>';

    /**
     * @var string labelEnd - конец списка
     */
    public $labelEnd = '<span aria-hidden="true">>></span>';

    /**
     * @var string - название переменной текущая страница
     */
    public $pageName = "page";

    /**
     * @var PaginationFormat model -  модель пагинатора
     */
    public $model;

    public function init() {
        $this->url = $this->url ? $this->url : URI::instance();
    }

    public function run() {

        $html = '';
        if (is_object($this->model) && ($this->model instanceof PaginationFormat)) {
            if ($this->model->countPage > 1) {
                $html = Html::beginTag('ul', $this->ulOptions);

                if ($this->labelStar) {
                    $options = $this->model->page == 1 ? $this->disabledOptions : [];
                    $html .= Html::tag('li', $this->renderLink($this->labelStar, null, $this->linkOptions), $options);
                }

                if ($this->labelPrevious) {
                    $options = $this->model->page == 1 ? $this->disabledOptions : [];
                    $page = $this->model->page == 1 || $this->model->page == 2 ? null : $this->model->page - 1;
                    $html .= Html::tag('li', $this->renderLink($this->labelStar, $page, $this->linkOptions), $options);
                }

                $sizeLeft = ($this->model->page-1 > $this->sizeLink ? $this->sizeLink : $this->model->page-1);

                $leftPage = $this->model->page - $sizeLeft;
                for ($i = 0; $i < $sizeLeft; $i++) {                    
                    $html .= Html::tag('li', $this->renderLink($leftPage, ($leftPage == 1 ? null : $leftPage), $this->linkOptions));
                    $leftPage++;
                }

                $html .= Html::tag('li', Html::a($this->model->page, '#', $this->linkOptions), $this->activeOptions);

                $sizeRight = $this->model->countPage - $this->model->page > $this->sizeLink ? $this->sizeLink : ($this->model->countPage - $this->model->page);
                $rightPage = $this->model->page + 1;
                for ($i = 0; $i < $sizeRight; $i++) {
                    $html .= Html::tag('li', $this->renderLink($rightPage, $rightPage, $this->linkOptions));
                    $rightPage++;
                }

                if ($this->labelNext) {
                    $options = $this->model->page == $this->model->countPage ? $this->disabledOptions : [];
                    $page = $this->model->page == $this->model->countPage ? $this->model->countPage : $this->model->page + 1;
                    $html .= Html::tag('li', $this->renderLink($this->labelNext, $page, $this->linkOptions), $options);
                }

                if ($this->labelEnd) {
                    $options = $this->model->page == $this->model->countPage ? $this->disabledOptions : [];
                    $html .= Html::tag('li', $this->renderLink($this->labelEnd, $this->model->countPage, $this->linkOptions), $options);
                }



                $html .= Html::endTag('ul');
            }
        }

        return $html;
    }

    protected function renderLink($text, $page, $options) {
        $url = new URI($this->url);
        if ($page) {
            $url->setVar($this->pageName, $page);
        }else{
            $url->delVar($this->pageName);
        }

        return Html::a($text, $url->toString(['path', 'query', 'fragment']), $options);
    }

}
