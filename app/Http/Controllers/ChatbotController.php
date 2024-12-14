<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class ChatbotController extends Controller
{
    public function handleChat(Request $request)
    {
        $message = $request->input('message');




        return response()->json();
    }
}
