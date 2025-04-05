<?php

namespace App\Http\Controllers;

use App\Events\NotificationPushed;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function message(Request $request)
    {


        try {
            broadcast(new NotificationPushed($request->get('message')));

            return response()->json([
                'message' => $request->get('message'),
            ]);
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
