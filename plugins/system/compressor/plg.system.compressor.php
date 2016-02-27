<?php

use plg\system\compressor\lib\CssUriRewriter;
use plg\system\compressor\lib\YUICompressor;
use plg\system\compressor\lib\MinifyHTML;

include_once __DIR__ . '/lib/cssmin.php';

class Compressor_Plugin_System extends Plugin {

    public function beforeGetScripts(&$arr) {
        if ($this->params->getVar("enablescript")) {
            $res = $this->getCompressScript($arr);
            if ($res) {
                $arr = $res;
            }
        }
    }

    public function beforeGetStylesheet(&$arr) {
        if ($this->params->getVar("enablecss")) {
            $res = $this->getCompressCss($arr);
            if ($res) {
                $arr = $res;
            }
        }
    }

    public function afterRenderApplication(&$result) {

        if ($this->params->getVar("enablehtml")) {
            $result = MinifyHTML::minify($result, ['cleanComments' => true,
//                        'jsMinifier' => function($js) {
//                            return (new JSqueeze())->squeeze($js);
//                        },
                        'cssMinifier' => function($css) {
                            return CssMin::minify($css);
                        }
            ]);
        }
    }

    protected function getCompressScript(array $arr) {
        $disabled = explode(',', ini_get('disable_functions'));
        if($disabled){
            $disabled = array_map(function($val){
                return trim($val);
            }, $disabled);
        }
 
        if(!function_exists('exec') || !function_exists('shell_exec') || !function_exists('proc_open') ||
                in_array('exec', $disabled) || in_array('shell_exec', $disabled) ||
                in_array('proc_open', $disabled)){
            return false;
        }
      
        $descriptorspec = array(
            0 => array("pipe", "r"), // stdin
            1 => array("pipe", "w"), // stdout
            2 => array("pipe", "w")   // stderr
        );

        $process = proc_open('java  -version', $descriptorspec, $pipes, null, null);

        if (is_resource($process)) {

            fclose($pipes[0]);

            $tmpout = '';
            $tmperr = '';

            $output = stream_get_contents($pipes[1]);
            $error_output = stream_get_contents($pipes[2]);

            fclose($pipes[1]);
            fclose($pipes[2]);
            $return_var = proc_close($process);
        }else{
            return false;
        }
        if(empty($output)){
            $output = $error_output;
        }
 
        if(!$output){
            return false;
        }else{
            if(mb_stripos($output, 'java version') === false){
                return false;
            }
        }

        $pathcache = RC::getAlias(trim($this->params->getVar("pathcache"), '/\\') . '/compress/js');
        if (!is_dir($pathcache)) {
            mkdir($pathcache, 0777, true);
        }

        $result = [];
        
        
        foreach ($arr as $key => $script) {
            $path = RC::getAlias('@root' . $script['src']);
            $newPath = $pathcache . '/' . sha1($script['src']) . '.js';
            if (!file_exists($newPath)) {
                if (file_exists($path)) {
                    $conpressor = new YUICompressor;
                    $conpressor->addString(file_get_contents($path));
                    $textJs = $conpressor->compress();
                    if(mb_strpos($textJs, '[ERROR]') !== false){
                        $result[]=$script;
                        continue;
                    }
                    file_put_contents($newPath, $textJs);
                }
            }
            if($this->params->getVar("enablepath")){
              $script['data-path'] = $script['src'];  
            }
            
            $script['src'] = str_replace(RC::getAlias("@root"), "", $newPath);
            $result[]=$script;
            
        }

        return $result;

    }

    protected function getCompressCss(array $arr) {
        $pathcache = RC::getAlias(trim($this->params->getVar("pathcache"), '/\\') . '/compress/css');
        if (!is_dir($pathcache)) {
            mkdir($pathcache, 0777, true);
        }

        $result = [];
        
        foreach ($arr as $key => $script) {
            $path = RC::getAlias('@root' . $script['href']);
            $newPath = $pathcache . '/' . sha1($script['href']) . '.css';
            if (!file_exists($newPath)) {
                if (file_exists($path)) {
                    $textcss = CssMin::minify((new CssUriRewriter($path))->rewrite(), [
                                "ImportImports" => false,
                                "RemoveComments" => true,
                                "RemoveEmptyRulesets" => false,
                                "RemoveEmptyAtBlocks" => false,
                                "ConvertLevel3AtKeyframes" => false,
                                "ConvertLevel3Properties" => false,
                                "Variables" => false,
                                "RemoveLastDelarationSemiColon" => false
                    ]);
                    
                    file_put_contents($newPath, $textcss);
                }
            }
            if($this->params->getVar("enablepath")){
              $script['data-path'] = $script['href'];
            }
            
            $script['href'] = str_replace(RC::getAlias("@root"), "", $newPath);
            $result[]=$script;
            
        }

        return $result;
    }

}

?>