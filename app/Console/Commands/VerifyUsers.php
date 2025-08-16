<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class VerifyUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:verify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify the Nigerian users were created successfully';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("User Statistics:");
        $this->info("Total users: " . User::count());
        $this->info("Nigerian users: " . User::where('country', 'Nigeria')->count());

        $this->info("\nSpecific requested users:");
        $specificUsers = User::whereIn('email', ['lawalthb@gmail.com', 'lawalvct@gmail.com'])->get();
        foreach ($specificUsers as $user) {
            $this->info("✓ {$user->full_name} ({$user->email}) - {$user->phone_number}");
        }

        $this->info("\nSample of Nigerian users:");
        $nigerianUsers = User::where('country', 'Nigeria')
            ->where('role', 'user')
            ->limit(8)
            ->get();

        foreach ($nigerianUsers as $user) {
            $this->line("- {$user->full_name} - {$user->phone_number} - {$user->email}");
        }

        $this->info("\nNigerian users verification completed!");
    }
}
