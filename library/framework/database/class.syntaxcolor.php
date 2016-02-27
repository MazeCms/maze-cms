<?php

defined('_CHECK_') or die("Access denied");

class SyntaxColor {

    private static $instance;
    protected $keywords = array(
        "avg", "as", "auto_increment", "and", "analyze", "alter",
        "asc", "all", "after", "add", "action", "against",
        "aes_encrypt", "aes_decrypt", "ascii", "abs", "acos",
        "asin", "atan", "authors", "between", "btree", "backup",
        "by", "binary", "before", "binlog", "benchmark", "blob",
        "bigint", "bit_count", "bit_or", "bit_and", "bin",
        "bit_length", "both", "create", "count", "comment",
        "check", "char", "concat", "cipher", "changed", "column",
        "columns", "change", "constraint", "cascade", "checksum",
        "cross", "close", "concurrent", "commit", "curdate",
        "current_date", "curtime", "current_time",
        "current_timestamp", "cast", "convert", "connection_id",
        "coalesce", "case", "conv", "concat_ws", "char_length",
        "character_length", "ceiling", "cos", "cot", "crc32",
        "compress", "delete", "drop", "default", "distinct",
        "decimal", "date", "describe", "data", "desc",
        "dayofmonth", "date_add", "database", "databases",
        "double", "duplicate", "disable", "datetime", "dumpfile",
        "distinctrow", "delayed", "dayofweek", "dayofyear",
        "dayname", "day_minute", "date_format", "date_sub",
        "decode", "des_encrypt", "des_decrypt", "degrees",
        "decompress", "dec", "engine", "explain", "enum",
        "escaped", "execute", "extended", "errors", "exists",
        "enable", "enclosed", "extract", "encrypt", "encode",
        "elt", "export_set", "escape", "exp", "end", "from",
        "float", "flush", "fields", "file", "for", "fast", "full",
        "fulltext", "first", "foreign", "force", "from_days",
        "from_unixtime", "format", "found_rows", "floor", "field",
        "find_in_set", "group", "grant", "grants", "global",
        "get_lock", "greatest", "having", "high_priority",
        "handler", "hour", "hex", "insert", "into", "inner",
        "int", "ifnull", "if", "isnull", "in", "infile", "is",
        "interval", "ignore", "identified", "index", "issuer",
        "integer", "is_free_lock", "inet_ntoa", "inet_aton",
        "instr", "join", "kill", "key", "keys", "left", "load",
        "local", "limit", "like", "lock", "lpad", "last_insert_id",
        "logs", "length", "longblob", "longtext", "last", "lines",
        "low_priority", "locate", "ltrim", "leading", "lcase",
        "lower", "load_file", "ln", "log", "least", "month", "mod",
        "max", "min", "mediumint", "medium", "master", "modify",
        "mediumblob", "mediumtext", "match", "mode", "monthname",
        "mid", "minute", "master_pos_wait", "make_set", "null",
        "not", "now", "none", "new", "numeric", "no", "natural",
        "next", "nullif", "national", "nchar", "on", "or",
        "optimize", "order", "optionally", "option", "outfile",
        "open", "offset", "outer", "old_password", "ord", "oct",
        "octet_length", "primary", "password", "privileges",
        "process", "processlist", "purge", "partial", "procedure",
        "prev", "period_add", "period_diff", "position", "pow",
        "power", "pi", "quick", "quarter", "quote", "right",
        "repair", "restore", "reset", "regexp", "references",
        "replace", "revoke", "reload", "require", "replication",
        "read", "rand", "rename", "real", "restrict",
        "release_lock", "rpad", "rtrim", "repeat", "reverse",
        "rlike", "round", "radians", "rollup", "select", "sum",
        "set", "show", "substring", "smallint", "super", "subject",
        "status", "slave", "session", "start", "share",
        "straight_join", "sql_small_result", "sql_big_result",
        "sql_buffer_result", "sql_cache", "sql_no_cache",
        "sql_calc_found_rows", "second", "sysdate", "sec_to_time",
        "system_user", "session_user", "substring_index", "std",
        "stddev", "soundex", "space", "strcmp", "sign", "sqrt",
        "sin", "straight", "sleep", "text", "truncate", "table",
        "tinyint", "tables", "to_days", "temporary", "terminated",
        "to", "types", "time", "timestamp", "tinytext",
        "tinyblob", "transaction", "time_format", "time_to_sec",
        "trim", "trailing", "tan", "then", "update", "union",
        "using", "unsigned", "unlock", "usage", "use_frm",
        "unix_timestamp", "unique", "use", "user", "ucase",
        "upper", "uuid", "values", "varchar", "variables",
        "version", "variance", "varying", "where", "with",
        "warnings", "write", "weekday", "week", "when", "xor",
        "year", "yearweek", "year_month", "zerofill");
    // ключевые команды с новой строки	
    protected $keywords_line = array("from", "left", "inner", "outer",
        "where", "set", "values", "order", "group", "having", "limit",
        "on", "and", "case");
    protected $prefix;

    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $conf = RC::getConfig();
        $set_db = $conf->get("database");
        $prefix = array();

