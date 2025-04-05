<?php

namespace App\Modules\ActiveCampaign\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActiveCampaignController extends Controller
{
    public function example(Request $request)
    {
        return response()->json([
            'message' => 'This is an example response from the ActiveCampaign module.',
        ]);
    }

    // Add your controller methods here
}
