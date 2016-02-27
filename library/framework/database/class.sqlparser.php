<?php

defined('_CHECK_') or die("Access denied");

class SQLParser {

    protected $file;
    protected $query_parse = array();
    private static $instance;

    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        
    }

    public function loadParser($file) {
        $this->setFile($file);
        $this->startParsing();

        return $this->query_parse;
    }

    public function setFile($file) {
        $this->query_parse = array();

        $this->file = $file;
    }

    public function getFile() {
        return $this->file;
    }

    protected function setQueryParse($text) {
        array_push($this->query_parse, $text);
    }

    protected function startParsing() {

        $file = $this->getFile();

        $content = file_get_contents($file);
        $content = preg_replace(array("#\/\*.+\*\/.+#",
            "#--.*#",
            "/\#.*/",
            "#[\n]{1,}#s"), array("", "", "", "\n"), $content);
        $file_content = explode("\n", $content);


        $query = "";

        foreach ($file_content as $sql_line) {
            if (trim($sql_line) !== "" && strpos($sql_line, "--") === false) {
                $query .= $sql_line;

                if (preg_match("/([^;]*);/", $sql_line)) {
                    $query = trim($query);
                    $query = substr($query, 0, strlen($query) - 1);
                    $this->setQueryParse($query);
                    $query = "";
                }
            }
        }
        return true;
    }

}

?>