        foreach ($set_db as $db) {
            $prefix[] = $db["dbprefix"];
        }

        $this->prefix = implode("|", $prefix);
    }

    public function parse($query) {
        $tmp = htmlspecialchars($query);
        $tmp = str_replace("\r", '', $tmp);
        $tmp = trim(str_replace("\n", "\r\n", $tmp)) . "\r\n";

        $quote_list_text = array();
        $quote_list_symbols = array();

        $k = 0;
        $quotes = array();
        // Обработать экранированные кавычки
        preg_match_all("/\\\'|\\\&quot;/is", $tmp, $quotes);
        array_unique($quotes);
        if (count($quotes)) {
            foreach ($quotes[0] as $i) {
                $k++;
                $quote_list_symbols[$k] = $i;
                $tmp = str_replace($i, '<symbol' . $k . '>', $tmp);
            }
        }

        $matches = array(
            "/(&quot;|'|`)(.*?)(\\1)/is", // текст в кавычках
            "/\/\*.*?\*\//s", // текст комментария
            "/ \-\-.*\x0D\x0A/", // текст ' --' комментария
            "/ #.*\x0D\x0A/", // текст ' #' комментария
        );

        // Обработать текст
        foreach ($matches as $match) {
            // Обработать текст
            $found = array();
            preg_match_all($match, $tmp, $found);
            $quotes = (array) $found[0];
            array_unique($quotes);
            if (count($quotes)) {
                foreach ($quotes as $i) {
                    $k++;
                    $quote_list_text[$k] = $i;
                    $tmp = str_replace($i, '<text' . $k . '>', $tmp);
                }
            }
        }

        // Служебные слова MySQL
        $line_replace = array();
        foreach ($this->keywords_line as $keyword) {
            $line_replace[] = '/\b' . $keyword . '\b/i';
        }

        $replace = array();
        foreach ($this->keywords as $keyword) {
            $replace[] = '/\b' . $keyword . '\b/i';
        }

        // Выделить служебные слова в тексте запроса
        $tmp = $this->colorCommand($replace, $tmp);

        // Выделить числовые значения в тексте запроса
        $tmp = $this->colorNumber($tmp);

        // Выделить скобки в тексте запроса
        $tmp = $this->colorHooks($tmp);

        // Выделить скобки в название таблиц запроса
        $tmp = $this->colorTableName($tmp);


        // Вернуть обратно строки в кавычках
        if (count($quote_list_text)) {
            $quote_list_text = array_reverse($quote_list_text, true);
            foreach ($quote_list_text as $k => $i) {
                $tmp = str_replace('<text' . $k . '>', '<span style="color:#777;">' . $i . '</span>', $tmp);
            }
        }
        // Вернуть обратно экранированные символы
        if (count($quote_list_symbols)) {
            $quote_list_symbols = array_reverse($quote_list_symbols, true);
            foreach ($quote_list_symbols as $k => $i) {
                $tmp = str_replace('<symbol' . $k . '>', $i, $tmp);
            }
        }
        // ключевые слова с новой строки
        $tmp = preg_replace($line_replace, '<br/>&#160;&#160;\\0', $tmp);

        // Вернуть подсвеченный текст запроса
        return nl2br(trim($tmp));
    }

    protected function colorCommand($replace, $str) {
        return preg_replace_callback($replace, function($matches){
            return "<b style=\"color:#0000FF\">".mb_strtoupper($matches[0])."</b>";
        }, $str);
    }

    protected function colorNumber($str) {
        return preg_replace('/\b([\.0-9]+)\b/', '<b style="color:#008000">\1</b>', $str);
    }

    protected function colorHooks($str) {
        return preg_replace('/([\(\)])/', '<b style="color:#FF0000">\1</b>', $str);
    }

    protected function colorTableName($str) {
        return preg_replace("/((" . $this->prefix . ")_[a-zA-Z-0-9_]+)/", '<b style="color:#ED3F1C">\\1</b>', $str);
    }

}

?>