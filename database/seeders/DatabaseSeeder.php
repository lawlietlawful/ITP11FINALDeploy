<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DocumentType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        // Default Admin
        User::updateOrCreate(
            ['email'    => 'lianzyrellelorejo21@gmail.com'],
            [
                'name'     => 'Admin User',
                'password' => Hash::make('3DF162004LIAN'),
                'role'     => 'admin',
            ]
        );

        // Default Document Types
        $types = [
            ['name' => 'Barangay Clearance',       'fee' => 50.00,  'processing_days' => 1],
            ['name' => 'Certificate of Indigency', 'fee' => 0.00,   'processing_days' => 1],
            ['name' => 'Certificate of Residency', 'fee' => 30.00,  'processing_days' => 1],
            ['name' => 'Business Permit',           'fee' => 200.00, 'processing_days' => 3],
            ['name' => 'Barangay ID',               'fee' => 100.00, 'processing_days' => 5],
        ];

        foreach ($types as $type) {
            DocumentType::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
