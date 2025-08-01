<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all users with their admin status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all(['id', 'name', 'email', 'is_admin']);
        
        if ($users->isEmpty()) {
            $this->info('No users found.');
            return 0;
        }
        
        $this->info('Users:');
        $this->line('');
        
        foreach ($users as $user) {
            $adminStatus = $user->is_admin ? 'Admin' : 'User';
            $this->line("ID: {$user->id} | Name: {$user->name} | Email: {$user->email} | Role: {$adminStatus}");
        }
        
        return 0;
    }
}
