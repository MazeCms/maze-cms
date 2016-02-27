<?php


namespace maze\exception;

/**
 * NotAcceptableHttpException - запрашиваемый ресурс недоступен в том формате,
 * который может принимать клиент(клиент обычно уточняет эти форматы в специальном 
 * HTTP-заголовке Accept).
 *
 * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.7
 */
class NotAcceptableHttpException extends HttpException
{
    /**
     * Constructor.
     * @param string $message error message
     * @param integer $code error code
     * @param \Exception $previous The previous exception used for the exception chaining.
     */
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct(406, $message, $code, $previous);
    }
}
