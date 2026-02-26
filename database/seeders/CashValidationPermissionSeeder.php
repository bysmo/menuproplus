<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CashValidationPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (class_exists(\Spatie\Permission\Models\Permission::class)) {
            // Get the module ID for 'Menu' (or typically 'Cashier' if it existed separate, but sticking to existing pattern)
            $module = \DB::table('modules')->where('name', 'Cash')->first();
            
            if (!$module) {
                // Fallback or just null if not strictly using modules table for relation
                echo "⚠️  Cash module not found.\n";
            }

            // Create permission
            \Spatie\Permission\Models\Permission::firstOrCreate([
                'name' => 'validate_cash_session',
                'guard_name' => 'web',
                'module_id' => $module ? $module->id : null
            ]);

            echo "✅ Permission 'validate_cash_session' created successfully!\n";
        }
    }
}
