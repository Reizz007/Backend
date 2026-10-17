<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        // Auto-provision default admin if database is unseeded
        if (!$user && strtolower($validated['email']) === 'admin@synapse.ai' && $validated['password'] === 'admin123') {
            $user = User::updateOrCreate(
                ['email' => 'admin@synapse.ai'],
                [
                    'name' => 'Chief Platform Architect',
                    'password' => Hash::make('admin123'),
                    'role' => 'admin',
                ]
            );

            // Auto-run seeder if products table is empty
            if (\App\Models\Product::count() === 0) {
                try {
                    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
                } catch (\Throwable $e) {
                    // Ignore background seeder errors
                }
            }
        }

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password. Use admin@synapse.ai / admin123',
            ], 401);
        }

        $token = $user->createToken('admin-api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Authentication successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }
}
