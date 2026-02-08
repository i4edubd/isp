<?php

namespace Database\Seeders;

use App\Models\department;
use App\Models\operator;
use Illuminate\Database\Seeder;

class DefaultDepartmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultDepartments = ['Billing', 'Sales', 'Support'];

        // Get all group admins and operators
        $operators = operator::whereIn('role', ['group_admin', 'operator'])->get();

        foreach ($operators as $operator) {
            foreach ($defaultDepartments as $departmentName) {
                // Check if department already exists for this operator
                $exists = department::where('operator_id', $operator->id)
                    ->where('name', $departmentName)
                    ->exists();

                if (!$exists) {
                    department::create([
                        'operator_id' => $operator->id,
                        'name' => $departmentName,
                    ]);
                }
            }
        }

        $this->command->info('Default departments seeded successfully.');
    }
}
