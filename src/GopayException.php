<?php

namespace Markette\GopaySimple;

use Exception;
use RuntimeException;
use stdClass;

final class GopayException extends RuntimeException
{

    /** @var array */
    private $args;

    /**
     * @param string $message
     * @param int $code
     * @param Exception $previous
     * @param array $args
     */
    public function __construct($message, $code = 0, Exception $previous = NULL, $args = [])
    {
        parent::__construct($message, $code, $previous);
        $this->args = $args;
    }

    /**
     * @param stdClass $error
     * @throw self
     */
    public static function format(stdClass $error)
    {
        return sprintf('#%s (%s)[%s] %s', $error->error_code, $error->scope, $error->field, $error->message ? $error->message : $error->description);
    }

}
