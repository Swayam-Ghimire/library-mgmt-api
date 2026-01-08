<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates user with the role admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user['name'] = $this->ask('Enter username:');
        $user['email'] = $this->ask('Enter your email:');
        $user['password'] = $this->secret('Enter the password:');
        $user['password_confirmation'] = $this->secret('Confirm password:');
        $credentials = Validator::make($user, [
            'name' => 'required|string|min:5|max:20',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|max:15|confirmed',
        ]);
        if ($credentials->fails()) {
            foreach ($credentials->errors()->all() as $error) {
                $this->error($error);
            }
            return -1;
        }
        $adminRole = Role::where('name', 'admin')->first();
        DB::transaction(function() use($user, $adminRole){
            $user['password'] = Hash::make($user['password']);
            $newUser = User::create($user);
            $newUser->roles()->attach($adminRole->id);
        });
        $this->info('User ' . $user['name'] . ' created successfully');
        return 0;
    }
}
