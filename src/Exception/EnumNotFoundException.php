<?php

namespace CancioLabs\Ds\Bag\Exception;

use Exception;
use Throwable;

class EnumNotFoundException extends Exception
{

    public function __construct(string $enumFQN = "", int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('Unable to find the Enum from the given full qualified name ("%s").', $enumFQN);

        parent::__construct($message, $code, $previous);
    }

}