<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * ⚠️ PRODUÇÃO — bilhetes reais existem na base de dados
     * Os seeders destrutivos estão desactivados para proteger dados.
     */
    public function run(): void
    {
        // ❌ DESACTIVADOS — podem duplicar/destruir dados reais
        // $this->call(UserSeeder::class);
        // $this->call(EventSeeder::class);
        // $this->call(TicketSeeder::class);  // NUNCA — apaga bilhetes reais

        // ✅ SEGUROS — usam updateOrCreate
        $this->call(SiteSettingsSeeder::class);
        $this->call(TicketBatchSeeder::class);
    }
}
