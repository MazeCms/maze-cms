<?php

namespace maze\helpers;

/**
 * BaseFileHelper provides concrete implementation for [[FileHelper]].
 *
 * Do not use BaseFileHelper. Use [[FileHelper]] instead.

 * @since 2.0
 */
class BaseFileHelper {

    const PATTERN_NODIR = 1;
    const PATTERN_ENDSWITH = 4;
    const PATTERN_MUSTBEDIR = 8;
    const PATTERN_NEGATIVE = 16;

    /**
     * Normalizes a file/directory path.
     * The normalization does the following work:
     *
     * - Convert all directory separators into `DIRECTORY_SEPARATOR` (e.g. "\a/b\c" becomes "/a/b/c")
     * - Remove trailing directory separators (e.g. "/a/b/c/" becomes "/a/b/c")
     * - Turn multiple consecutive slashes into a single one (e.g. "/a///b/c" becomes "/a/b/c")
     * - Remove ".." and "." based on their meanings (e.g. "/a/./b/../c" becomes "/a/c")
     *
     * @param string $path the file/directory path to be normalized
     * @param string $ds the directory separator to be used in the normalized result. Defaults to `DIRECTORY_SEPARATOR`.
     * @return string the normalized file/directory path
     */
    public static function normalizePath($path, $ds = DIRECTORY_SEPARATOR) {
        $path = rtrim(strtr($path, ['/' => $ds, '\\' => $ds]), $ds);
        if (strpos($ds . $path, "{$ds}.") === false && strpos($path, "{$ds}{$ds}") === false) {
            return $path;
        }
        // the path may contain ".", ".." or double slashes, need to clean them up
        $parts = [];
        foreach (explode($ds, $path) as $part) {
            if ($part === '..' && !empty($parts) && end($parts) !== '..') {
                array_pop($parts);
            } elseif ($part === '.' || $part === '' && !empty($parts)) {
                continue;
            } else {
                $parts[] = $part;
            }
        }
        $path = implode($ds, $parts);
        return $path === '' ? '.' : $path;
    }

    /**
     * Determines the MIME type of the specified file.
     * This method will first try to determine the MIME type based on
     * [finfo_open](http://php.net/manual/en/function.finfo-open.php). If this doesn't work, it will
     * fall back to [[getMimeTypeByExtension()]].
     * @param string $file the file name.
     * @param string $magicFile name of the optional magic database file, usually something like `/path/to/magic.mime`.
     * This will be passed as the second parameter to [finfo_open](http://php.net/manual/en/function.finfo-open.php).
     * @param boolean $checkExtension whether to use the file extension to determine the MIME type in case
     * `finfo_open()` cannot determine it.
     * @return string the MIME type (e.g. `text/plain`). Null is returned if the MIME type cannot be determined.
     */
    public static function getMimeType($file, $magicFile = null, $checkExtension = true) {
        if (function_exists('finfo_open')) {
            $info = finfo_open(FILEINFO_MIME_TYPE, $magicFile);
            if ($info) {
                $result = finfo_file($info, $file);
                finfo_close($info);
                if ($result !== false) {
                    return $result;
                }
            }
        }

        return $checkExtension ? static::getMimeTypeByExtension($file) : null;
    }

    /**
     * Determines the MIME type based on the extension name of the specified file.
     * This method will use a local map between extension names and MIME types.
     * @param string $file the file name.
     * @param string $magicFile the path of the file that contains all available MIME type information.
     * If this is not set, the default file aliased by `@yii/util/mimeTypes.php` will be used.
     * @return string the MIME type. Null is returned if the MIME type cannot be determined.
     */
    public static function getMimeTypeByExtension($file, $magicFile = null) {
        static $mimeTypes = [];
        if ($magicFile === null) {
            $magicFile = __DIR__ . '/mimeTypes.php';
        }
        if (!isset($mimeTypes[$magicFile])) {
            $mimeTypes[$magicFile] = require($magicFile);
        }
        if (($ext = pathinfo($file, PATHINFO_EXTENSION)) !== '') {
            $ext = strtolower($ext);
            if (isset($mimeTypes[$magicFile][$ext])) {
                return $mimeTypes[$magicFile][$ext];
            }
        }

        return null;
    }

