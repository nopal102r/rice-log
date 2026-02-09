<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Deposit;
use App\Models\User;

$pendingDeposits = Deposit::where('status', 'pending')->with('user')->get();

if ($pendingDeposits->isEmpty()) {
    echo "No pending deposits found.\n";
}

foreach ($pendingDeposits as $d) {
    $job = $d->user ? $d->user->job : 'N/A';
    echo "ID: {$d->id}, User: {$d->user->name}, Job: {$job}, Type: {$d->type}, Weight: {$d->weight}, Boxes: {$d->box_count}, Money: {$d->money_amount}, Wage: {$d->wage_amount}, Total: {$d->total_price}\n";
}
