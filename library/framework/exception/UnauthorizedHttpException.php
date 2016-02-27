<?php

namespace maze\exception;

/**
 * UnauthorizedHttpException - запрос трубет аунтефикации клиента клиента.
 * Для уточнения типа анутификации и области запрашивамого ресурса сервер
 * отправляет HTTP-заголовок WWW-Authentioficate
 *
 * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.2
 */
class UnauthorizedHttpException extends HttpException
{
    /**
     * Constructor.
     * @param string $message error message
     * @param integer $code error code
     * @param \Exception $previous The previous exception used for the exception chaining.
     */
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct(401, $message, $code, $previous);
    }
}
