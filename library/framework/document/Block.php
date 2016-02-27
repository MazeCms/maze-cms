<?php
namespace maze\document;

defined('_CHECK_') or die("Access denied");

use ui\Elements;

class Block extends Elements
{
  
    public $renderInPlace = false;
    
    public $view;


    /**
     * Starts recording a block.
     */
    public function init()
    {
        ob_start();
        ob_implicit_flush(false);
    }

    public function run()
    {
        $block = ob_get_clean();
        if ($this->renderInPlace) {
            echo $block;
        }
        $this->view->blocks[$this->getId()] = $block;
    }
}
