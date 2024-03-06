<?php

namespace CancioLabs\Ds\Bag\Exception;

use Exception;
use Throwable;

class ElementNotFoundException extends Exception
{

    public function __construct(string $key = "", int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('Unable to remove the element "%s" as it was not found in the bag.', $key);

        parent::__construct($message, $code, $previous);
    }

}