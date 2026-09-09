<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Validator;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (! config('initial-admin.enabled')) {
            return;
        }

        $credentials = Validator::make(
            [
                'name' => config('initial-admin.name'),
                'email' => config('initial-admin.email'),
                'password' => config('initial-admin.password'),
            ],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'password' => ['required', 'string', 'min:12'],
            ]
        )->validate();

        User::query()->firstOrCreate(
            [
                'email' => mb_strtolower(
                    trim($credentials['email'])
                ),
            ],
            [
                'name' => trim($credentials['name']),
                'password' => $credentials['password'],
                'role' => 'admin',
                'status' => true,
            ]
        );
    }
}
