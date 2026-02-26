<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CashierPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if using Spatie Permission package
        if (class_exists(\Spatie\Permission\Models\Permission::class)) {
            // Get the Cash module (or create a Cashier module if needed)
            $module = \DB::table('modules')->where('name', 'Cash')->first();
            
            if (!$module) {
                echo "⚠️  Cash module not found. Creating permission without module...\n";
                return;
            }

            \Spatie\Permission\Models\Permission::firstOrCreate([
                'name' => 'manage_cashier',
                'guard_name' => 'web',
                'module_id' => $module->id
            ]);

            echo "✅ Permission 'manage_cashier' created successfully!\n";
            echo "ℹ️  Assign this permission to appropriate roles (cashier, manager, admin)\n";
        } else {
            echo "⚠️  Spatie Permission package not found. Please create permission manually.\n";
        }
    }
}
