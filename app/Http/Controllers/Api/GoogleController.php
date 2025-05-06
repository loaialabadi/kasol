<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class GoogleController extends Controller
{
    public function socialRegister(Request $request)
    {
        $provider = $request->input('provider');
        $accessToken = $request->input('access_token');
        $request->validate([
            'provider' => 'required|string',
            'access_token' => 'required|string',
        ]);
        try {
            $googleUser = Socialite::driver('google')->userFromToken($accessToken);
            if (!$googleUser) {
                return $this->sendError('Unable to fetch user from Google', [], 400);
            }
            $email = $googleUser->getEmail();
            $name = $googleUser->getName();

            if (!$email || !$name) {
                return $this->sendError('Invalid Google user data', [], 400);
            }
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'provider_id' => $googleUser->getId(),
                    'provider' => 'google',
                    'phone' => null,
                    'status' => 'accept'
                ]
            );
            $user->assignRole('user');
            $token = $user->createToken('auth_token')->plainTextToken;
            return $this->sendResponse(['token' => $token], 'Successfully registered.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);
        }
    }
}