<?php

namespace Database\Seeders;

use App\Models\Courier;
use App\Models\EstatusAduanero;
use App\Models\Puerto;
use App\Models\Ubicacion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Catálogos iniciales
        foreach (['DHL', 'AWB', 'DTD', 'FEDEX', 'UPS', 'TNT', 'ROSM'] as $courier) {
            Courier::firstOrCreate(['nombre' => $courier], ['activo' => true]);
        }

        foreach (['Puerto de Algeciras', 'Puerto de Tarifa', 'Puerto de Gibraltar', 'Puerto de Ceuta', 'Puerto de Málaga', 'Puerto de Cádiz'] as $puerto) {
            Puerto::firstOrCreate(['nombre' => $puerto], ['activo' => true]);
        }

        foreach (['Almacén A', 'Almacén B', 'Almacén C', 'Muelle Norte', 'Muelle Sur', 'Terminal 1', 'Terminal 2'] as $ubicacion) {
            Ubicacion::firstOrCreate(['nombre' => $ubicacion], ['activo' => true]);
        }

        foreach (['Despachado', 'En trámite', 'Pendiente documentación', 'Retenido', 'Liberado', 'DUA presentado'] as $estatus) {
            EstatusAduanero::firstOrCreate(['nombre' => $estatus], ['activo' => true]);
        }

        User::updateOrCreate(
            ['email' => 'dani@hawkins.es'],
            [
                'name' => 'Dani Hawkins',
                'password' => Hash::make(env('SEED_PASS_DANI_H', 'Hawkins2025!')),
                'locale' => 'es',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'dani.mefle@hawkins.es'],
            [
                'name' => 'Dani Mefle',
                'password' => Hash::make(env('SEED_PASS_DANI_M', 'Euroship@2026')),
                'locale' => 'es',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'juancarlos@euroship.es'],
            [
                'name' => 'Juan Carlos Euroship',
                'password' => Hash::make(env('SEED_PASS_JC', 'Euroship2025!')),
                'locale' => 'es',
                'email_verified_at' => now(),
            ]
        );
    }
}
