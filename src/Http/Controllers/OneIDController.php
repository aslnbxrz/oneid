<?php

namespace Aslnbxrz\OneId\Http\Controllers;

use Aslnbxrz\OneId\Facades\OneID;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OneIDController
{
    /**
     * Handle OneID authentication callback
     */
    public function handle(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request data',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = OneID::handle($request->get('code'));

        return response()->json($result->toArray());
    }

    /**
     * Handle OneID logout
     */
    public function logout(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'access_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request data',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = OneID::logout($request->get('access_token'));

        return response()->json($result->toArray());
    }

    /**
     * Redirect to OneID authorization page
     */
    public function redirect(): RedirectResponse
    {
        $authUrl = OneID::getAuthorizationUrl();

        return redirect($authUrl);
    }
}
