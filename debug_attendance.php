<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Absence;

echo "--- Users ---\n";
$users = User::all();
foreach ($users as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Role: {$user->role}, Status: {$user->status}, Job: {$user->job}\n";
}

echo "\n--- Recent Absences ---\n";
$absences = Absence::latest()->take(10)->get();
foreach ($absences as $absence) {
    echo "ID: {$absence->id}, User ID: {$absence->user_id}, Type: {$absence->type}, Created At: {$absence->created_at}\n";
}
