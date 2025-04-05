<?php

namespace App\Modules\Patients\Exceptions;

use Exception;
use Illuminate\Http\Response;

class PatientNumberExistsException extends Exception
{
    protected $code = Response::HTTP_BAD_REQUEST;
    protected $message = 'O Número de Utente já existe.';
}
