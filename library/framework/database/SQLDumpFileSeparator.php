<?php

namespace maze\database;



class SQLDumpFileSeparator {
    
    private $query = [];


    public function __construct($file_name) {
        static::separate_queries($file_name, function($qu){
            $this->setQuery(trim($qu));
        });
    }
    
    public function setQuery($param) {
        $this->query[] = $param;
    }
    public function getQuery() {
        return $this->query;
    }

    public static function separate_queries($file_name, $callback) {
        $query = '';
        $opening_token = '';
        $is_comment = false;

        while ($input_string = static::get_next_chunk($file_name)) {
            $opening_pos = - strlen($opening_token);
            $curren_pos = 0;
            $i = strlen($input_string);

            while ($i--) {
                if ($opening_token) {
                    list($closing_token, $closing_pos) = static::get_closing_token(
                                    $input_string, $opening_pos + strlen($opening_token), $opening_token
                    );

                    if ($closing_token) {
                        if ($opening_token === '--' || $opening_token == '#' || $is_comment) {
                            $query .= substr(
                                    $input_string, $curren_pos, $opening_pos - $curren_pos
                            );
                        } else {
                            $query .= substr(
                                    $input_string, $curren_pos, $closing_pos + strlen($closing_token) - $curren_pos
                            );
                        }

                        $curren_pos = $closing_pos + strlen($closing_token);
                        $opening_token = '';
                        $opening_pos = 0;
                    } else {
                        $query .= substr($input_string, $curren_pos);
                        break;
                    }
                } else {
                    list($opening_token, $opening_pos) = static::get_opening_token($input_string, $curren_pos);

                    if ($opening_token === ';') {
                        $query .= substr(
                                $input_string, $curren_pos, $opening_pos - $curren_pos + 1
                        );

                        call_user_func($callback, $query);

                        $query = '';
                        $curren_pos = $opening_pos + strlen($opening_token);
                        $opening_token = '';
                        $opening_pos = 0;
                    } elseif (!$opening_token) {
                        $query .= substr($input_string, $curren_pos);
                        break;
                    } else {
                        if ($opening_token === '/*' && substr($input_string, $opening_pos, 3) !== '/*!') {
                            $is_comment = true;
                        } else {
                            $is_comment = false;
                        }
                    }
                }
            }
        }

        if ($query) {
            call_user_func($callback, $query);
            $query = '';
        }

        return 1;
    }

    //read from insql var or file
    public static function get_next_chunk($file_name, $buffer = 65536) {
        static $file_handle;

        if (!$file_handle) {
            $file_handle = fopen($file_name, "r+b");

            if (!$file_handle) {
                throw new Exception("Can't open [$file_name] file. ");
            }
        }

        return fread($file_handle, $buffer);
    }

    public static function get_opening_token($input_string, $pos) {
        $opening_token = null;
        $opening_pos = null;

        if (preg_match('~(\/\*|^--|(?<=\s)--|#|\'|"|;)~', $input_string, $matches, PREG_OFFSET_CAPTURE, $pos)) {
            $opening_token = $matches[1][0];
            $opening_pos = $matches[1][1];
        }

        return array($opening_token, $opening_pos);
    }

    public static function get_closing_token($input_string, $pos, $opening_token) {
        $closing_characters = array(
            // opening character => closing character regexes
            '\'' => '(?<!\\\\)\'|(\\\\+)\'',
            '"' => '(?<!\\\\)"',
            '/*' => '\*\/',
            '#' => '[\r\n]+',
            '--' => '[\r\n]+',
        );

        $closing_token = null;
        $closing_pos = null;
        if (!isset($closing_characters[$opening_token])) {
            return array($closing_token, $closing_pos);
        }
        $closing_character_regex = $closing_characters[$opening_token];

        if (preg_match('~(' . $closing_character_regex . ')~', $input_string, $matches, PREG_OFFSET_CAPTURE, $pos)) {
            $closing_token = $matches[1][0];
            $closing_pos = $matches[1][1];
            if (isset($matches[2][0])) {
                $sl = strlen($matches[2][0]);

                if ($opening_token == "'" && $sl) {
                    if ($sl % 2) {
                        list($closing_token, $closing_pos) = static::get_closing_token(
                                        $input_string, $closing_pos + strlen($closing_token), $opening_token
                        );
                    } else {
                        $closing_pos += strlen($closing_token) - 1;
                        $closing_token = "'";
                    }
                }
            }
        }

        return array($closing_token, $closing_pos);
    }

}
