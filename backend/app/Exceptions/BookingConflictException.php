<?php

namespace App\Exceptions;

class BookingConflictException extends ApiException
{
    public function __construct(string $message = 'Booking time conflicts with an existing meeting.')
    {
        parent::__construct($message, 409);
    }
}
