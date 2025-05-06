<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class VerificationController extends Controller
{
    // Email verification method
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        // Check the email hash verification
        if ($user->hasVerifiedEmail() || $user->getEmailVerificationUrlAttribute() !== $hash) {
            return response()->json(['message' => 'Invalid or expired verification link'], 400);
        }

        $user->markEmailAsVerified();

        // Trigger the Verified event (optional)
        event(new Verified($user));

        return response()->json(['message' => 'Email verified successfully']);
    }

    // Resend the verification email
    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified']);
        }

        // Send verification email again
        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification email resent']);
    }
}
