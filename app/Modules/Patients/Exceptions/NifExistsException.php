<?php

namespace App\Modules\Patients\Exceptions;

use Exception;
use Illuminate\Http\Response;

class NifExistsException extends Exception
{
    protected $code = Response::HTTP_BAD_REQUEST;
    protected $message = 'Este NIF já existe.';
}
