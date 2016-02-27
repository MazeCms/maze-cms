<?php
namespace maze\document;

defined('_CHECK_') or die("Access denied");

use RC;
use Exception;
use maze\helpers\StringHelper;
use maze\base\Object;
use maze\helpers\FileHelper;
use maze\base\ResponseFormatterInterface;
use maze\exception\HttpException;

class Response extends Object {

    const FORMAT_HTML = 'html';
    const FORMAT_AJAX = 'ajax';
    const FORMAT_JSONP = 'jsonp';
    const FORMAT_IFRAME = 'iframe'; 
    const FORMAT_RAW = 'raw';
    

    public $format = self::FORMAT_HTML;
    public $content;
    public $statusText = 'OK';
    public $version;
    public $charset = "utf-8";
    public $stream;
    public $data;
    public $isSent = false;
    public $formatters = [
        'html' => 'maze\document\HtmlResponseFormatter',        
        'iframe' => [
                'class'=>'maze\document\HtmlResponseFormatter',
                'useIframe'=>true
            ],
        'ajax' => 'maze\document\AjaxResponseFormatter',
        'jsonp' =>[
                'class'=>'maze\document\AjaxResponseFormatter',
                'useJsonp'=>true
            ] 
    ];
    public static $httpStatuses = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        118 => 'Connection timed out',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        210 => 'Content Different',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        310 => 'Too many Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested range unsatisfiable',
        417 => 'Expectation failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable entity',
        423 => 'Locked',
        424 => 'Method failure',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway or Proxy Error',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        507 => 'Insufficient storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];
    private $_statusCode = 200;

    public function init() {

        $this->getDocument()->setHeader("X-Powered-CMS", "MAZE CMS (Content Management System)");

        if ($this->version === null) {
            if (isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.0') {
                $this->version = '1.0';
            } else {
                $this->version = '1.1';
            }
        }
       
    }

    public function getDocument() {
        return RC::app()->document;
    }
    
    public function clear()
    {
        $this->_statusCode = 200;
        $this->statusText = 'OK';
        $this->data = null;
        $this->stream = null;
        $this->content = null;
        $this->isSent = false;
    }

    protected function prepare() {
       
        
         
        if (isset($this->formatters[$this->format])) {
            $formatter = $this->formatters[$this->format];
            
            if (!is_object($formatter)) {
                $this->formatters[$this->format] = $formatter = RC::createObject($formatter);
            }
            if ($formatter instanceof ResponseFormatterInterface) {
        
                $formatter->format($this);
               
            } else {
                throw new Exception("The '{$this->format}' response formatter is invalid. It must implement the ResponseFormatterInterface.");
            }
        } elseif ($this->format === self::FORMAT_RAW) {
            if(!$this->content) $this->content = $this->data;
        } else {
            throw new Exception("Unsupported response format: {$this->format}");
        }
       
        
         
        if (is_array($this->content)) {
            throw new Exception("Response content must not be an array.");
        } elseif (is_object($this->content)) {
            if (method_exists($this->content, '__toString')) {
                $this->content = $this->content->__toString();
            } else {
                throw new Exception("Response content must be a string or an object implementing __toString().");
            }
        }
    }

    public function getStatusCode() {
        return $this->_statusCode;
    }

    public function setStatusCode($value, $text = null) {
        if ($value === null) {
            $value = 200;
        }
        $this->_statusCode = (int) $value;
        if ($this->getIsInvalid()) {
            throw new Exception("The HTTP status code is invalid: $value");
        }
        if ($text === null) {
            $this->statusText = isset(static::$httpStatuses[$this->_statusCode]) ? static::$httpStatuses[$this->_statusCode] : '';
        } else {
            $this->statusText = $text;
        }
    }

    /**
     * @return boolean whether this response has a valid [[statusCode]].
     */
    public function getIsInvalid() {
        return $this->getStatusCode() < 100 || $this->getStatusCode() >= 600;
    }

    /**
     * @return boolean whether this response is informational
     */
    public function getIsInformational() {
        return $this->getStatusCode() >= 100 && $this->getStatusCode() < 200;
    }

    /**
     * @return boolean whether this response is successful
     */
    public function getIsSuccessful() {
        return $this->getStatusCode() >= 200 && $this->getStatusCode() < 300;
    }

    /**
     * @return boolean whether this response is a redirection
     */
    public function getIsRedirection() {
        return $this->getStatusCode() >= 300 && $this->getStatusCode() < 400;
    }

    /**
     * @return boolean whether this response indicates a client error
     */
    public function getIsClientError() {
        return $this->getStatusCode() >= 400 && $this->getStatusCode() < 500;
    }

    /**
     * @return boolean whether this response indicates a server error
     */
    public function getIsServerError() {
        return $this->getStatusCode() >= 500 && $this->getStatusCode() < 600;
    }

    /**
     * @return boolean whether this response is OK
     */
    public function getIsOk() {
        return $this->getStatusCode() == 200;
    }

    /**
     * @return boolean whether this response indicates the current request is forbidden
     */
    public function getIsForbidden() {
        return $this->getStatusCode() == 403;
    }

    /**
     * @return boolean whether this response indicates the currently requested resource is not found
     */
    public function getIsNotFound() {
        return $this->getStatusCode() == 404;
    }

    /**
     * @return boolean whether this response is empty
     */
    public function getIsEmpty() {
        return in_array($this->getStatusCode(), [201, 204, 304]);
    }

    public function redirect($url, $statusCode = 302)
    {
        
        $url = is_array($url) ? \Route::_($url) : new \URI($url);
      
        if (RC::app()->getRequest()->isPjax()) {
            $this->getDocument()->setHeader('X-Pjax-Url', $url);
        } elseif (RC::app()->getRequest()->isAjax()) {
            $this->getDocument()->setHeader('X-Redirect', $url);
        }else {
            $this->getDocument()->setHeader('Location', $url);
            
        }
        $this->setStatusCode($statusCode);
        
        if(RC::app()->getRequest()->isAjax() && RC::app()->getRequest()->get('clear') == 'ajax'){
            $this->setStatusCode(200);
        }
        

        return $this;
    }
    
    /**
     * Sends a file to the browser.
     *
     * Note that this method only prepares the response for file sending. The file is not sent
     * until [[send()]] is called explicitly or implicitly. The latter is done after you return from a controller action.
     *
     * @param string $filePath the path of the file to be sent.
     * @param string $attachmentName the file name shown to the user. If null, it will be determined from `$filePath`.
     * @param array $options additional options for sending the file. The following options are supported:
     *
     *  - `mimeType`: the MIME type of the content. If not set, it will be guessed based on `$filePath`
     *  - `inline`: boolean, whether the browser should open the file within the browser window. Defaults to false,
     *     meaning a download dialog will pop up.
     *
     * @return static the response object itself
     */
    public function sendFile($filePath, $attachmentName = null, $options = [])
    {
        if (!isset($options['mimeType'])) {
            $options['mimeType'] = FileHelper::getMimeTypeByExtension($filePath);
        }
        if ($attachmentName === null) {
            $attachmentName = basename($filePath);
        }
        $handle = fopen($filePath, 'rb');
        $this->sendStreamAsFile($handle, $attachmentName, $options);

        return $this;
    }

    /**
     * Sends the specified content as a file to the browser.
     *
     * Note that this method only prepares the response for file sending. The file is not sent
     * until [[send()]] is called explicitly or implicitly. The latter is done after you return from a controller action.
     *
     * @param string $content the content to be sent. The existing [[content]] will be discarded.
     * @param string $attachmentName the file name shown to the user.
     * @param array $options additional options for sending the file. The following options are supported:
     *
     *  - `mimeType`: the MIME type of the content. Defaults to 'application/octet-stream'.
     *  - `inline`: boolean, whether the browser should open the file within the browser window. Defaults to false,
     *     meaning a download dialog will pop up.
     *
     * @return static the response object itself
     * @throws HttpException if the requested range is not satisfiable
     */
    public function sendContentAsFile($content, $attachmentName, $options = [])
    {
       

        $contentLength = StringHelper::byteLength($content);
        $range = $this->getHttpRange($contentLength);

        if ($range === false) {
            $this->getDocument()->setHeader('Content-Range', "bytes */$contentLength");
            throw new HttpException(416, 'Requested range not satisfiable');
        }

        $mimeType = isset($options['mimeType']) ? $options['mimeType'] : 'application/octet-stream';
        $this->setDownloadHeaders($attachmentName, $mimeType, !empty($options['inline']), $contentLength);

        list($begin, $end) = $range;
        if ($begin != 0 || $end != $contentLength - 1) {
            $this->setStatusCode(206);
            $headers->setHeader('Content-Range', "bytes $begin-$end/$contentLength");
            $this->content = StringHelper::byteSubstr($content, $begin, $end - $begin + 1);
        } else {
            $this->setStatusCode(200);
            $this->content = $content;
        }
;
        $this->format = self::FORMAT_RAW;

        return $this;
    }
    /**
     * Загрузка файла по частям
     * 
     * @param type $fileSize
     * @return boolean
     */
    protected function getHttpRange($fileSize) {
        if (!isset($_SERVER['HTTP_RANGE']) || $_SERVER['HTTP_RANGE'] === '-') {
            return [0, $fileSize - 1];
        }
        if (!preg_match('/^bytes=(\d*)-(\d*)$/', $_SERVER['HTTP_RANGE'], $matches)) {
            return false;
        }
        if ($matches[1] === '') {
            $start = $fileSize - $matches[2];
            $end = $fileSize - 1;
        } elseif ($matches[2] !== '') {
            $start = $matches[1];
            $end = $matches[2];
            if ($end >= $fileSize) {
                $end = $fileSize - 1;
            }
        } else {
            $start = $matches[1];
            $end = $fileSize - 1;
        }
        if ($start < 0 || $start > $end) {
            return false;
        } else {
            return [$start, $end];
        }
    }

    /**
     * Sends the specified stream as a file to the browser.
     *
     * Note that this method only prepares the response for file sending. The file is not sent
     * until [[send()]] is called explicitly or implicitly. The latter is done after you return from a controller action.
     *
     * @param resource $handle the handle of the stream to be sent.
     * @param string $attachmentName the file name shown to the user.
     * @param array $options additional options for sending the file. The following options are supported:
     *
     *  - `mimeType`: the MIME type of the content. Defaults to 'application/octet-stream'.
     *  - `inline`: boolean, whether the browser should open the file within the browser window. Defaults to false,
     *     meaning a download dialog will pop up.
     *
     * @return static the response object itself
     * @throws HttpException if the requested range cannot be satisfied.
     */
    public function sendStreamAsFile($handle, $attachmentName, $options = []) {
        fseek($handle, 0, SEEK_END);
        $fileSize = ftell($handle);

        $range = $this->getHttpRange($fileSize);
        if ($range === false) {
            RC::app()->document->setHeader('Content-Range', "bytes */$fileSize");
            throw new HttpException(416, 'Requested range not satisfiable');
        }

        list($begin, $end) = $range;
        if ($begin != 0 || $end != $fileSize - 1) {
            $this->setStatusCode(206);
            RC::app()->document->setHeader('Content-Range', "bytes $begin-$end/$fileSize");
        } else {
            $this->setStatusCode(200);
        }

        $mimeType = isset($options['mimeType']) ? $options['mimeType'] : 'application/octet-stream';
        $this->setDownloadHeaders($attachmentName, $mimeType, !empty($options['inline']), $end - $begin + 1);

        $this->format = self::FORMAT_RAW;
        $this->stream = [$handle, $begin, $end];

        return $this;
    }

    /**
     * Sets a default set of HTTP headers for file downloading purpose.
     * @param string $attachmentName the attachment file name
     * @param string $mimeType the MIME type for the response. If null, `Content-Type` header will NOT be set.
     * @param boolean $inline whether the browser should open the file within the browser window. Defaults to false,
     * meaning a download dialog will pop up.
     * @param integer $contentLength the byte length of the file being downloaded. If null, `Content-Length` header will NOT be set.
     * @return static the response object itself
     */
    public function setDownloadHeaders($attachmentName, $mimeType = null, $inline = false, $contentLength = null) {

        $disposition = $inline ? 'inline' : 'attachment';
        RC::app()->document->setHeader('Pragma', 'public')
                ->setHeader('Accept-Ranges', 'bytes')
                ->setHeader('Expires', '0')
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                ->setHeader('Content-Transfer-Encoding', 'binary')
                ->setHeader('Content-Disposition', "$disposition; filename=\"$attachmentName\"");

        if ($mimeType !== null) {
            RC::app()->document->setHeader('Content-Type', $mimeType);
        }

        if ($contentLength !== null) {
            RC::app()->document->setHeader('Content-Length', $contentLength);
        }

        return $this;
    }

    /**

     * **Example**
     *
     * ~~~
     * RC::$app->response->xSendFile('/home/user/Pictures/picture1.jpg');
     * ~~~
     *
     * @param string $filePath file name with full path
     * @param string $attachmentName file name shown to the user. If null, it will be determined from `$filePath`.
     * @param array $options additional options for sending the file. The following options are supported:
     *
     *  - `mimeType`: the MIME type of the content. If not set, it will be guessed based on `$filePath`
     *  - `inline`: boolean, whether the browser should open the file within the browser window. Defaults to false,
     *     meaning a download dialog will pop up.
     *  - xHeader: string, the name of the x-sendfile header. Defaults to "X-Sendfile".
     */
    public function xSendFile($filePath, $attachmentName = null, $options = []) {
        if ($attachmentName === null) {
            $attachmentName = basename($filePath);
        }
        if (isset($options['mimeType'])) {
            $mimeType = $options['mimeType'];
        } elseif (($mimeType = FileHelper::getMimeTypeByExtension($filePath)) === null) {
            $mimeType = 'application/octet-stream';
        }
        if (isset($options['xHeader'])) {
            $xHeader = $options['xHeader'];
        } else {
            $xHeader = 'X-Sendfile';
        }

        $disposition = empty($options['inline']) ? 'attachment' : 'inline';
        RC::app()->document
                ->setHeader('Content-Type', $mimeType)
                ->setHeader($xHeader, $filePath)
                ->setHeader('Content-Disposition', "{$disposition}; filename=\"{$attachmentName}\"");

        return $this;
    }

    public function sendHeaders() {
        if (headers_sent()) {
            return;
        }
        $statusCode = $this->getStatusCode();
        header("HTTP/{$this->version} $statusCode {$this->statusText}");
        RC::app()->document->sendHeader();
        $this->sendCookies();
        
    }
    
    /**
     * Sends the cookies to the client.
     */
    protected function sendCookies()
    {
        $document = RC::app()->document;

        foreach ($document->getCookies() as $cookie) {
            $value = $cookie->value;
            setcookie($cookie->name, $value, $cookie->expire, $cookie->path, $cookie->domain, $cookie->secure, $cookie->httpOnly);
        }
        $document->getCookies()->removeAll();
    }

    public function sendContent() {
         
        if ($this->stream === null) {
            echo $this->content;

            return;
        }

        set_time_limit(0); // Reset time limit for big files
        $chunkSize = 8 * 1024 * 1024; // 8MB per chunk

        if (is_array($this->stream)) {
            list ($handle, $begin, $end) = $this->stream;
            fseek($handle, $begin);
            while (!feof($handle) && ($pos = ftell($handle)) <= $end) {
                if ($pos + $chunkSize > $end) {
                    $chunkSize = $end - $pos + 1;
                }
                echo fread($handle, $chunkSize);
                flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
            }
            fclose($handle);
        } else {
            while (!feof($this->stream)) {
                echo fread($this->stream, $chunkSize);
                flush();
            }
            fclose($this->stream);
        }
    }

    public function send() {
        if ($this->isSent) {
            return;
        }
       
        $this->prepare();       
        $this->sendHeaders();
        $this->sendContent();
        $this->isSent = true;
        
    }

}

?>