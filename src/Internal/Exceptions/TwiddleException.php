<?php

namespace Fabis94\Twiddle\Internal\Exceptions;

use Throwable;

class TwiddleException extends \Exception
{

    /**
     * TwiddleException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        $message = "Twiddle algorithm failed.",
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}