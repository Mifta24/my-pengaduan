<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $rolesByUser = [];

        DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', User::class)
            ->whereIn('roles.name', ['admin', 'user'])
            ->orderBy('roles.name')
            ->select('model_has_roles.model_id', 'roles.name')
            ->each(function ($assignment) use (&$rolesByUser) {
                $userId = $assignment->model_id;

                if (($rolesByUser[$userId] ?? null) === 'admin') {
                    return;
                }

                $rolesByUser[$userId] = $assignment->name;
            });

        foreach ($rolesByUser as $userId => $role) {
            DB::table('users')
                ->where('id', $userId)
                ->update(['role' => $role]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data sync only; no schema changes to roll back.
    }
};
