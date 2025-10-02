<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Author', 'description' => 'Full system access (undeletable)'],
            ['name' => 'Admin', 'description' => 'User management, role assignment, full settings'],
            ['name' => 'Chairman', 'description' => 'Org KPIs & summary reports'],
            ['name' => 'Director', 'description' => 'Org KPIs & summary reports'],
            ['name' => 'ED', 'description' => 'Org KPIs & summary reports'],
            ['name' => 'GM', 'description' => 'Final approvals; org-wide dashboard'],
            ['name' => 'DGM', 'description' => 'High-level approvals; consolidated reporting'],
            ['name' => 'AGM', 'description' => 'Set company targets; approve NSM data'],
            ['name' => 'NSM', 'description' => 'Assign teams to ZSM/RSM/ASM; approve orders/bills; presentations'],
            ['name' => 'ZSM', 'description' => 'Regional view; budgets & expenses approvals'],
            ['name' => 'RSM', 'description' => 'Self + ASM + MR, approve ASM/MR, assign ASM targets'],
            ['name' => 'ASM', 'description' => 'Self + MR team, approve MR, assign MR targets'],
            ['name' => 'MPO', 'description' => 'Self data only (sales/bills/visits/budgets/assessments/reports)'],
            ['name' => 'MR', 'description' => 'Self data only (sales/bills/visits/budgets/assessments/reports)'],
            ['name' => 'Trainee', 'description' => 'Self data only (sales/bills/visits/budgets/assessments/reports)']
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
