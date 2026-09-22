<?php

namespace CancioLabs\Ds\Bag\Exception;

use Exception;
use Throwable;

class EnumNotFoundException extends Exception
{

    public function __construct(string $enumFQN = "", int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('The name "%s" does not identify a backed enum.', $enumFQN);

        parent::__construct($message, $code, $previous);
    }

}