<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            // Eliminar la clave primaria actual
            $table->dropPrimary(['email']);
            
            // Agregar ID autoincremental
            $table->id()->first();
            
            // Modificar columna email para que no sea única
            $table->string('email')->nullable()->change();
            
            // Agregar user_id con foreign key
            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            
            // Modificar token para que sea de 64 caracteres y único
            $table->string('token', 64)->unique()->change();
            
            // Agregar nuevas columnas
            $table->string('type')->default('password_reset')->after('token');
            $table->timestamp('expires_at')->after('type');
            $table->timestamp('used_at')->nullable()->after('expires_at');
            $table->string('ip_address', 45)->nullable()->after('used_at');
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->timestamp('updated_at')->nullable()->after('user_agent');
            
            // Agregar índices
            $table->index(['token', 'expires_at']);
            $table->index(['user_id', 'type']);
        });
        
        // Eliminar columna email ya que ahora usamos user_id
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            // Eliminar índices
            $table->dropIndex(['token', 'expires_at']);
            $table->dropIndex(['user_id', 'type']);
            
            // Eliminar columnas nuevas
            $table->dropColumn([
                'user_agent',
                'ip_address',
                'used_at',
                'expires_at',
                'type',
                'updated_at'
            ]);
            
            // Eliminar foreign key y user_id
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            
            // Agregar email de vuelta
            $table->string('email')->first();
            
            // Eliminar id
            $table->dropColumn('id');
            
            // Restaurar primary key en email
            $table->primary('email');
            
            // Restaurar token a varchar(255)
            $table->string('token', 255)->change();
        });
    }
};
