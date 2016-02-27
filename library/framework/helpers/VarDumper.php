<?php


namespace maze\helpers;

/**
 * VarDumper is intended to replace the buggy PHP function var_dump and print_r.
 * It can correctly identify the recursively referenced objects in a complex
 * object structure. It also has a recursive depth control to avoid indefinite
 * recursive display of some peculiar variables.
 *
 * VarDumper can be used as follows,
 *
 * ~~~
 * VarDumper::dump($var);
 * ~~~
 */
class VarDumper extends BaseVarDumper
{
}