    public static function getExtensionByMimeType($type, $magicFile = null) {
        static $mimeTypes = [];
        if ($magicFile === null) {
            $magicFile = __DIR__ . '/mimeTypes.php';
        }
        if (!isset($mimeTypes[$magicFile])) {
            $mimeTypes[$magicFile] = require($magicFile);
        }
        if ($type) {
            $type = strtolower($type);
            if (in_array($type, $mimeTypes[$magicFile])) {
                return array_search($type, $mimeTypes[$magicFile]);
            }
        }

        return null;
    }
  

    /**
     * Копировать файлы папку
     * 
     * @param string $src - абсолютный путь (что копируем)
     * @param type $dst - абсолютный путь (куда копируем)
     * @param array $options
     *      dirMode - права на папку
     *      fileMode - права на файл     * 
     *      beforeCopy - функция обратного вызова должа возвращать true для текущей итерации 
     *                  аргументы $from - что копируем , $to - куда копием
     *      afterCopy - функция обратного вызова после копирования, для текущей итерации 
     *                  аргументы $from - что копируем , $to - куда копием
     *      rename -    boolean переименовать дубликаты файла папки
     *      space -     boolean заменять пробелы в названии файла или папки
     *      afterName - функция обратного вызова после переименования $src - новый путь к файлу, 
     *                  $name - новое имя или false, $old - оригинальный путь
     */
    public static function copy($src, $dst, $options = []) {

        if (!is_dir($dst)) {
            static::createDirectory($dst, isset($options['dirMode']) ? $options['dirMode'] : 0775, true);
        }


        if (is_dir($src)) {
            $handle = opendir($src);

            if ($handle === false) {
                throw new \Exception('Невозможно открыть каталог: ' . $src);
            }

            while (($file = readdir($handle)) !== false) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $from = $src . DIRECTORY_SEPARATOR . $file;
                $to = static::getName($dst . DIRECTORY_SEPARATOR . $file, $options);
                if (isset($options['beforeCopy']) && !call_user_func($options['beforeCopy'], $from, $to)) {
                    continue;
                }
                if (is_file($from)) {
                    copy($from, $to);
                    if (isset($options['fileMode'])) {
                        @chmod($to, $options['fileMode']);
                    }
                } else {
                    static::copy($from, $to, $options);
                }
                if (isset($options['afterCopy'])) {
                    call_user_func($options['afterCopy'], $from, $to);
                }
            }
            closedir($handle);
        } elseif (is_file($src)) {
            $file = pathinfo($src, PATHINFO_BASENAME);
            $to = static::getName($dst . DIRECTORY_SEPARATOR . $file, $options);
            if (isset($options['beforeCopy']) && !call_user_func($options['beforeCopy'], $src, $to)) {
                return;
            }
            copy($src, $to);

            if (isset($options['fileMode'])) {
                @chmod($to, $options['fileMode']);
            }

            if (isset($options['afterCopy'])) {
                call_user_func($options['afterCopy'], $src, $to);
            }
        }
    }

    public static function getName($src, $options = []) {
        $name = false;
        $old = $src;
        if (isset($options['rename']) && $options['rename']) {
            $count = 0;
            if(isset($options['space']) && $options['space']){
                $path = explode(DIRECTORY_SEPARATOR, $src);
                $target = array_pop($path);
                $src = implode(DIRECTORY_SEPARATOR, $path) . DIRECTORY_SEPARATOR . preg_replace('/[\s]+/', '_', $target);
            }
            if (is_file($src)) {
                while (file_exists($src)) {
                    $count++;
                    $dir = pathinfo($src, PATHINFO_DIRNAME);
                    $file = pathinfo($src, PATHINFO_FILENAME);
                    $ext = pathinfo($src, PATHINFO_EXTENSION);
                    if(preg_match('/^.+(_\d+)/', $file, $matches)){
                        $file = str_replace($matches[1], '_'.$count, $file);
                    }else{
                        $file .= '_'.$count;
                    }
                    $name = $file .'.' . $ext;
                    $src = $dir . DIRECTORY_SEPARATOR .$name;
                }
            } elseif (is_dir($src)) {
                while (is_dir($src)) {
                    $count++;
                    $path = explode(DIRECTORY_SEPARATOR, $src);
                    $target = array_pop($path);
                    if(preg_match('/^.+(_\d+)/', $target, $matches)){
                        $name = str_replace($matches[1], '_'.$count, $name);
                    }
                    else{
                        $name = $target .'_'.$count;
                    }
                    
                    $src = implode(DIRECTORY_SEPARATOR, $path) . DIRECTORY_SEPARATOR . $name;
                }
            }
        }
        
        if (isset($options['afterName'])) {
            call_user_func($options['afterName'], $src, $name, $old);
        }

        return $src;
    }
    
   

    /**
     * Перемисть файлы папку
     * 
     * @param type $src  - copy
     * @param type $dst - copy
     * @param array $options - copy
     */
    public static function move($src, $dst, $options = []) {
        if ($src == $dst)
            return;

        static::copy($src, $dst, $options);
        static::remove($src);
    }

    public static function remove($path) {
        if (is_file($path)) {
            unlink($path);
        } else {
            static::removeDirectory($path);
        }
    }

    /**
     * Removes a directory (and all its content) recursively.
     * @param string $dir the directory to be deleted recursively.
     */
    public static function removeDirectory($dir) {
        if (!is_dir($dir) || !($handle = opendir($dir))) {
            return;
        }
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_file($path)) {
                unlink($path);
            } else {
                static::removeDirectory($path);
            }
        }
        closedir($handle);
        rmdir($dir);
    }

    /**
     * Returns the files found under the specified directory and subdirectories.
     * @param string $dir the directory under which the files will be looked for.
     * @param array $options options for file searching. Valid options are:
     *
     * - filter: callback, a PHP callback that is called for each directory or file.
     *   The signature of the callback should be: `function ($path)`, where `$path` refers the full path to be filtered.
     *   The callback can return one of the following values:
     *
     *   * true: the directory or file will be returned (the "only" and "except" options will be ignored)
     *   * false: the directory or file will NOT be returned (the "only" and "except" options will be ignored)
     *   * null: the "only" and "except" options will determine whether the directory or file should be returned
     *
     * - except: (кроме) array, list of patterns excluding from the results matching file or directory paths.
     *   Patterns ending with '/' apply to directory paths only, and patterns not ending with '/'
     *   apply to file paths only. For example, '/a/b' matches all file paths ending with '/a/b';
     *   and '.svn/' matches directory paths ending with '.svn'.
     *   If the pattern does not contain a slash /, it is treated as a shell glob pattern and checked for a match against the pathname relative to $dir.
     *   Otherwise, the pattern is treated as a shell glob suitable for consumption by fnmatch(3) with the FNM_PATHNAME flag: wildcards in the pattern will not match a / in the pathname.
     *   For example, "views/*.php" matches "views/index.php" but not "views/controller/index.php".
     *   A leading slash matches the beginning of the pathname. For example, "/*.php" matches "index.php" but not "views/start/index.php".
     *   An optional prefix "!" which negates the pattern; any matching file excluded by a previous pattern will become included again.
     *   If a negated pattern matches, this will override lower precedence patterns sources. Put a backslash ("\") in front of the first "!"
     *   for patterns that begin with a literal "!", for example, "\!important!.txt".
     *   Note, the '/' characters in a pattern matches both '/' and '\' in the paths.
     * - only:(только) array, list of patterns that the file paths should match if they are to be returned. Directory paths are not checked against them.
     *   Same pattern matching rules as in the "except" option are used.
     *   If a file path matches a pattern in both "only" and "except", it will NOT be returned.
     * - recursive: boolean, whether the files under the subdirectories should also be looked for. Defaults to true.
     * @return array files found under the directory. The file list is sorted.
     * @throws InvalidParamException if the dir is invalid.
     */
    public static function findFiles($dir, $options = []) {
        if (!is_dir($dir)) {
            throw new \Exception('The dir argument must be a directory.');
        }
        $dir = rtrim($dir, DIRECTORY_SEPARATOR);
        if (!isset($options['basePath'])) {
            $options['basePath'] = realpath($dir);
            // this should also be done only once
            if (isset($options['except'])) {
                foreach ($options['except'] as $key => $value) {
                    if (is_string($value)) {
                        $options['except'][$key] = self::parseExcludePattern($value);
                    }
                }
            }
            if (isset($options['only'])) {
                foreach ($options['only'] as $key => $value) {
                    if (is_string($value)) {
                        $options['only'][$key] = self::parseExcludePattern($value);
                    }
                }
            }
        }
        $list = [];
        $handle = opendir($dir);
        if ($handle === false) {
            throw new \Exception('Unable to open directory: ' . $dir);
        }
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (static::filterPath($path, $options)) {
                if (is_file($path)) {
                    $list[] = $path;
                } elseif (!isset($options['recursive']) || $options['recursive']) {
                    $list = array_merge($list, static::findFiles($path, $options));
                }
            }
        }
        closedir($handle);

        return $list;
    }

    /**
     * Checks if the given file path satisfies the filtering options.
     * @param string $path the path of the file or directory to be checked
     * @param array $options the filtering options. See [[findFiles()]] for explanations of
     * the supported options.
     * @return boolean whether the file or directory satisfies the filtering options.
     */
    public static function filterPath($path, $options) {
        if (isset($options['filter'])) {
            $result = call_user_func($options['filter'], $path);
            if (is_bool($result)) {
                return $result;
            }
        }

        if (empty($options['except']) && empty($options['only'])) {
            return true;
        }

        $path = str_replace('\\', '/', $path);

        if (!empty($options['except'])) {
            if (($except = self::lastExcludeMatchingFromList($options['basePath'], $path, $options['except'])) !== null) {
                return $except['flags'] & self::PATTERN_NEGATIVE;
            }
        }

        if (!is_dir($path) && !empty($options['only'])) {
            if (($except = self::lastExcludeMatchingFromList($options['basePath'], $path, $options['only'])) !== null) {
                // don't check PATTERN_NEGATIVE since those entries are not prefixed with !
                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * Creates a new directory.
     *
     * This method is similar to the PHP `mkdir()` function except that
     * it uses `chmod()` to set the permission of the created directory
     * in order to avoid the impact of the `umask` setting.
     *
     * @param string $path path of the directory to be created.
     * @param integer $mode the permission to be set for the created directory.
     * @param boolean $recursive whether to create parent directories if they do not exist.
     * @return boolean whether the directory is created successfully
     */
    public static function createDirectory($path, $mode = 0775, $recursive = true) {
        if (is_dir($path)) {
            return true;
        }
        $parentDir = dirname($path);
        if ($recursive && !is_dir($parentDir)) {
            static::createDirectory($parentDir, $mode, true);
        }
        $result = mkdir($path, $mode);
        chmod($path, $mode);

        return $result;
    }

    /**
     * Performs a simple comparison of file or directory names.
     *
     * Based on match_basename() from dir.c of git 1.8.5.3 sources.
     *
     * @param string $baseName file or directory name to compare with the pattern
     * @param string $pattern the pattern that $baseName will be compared against
     * @param integer|boolean $firstWildcard location of first wildcard character in the $pattern
     * @param integer $flags pattern flags
     * @return boolean wheter the name matches against pattern
     */
    private static function matchBasename($baseName, $pattern, $firstWildcard, $flags) {
        if ($firstWildcard === false) {
            if ($pattern === $baseName) {
                return true;
            }
        } elseif ($flags & self::PATTERN_ENDSWITH) {
            /* "*literal" matching against "fooliteral" */
            $n = StringHelper::byteLength($pattern);
            if (StringHelper::byteSubstr($pattern, 1, $n) === StringHelper::byteSubstr($baseName, -$n, $n)) {
                return true;
            }
        }

        return fnmatch($pattern, $baseName, 0);
    }

    /**
     * Compares a path part against a pattern with optional wildcards.
     *
     * Based on match_pathname() from dir.c of git 1.8.5.3 sources.
     *
     * @param string $path full path to compare
     * @param string $basePath base of path that will not be compared
     * @param string $pattern the pattern that path part will be compared against
     * @param integer|boolean $firstWildcard location of first wildcard character in the $pattern
     * @param integer $flags pattern flags
     * @return boolean wheter the path part matches against pattern
     */
    private static function matchPathname($path, $basePath, $pattern, $firstWildcard, $flags) {
        // match with FNM_PATHNAME; the pattern has base implicitly in front of it.
        if (isset($pattern[0]) && $pattern[0] == '/') {
            $pattern = StringHelper::byteSubstr($pattern, 1, StringHelper::byteLength($pattern));
            if ($firstWildcard !== false && $firstWildcard !== 0) {
                $firstWildcard--;
            }
        }

        $namelen = StringHelper::byteLength($path) - (empty($basePath) ? 0 : StringHelper::byteLength($basePath) + 1);
        $name = StringHelper::byteSubstr($path, -$namelen, $namelen);

        if ($firstWildcard !== 0) {
            if ($firstWildcard === false) {
                $firstWildcard = StringHelper::byteLength($pattern);
            }
            // if the non-wildcard part is longer than the remaining pathname, surely it cannot match.
            if ($firstWildcard > $namelen) {
                return false;
            }

            if (strncmp($pattern, $name, $firstWildcard)) {
                return false;
            }
            $pattern = StringHelper::byteSubstr($pattern, $firstWildcard, StringHelper::byteLength($pattern));
            $name = StringHelper::byteSubstr($name, $firstWildcard, $namelen);

            // If the whole pattern did not have a wildcard, then our prefix match is all we need; we do not need to call fnmatch at all.
            if (empty($pattern) && empty($name)) {
                return true;
            }
        }

        return fnmatch($pattern, $name, FNM_PATHNAME);
    }

    /**
     * Scan the given exclude list in reverse to see whether pathname
     * should be ignored.  The first match (i.e. the last on the list), if
     * any, determines the fate.  Returns the element which
     * matched, or null for undecided.
     *
     * Based on last_exclude_matching_from_list() from dir.c of git 1.8.5.3 sources.
     *
     * @param string $basePath
     * @param string $path
     * @param array $excludes list of patterns to match $path against
     * @return string null or one of $excludes item as an array with keys: 'pattern', 'flags'
     * @throws InvalidParamException if any of the exclude patterns is not a string or an array with keys: pattern, flags, firstWildcard.
     */
    private static function lastExcludeMatchingFromList($basePath, $path, $excludes) {
        foreach (array_reverse($excludes) as $exclude) {
            if (is_string($exclude)) {
                $exclude = self::parseExcludePattern($exclude);
            }
            if (!isset($exclude['pattern']) || !isset($exclude['flags']) || !isset($exclude['firstWildcard'])) {
                throw new \Exception('If exclude/include pattern is an array it must contain the pattern, flags and firstWildcard keys.');
            }
            if ($exclude['flags'] & self::PATTERN_MUSTBEDIR && !is_dir($path)) {
                continue;
            }

            if ($exclude['flags'] & self::PATTERN_NODIR) {
                if (self::matchBasename(basename($path), $exclude['pattern'], $exclude['firstWildcard'], $exclude['flags'])) {
                    return $exclude;
                }
                continue;
            }

            if (self::matchPathname($path, $basePath, $exclude['pattern'], $exclude['firstWildcard'], $exclude['flags'])) {
                return $exclude;
            }
        }

        return null;
    }

    /**
     * Processes the pattern, stripping special characters like / and ! from the beginning and settings flags instead.
     * @param string $pattern
     * @return array with keys: (string) pattern, (int) flags, (int|boolean)firstWildcard
     * @throws InvalidParamException if the pattern is not a string.
     */
    private static function parseExcludePattern($pattern) {
        if (!is_string($pattern)) {
            throw new \Exception('Exclude/include pattern must be a string.');
        }
        $result = [
            'pattern' => $pattern,
            'flags' => 0,
            'firstWildcard' => false,
        ];
        if (!isset($pattern[0])) {
            return $result;
        }

        if ($pattern[0] == '!') {
            $result['flags'] |= self::PATTERN_NEGATIVE;
            $pattern = StringHelper::byteSubstr($pattern, 1, StringHelper::byteLength($pattern));
        }
        $len = StringHelper::byteLength($pattern);
        if ($len && StringHelper::byteSubstr($pattern, -1, 1) == '/') {
            $pattern = StringHelper::byteSubstr($pattern, 0, -1);
            $len--;
            $result['flags'] |= self::PATTERN_MUSTBEDIR;
        }
        if (strpos($pattern, '/') === false) {
            $result['flags'] |= self::PATTERN_NODIR;
        }
        $result['firstWildcard'] = self::firstWildcardInPattern($pattern);
        if ($pattern[0] == '*' && self::firstWildcardInPattern(StringHelper::byteSubstr($pattern, 1, StringHelper::byteLength($pattern))) === false) {
            $result['flags'] |= self::PATTERN_ENDSWITH;
        }
        $result['pattern'] = $pattern;

        return $result;
    }

    /**
     * Searches for the first wildcard character in the pattern.
     * @param string $pattern the pattern to search in
     * @return integer|boolean position of first wildcard character or false if not found
     */
    private static function firstWildcardInPattern($pattern) {
        $wildcards = ['*', '?', '[', '\\'];
        $wildcardSearch = function ($r, $c) use ($pattern) {
            $p = strpos($pattern, $c);

            return $r === false ? $p : ($p === false ? $r : min($r, $p));
        };

        return array_reduce($wildcards, $wildcardSearch, false);
    }

}
