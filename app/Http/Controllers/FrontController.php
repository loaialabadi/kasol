<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function payment_verify($payment = null)
    {
        if ($payment) {
            return response()->json(['message' => 'Payment verification successful', 'payment' => $payment]);
        }

        return response()->json(['message' => 'No payment ID provided']);
    }

}
