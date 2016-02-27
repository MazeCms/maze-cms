<?php

namespace maze\install;

use Text;
use RC;
use XMLConfig;

class WizardInstall extends \maze\base\Object {

    public $project;
    protected $steps = [];
    protected $config;

    public function getSteps() {
        $result = [];
        foreach($this->getConfig()->getXML()->steps->step as $step){
            $result[] = ['name'=>(string)$step['name'], 'title'=>(string)$step['title']];
        }
        return $result;
    }
    
    public function getNextStep($step) {
       $steps = $this->getSteps();
       $result = null;
       foreach($steps as $key=>$s){
           if($s['name'] == $step){
               if(isset($steps[$key+1])){
                   return $steps[$key+1]["name"];
               }
               break;
           }
       }
       return $result;
    }
    
    public function getEndStep() {
        $result = false;
        $steps = $this->getSteps();
        if(!empty($steps)){
            $result = end($steps);
        }
        return $result;
    }
    
    public function firstStep() {
        $steps = $this->getSteps();
        return isset($steps[0]) ? $steps[0]['name'] : null;
    }
    
    public function getIsInActive($step, $curentStep) {
       $steps = $this->getSteps();
       $cIndex = null;
       $sIndex = null;
       foreach($steps as $key=>$s){
           if($s['name'] == $step){
              $sIndex =  $key;
           }
           if($s['name'] == $curentStep){
              $cIndex =  $key;
           }
       }
       return $cIndex > $sIndex;
    }
    
    public function getPrevStep($step) {
        $steps = $this->getSteps();
       $result = null;
       foreach($steps as $key=>$s){
           if($s['name'] == $step){
               if(isset($steps[$key-1])){
                   return $steps[$key-1]["name"];
               }
               break;
           }
       }
       return $result;
    }
    
    public function getConfig() {
        if ($this->config == null) {
            if(file_exists(RC::getAlias('@root/profiles/project/' . $this->project . '/meta.options.xml'))){
                $this->config = new XMLConfig(RC::getAlias('@root/profiles/project/' . $this->project . '/meta.options.xml'));
            }
            
        }
        return $this->config;
    }

    public function getStepModel($name) {
        if (!isset($this->steps[$name])) {
            $this->steps[$name] = RC::createObject(['class' => 'root\profiles\project\\' . $this->project . '\\'.$name]);
        }
        return $this->steps[$name];
    }
    
    public function getAllModel() {
        return $this->steps;
    }

    public function getStepElement($name) {
        $xml = $this->getConfig()->getXML();
        $step = $xml->xpath("//step[@name='" . $name . "']");
        $result = [];
        if (isset($step[0]) && $step[0]->children()->count()) {
            foreach ($step[0]->children() as $elem) {
                if ($elem->getName() == 'element') {
                    $result[] = $elem;
                } elseif ($elem->getName() == 'condition') {
                    $result = array_merge($result, $this->filterCondition($name, $elem));
                }
            }
        }
        return $result;
    }

    protected function filterCondition($nameStep, $condition) {
        $result = [];
        $attr = [];
        $attrModel = [];
        if ($model = $this->getStepModel($nameStep)) {
            foreach ($condition->attributes() as $name => $value) {
                $name = (string) $name;
                $value = (string) $value;
                $attr[$name] = $value;
                if ($model->offsetExists($name)) {
                    $attrModel[$name] = $model->offsetGet($name);
                }
            }
        }
       

        if ($attr == $attrModel) {
            
            if ($condition->children()->count()) {
                foreach ($condition->children() as $elem) {
                    if ($elem->getName() == 'element') {
                        $result[] = $elem;
                    } elseif ($elem->getName() == 'condition') {
                        $result = array_merge($result, $this->filterCondition($elem));
                    }
                }
            }
        }
        return $result;
    }

}
