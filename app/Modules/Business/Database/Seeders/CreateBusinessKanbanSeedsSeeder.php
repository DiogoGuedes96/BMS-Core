<?php

namespace App\Modules\Business\Database\Seeders;

use App\Modules\Business\Enums\KanbanTypesEnum;
use App\Modules\Business\Models\BusinessKanban;
use App\Modules\Business\Models\BusinessKanbanColumns;
use Illuminate\Database\Seeder;

class CreateBusinessKanbanSeedsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!BusinessKanban::where('type', KanbanTypesEnum::COACHING_BUSINESS_CLUB)->exists()) {
            $businessKanban = BusinessKanban::create([
                'type' => KanbanTypesEnum::COACHING_BUSINESS_CLUB,
            ]);

            BusinessKanbanColumns::create([
                'name' => 'Primeiro contacto',
                'color' => '#FFF1F0',
                'index' => 1,
                'business_kanban_id' => $businessKanban->id,
                'is_first' => true,
            ]);

            BusinessKanbanColumns::create([
                'name' => 'Aguardar follow-up',
                'color' => '#FCFFE6',
                'index' => 2,
                'business_kanban_id' => $businessKanban->id,
            ]);

            BusinessKanbanColumns::create([
                'name' => 'Aguardar pagamento',
                'color' => '#F0F5FF',
                'index' => 3,
                'business_kanban_id' => $businessKanban->id,
            ]);

            BusinessKanbanColumns::create([
                'name' => 'Negócio fechado',
                'color' => '#F4FFB8',
                'index' => 4,
                'business_kanban_id' => $businessKanban->id,
                'is_last' => true,
            ]);
        }

        if (!BusinessKanban::where('type', KanbanTypesEnum::DIAGNOSIS)->exists()) {
            $businessKanbanDiagnostico = BusinessKanban::create([
                'type' => KanbanTypesEnum::DIAGNOSIS,
            ]);

            BusinessKanbanColumns::create([
                'name' => 'Aguarda contacto',
                'color' => '#D1F6F1',
                'index' => 1,
                'business_kanban_id' => $businessKanbanDiagnostico->id,
                'is_first' => true,
            ]);

            BusinessKanbanColumns::create([
                'name' => 'À espera de marcação de diagnóstico',
                'color' => '#FFC069',
                'index' => 2,
                'business_kanban_id' => $businessKanbanDiagnostico->id,

            ]);

            BusinessKanbanColumns::create([
                'name' => 'Diagnóstico marcado',
                'color' => '#D3ADF7',
                'index' => 3,
                'business_kanban_id' => $businessKanbanDiagnostico->id,
            ]);

            BusinessKanbanColumns::create([
                'name' => 'Diagnóstico feito',
                'color' => '#FFADD2',
                'index' => 4,
                'business_kanban_id' => $businessKanbanDiagnostico->id,
            ]);

            BusinessKanbanColumns::create([
                'name' => 'Proposta feita',
                'color' => '#D6E4FF',
                'index' => 5,
                'business_kanban_id' => $businessKanbanDiagnostico->id,
            ]);

            BusinessKanbanColumns::create([
                'name' => 'Negócio fechado',
                'color' => '#D9F7BE',
                'index' => 6,
                'business_kanban_id' => $businessKanbanDiagnostico->id,
                'is_last' => true,
            ]);
        }
    }
}
