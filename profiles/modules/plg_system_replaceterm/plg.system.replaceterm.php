<?php

use maze\table\DictionaryTerm;
use maze\fields\FieldHelper;

class Replaceterm_Plugin_System extends Plugin {

    protected $fields;

    public function afterDispatcher() {
        
        $request = RC::app()->request;
        $domain = mb_strtolower($request->getDomain());
        $domain = explode('.', $domain);
        if (count($domain) > 2 && $this->params->getVar("canonical")) {
           array_shift($domain);
           RC::app()->document->deleteLinkTag('rel', 'canonical');
           $url = $request->getProtocol() . implode('.', $domain).URI::instance()->toString(['path', 'query', 'fragment']);          
           RC::app()->document->addlinkTag(['rel'=>'canonical', 'href'=>$url]);
           //RC::app()->document->addMetateg(['name'=>'googlebot', 'content'=>"index, follow"]);
           //RC::app()->document->addMetateg(['name'=>'yandex', 'content'=>"noindex, follow"]);
        }
        
        if ($term = $this->redirectIsBundle()) {
            if($this->params->getVar("reptitle")){
                $title = $this->replaceText(RC::app()->document->get('title'));
                $description = $this->replaceText(RC::app()->document->get('description'));
                $keywords = $this->replaceText(RC::app()->document->get('keywords'));
                RC::app()->document->set("title", $title);
                RC::app()->document->set("description", $description);
                RC::app()->document->set("keywords", $keywords);
                return ;
            }
        }
        $repText = $this->params->getVar("defaultname") ? $this->params->getVar("defaultname") : '';
        $title = RC::app()->document->get('title');
        $title = preg_replace("/\{START\}(.+)\{END\}/", '', $title);

        RC::app()->document->set("title", strtr($title, ['{TERMNAME}' => $repText]));
        $description = $this->replaceText(RC::app()->document->get('description'));
        $description = preg_replace("/\{START\}(.+)\{END\}/", '', $description);
        $keywords = $this->replaceText(RC::app()->document->get('keywords'));
        $keywords = preg_replace("/\{START\}(.+)\{END\}/", '', $keywords);        
        RC::app()->document->set("description", $description);
        RC::app()->document->set("keywords", $keywords);
    }

    public function afterContentFieldRender($view, &$content) {
        $fields = $this->params->getVar("fields");
        if ($fields && is_array($fields) && in_array($view->field->field_name, $fields)) {            
            $content = $this->replaceText($content);
        }
    }
    
     public function afterRenderApplication(&$result) {
        $result = $this->replaceText($result);
    }

    protected function replaceText($text) {

        if (mb_strpos($text, '{TERMNAME}') !== false) {
            $repText = $this->params->getVar("defaultname") ? $this->params->getVar("defaultname") : '';

            if ($term = $this->redirectIsBundle()) {
                $title = $this->getField('title', $this->params->getVar("dict"));
                $title->findData(['entry_id' => $term->term_id]);
                if ($title->data && isset($title->data[0])) {
                    $repText = $title->data[0]->title_value;
                }
            }
            if(empty($repText)){
                $text = preg_replace("/\{START\}(.+)\{END\}/", '', $text);
            }else{
                $text = str_replace(['{START}', '{END}'], '', $text);
            }
            $text = strtr($text, ['{TERMNAME}' => $repText]);
        }
        return $text;
    }

    protected function redirectIsBundle() {
        if(RC::app()->response->getIsRedirection()){
            return false;
        }
        $request = RC::app()->request;
        $domain = mb_strtolower($request->getDomain());
        $domain = explode('.', $domain);
        if (count($domain) > 2) {
            $alias = array_shift($domain);
            $sub = preg_split("/,[\s]+|,/s", $this->params->getVar("notsub"));
            
            $sub = array_map('mb_strtolower', $sub);
            
            if (!in_array($alias, $sub) && $dict = $this->params->getVar("dict")) {
                $term = RC::getDb()->cache(function($db) use ($alias, $dict) {
                    return DictionaryTerm::find()
                                    ->from(['dt' => DictionaryTerm::tableName()])
                                    ->joinWith(['route'])->where(['dt.bundle' => $dict, 'dt.enabled' => 1, 'route.alias' => $alias])->one();
                }, null, 'fw_fields');
                if (!$term && $this->params->getVar("redirect")) {
                    $url = $request->getProtocol() . implode('.', $domain);
                    if(!$this->params->getVar("redirecthome")){
                        $url .= URI::instance()->toString(['path', 'query', 'fragment']);
                    }
                    RC::app()->document->setRedirect($url, 301);
                }
                return $term;
            }
        }

        return false;
    }

    protected function getFields($bundle) {
        if ($this->fields == null) {
            $this->fields = FieldHelper::findAll(['expansion' => 'dictionary', 'bundle' => $bundle, 'active' => 1]);
        }
        return $this->fields;
    }

    protected function getField($name, $bundle) {
        $result = null;
        foreach ($this->getFields($bundle) as $field) {
            if ($field->field_name == $name) {
                $result = $field;
                break;
            }
        }
        return $result;
    }

}

?>