<?php

namespace App\Modules\Veiculos\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VeiculosController extends Controller
{
    public function example(Request $request)
    {
        return response()->json([
            'message' => 'This is an example response from the Veiculos module.',
        ]);
    }

    // Add your controller methods here
}
