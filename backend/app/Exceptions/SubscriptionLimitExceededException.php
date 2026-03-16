<?php

namespace App\Exceptions;

class SubscriptionLimitExceededException extends ApiException
{
    public function __construct(string $message = 'Subscription booking limit exceeded.')
    {
        parent::__construct($message, 409);
    }
}
