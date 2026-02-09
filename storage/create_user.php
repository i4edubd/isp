App\Models\User::create(['name' => 'Developer', 'email' => 'developer@example.com', 'password' => bcrypt('password'), 'role' => 'developer']);
exit;
