<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserProfileResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile(Request $request) {
        // Get logged in user profile info
        $user = $request->user()->load(['roles', 'loans.book']);
        return new UserProfileResource($user);
    }


}
