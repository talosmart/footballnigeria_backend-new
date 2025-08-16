<?php
require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\DB;

$app = require_once 'bootstrap/app.php';
$app->boot();

echo "Total users: " . User::count() . "\n";
echo "Nigerian users: " . User::where('country', 'Nigeria')->count() . "\n\n";

echo "Specific requested users:\n";
$specificUsers = User::whereIn('email', ['lawalthb@gmail.com', 'lawalvct@gmail.com'])->get();
foreach ($specificUsers as $user) {
    echo "- {$user->full_name} ({$user->email})\n";
}

echo "\nFirst 5 Nigerian users:\n";
$nigerianUsers = User::where('country', 'Nigeria')->where('role', 'user')->limit(5)->get();
foreach ($nigerianUsers as $user) {
    echo "- {$user->full_name} - {$user->phone_number}\n";
}
