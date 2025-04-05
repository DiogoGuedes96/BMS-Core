<?php

namespace App\Modules\Users\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Modules\Users\Enums\PermissionTypesEnum;
use App\Modules\Users\Models\ModulePermission;

class ModulePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->runModulePermissions();
    }

    public function runModulePermissions()
    {
        if (env('BMS_CLIENT') == 'ATRAVEL') {
            $modulePermissions = $this->atravelModulePermissions();
        } else if (env('BMS_CLIENT') == 'ASM') {
            $modulePermissions = $this->asmModulePermissions();
        } else if (env('BMS_CLIENT') == 'UNI') {
            $modulePermissions = $this->uniModulePermissions();
        } else {
            return;
        }

        foreach ($modulePermissions as $module => $data) {
            $label = $data['label'];

            $permissions = array_map(function($value, $label) use ($module) {
                return [
                    'label' => $label,
                    'value' => $value,
                    'permissions' => $this->getPermissions(env('BMS_CLIENT'), $module)
                ];
            }, array_keys($data['permissions']), array_values($data['permissions']));

            if (ModulePermission::where('module', '=', $module)->exists()) {
                ModulePermission::where('module', '=', $module)->update(compact('permissions'));
            } else {
                ModulePermission::create(compact('module', 'label', 'permissions'));
            }
        }
    }

    private function getPermissions($bmsClient, $module) {
        if ($bmsClient == 'UNI') {
            return $module == 'manager' ? PermissionTypesEnum::getAll() : PermissionTypesEnum::getSimplified();
        }

        return PermissionTypesEnum::getAll();
    }

    private function uniModulePermissions() {
        return [
            'dashboard' => [
                'label' => 'Dashboard',
                'permissions' => [
                    'dashboard_all' => 'Todas as informações',
                    'dashboard_personal' => 'Somente informações pessoais'
                ]
            ],
            'clients' => [
                'label' => 'Clientes',
                'permissions' => [
                    'new_client' => 'Criar novo',
                    'view_client' => 'Visualizar clientes',
                    'edit_client' => 'Edição de dados do cliente',
                    'delete_client' => 'Apagar dados do cliente',
                ]
            ],
            'business' => [
                'label' => 'Negócios',
                'permissions' => [
                    'new_business' => 'Criar novo',
                    'view_business' => 'Visualizar negócios',
                    'edit_business' => 'Editar dados do negócio',
                    'delete_business' => 'Apagar dados do negócio',
                    'new_follow_up' => 'Criar follow up',
                ]
            ],
            'manager' => [
                'label' => 'Gestão',
                'permissions' => [
                    'profiles' => 'Perfil',
                    'users' => 'Utilizadores',
                    'payments' => 'Pagamentos',
                    'administrative' => 'Administrativa',
                ]
            ],
            'products' => [
                'label' => 'Produtos',
                'permissions' => [
                    'new_products' => 'Criar novo',
                    'view_products' => 'Visualizar produtos',
                    'edit_products' => 'Edição de dados do produto',
                    'delete_products' => 'Apagar dados do produto'
                ]
            ],
        ];
    }
    private function asmModulePermissions() {
        return [
            'dashboard' => [
                'label' => 'Dashboard',
                'permissions' => [
                    'dashboard' => 'Dashboard'
                ]
            ],
            'asm_schedule' => [
                'label' => 'Agendamento',
                'permissions' => [
                    'asm_schedule' => 'Agendamento',
                    'asm_schedule_feedback' => 'Elogio/Reclamação',
                    'asm_schedule_canceled' => 'Canceladas',
                ]
            ],
            'clients' => [
                'label' => 'Clientes',
                'permissions' => [
                    'clients' => 'Clientes'
                ]
            ],
            'patients' => [
                'label' => 'Utentes',
                'permissions' => [
                    'patients' => 'Utentes'
                ]
            ],
            'users' => [
                'label' => 'Usuários',
                'permissions' => [
                    'users' => 'Usuários'
                ]
            ],
            'calls' => [
                'label' => 'Central Telefónica',
                'permissions' => [
                    'calls' => 'Central Telefónica'
                ]
            ],
        ];
    }

    private function atravelModulePermissions()
    {
        return [
            'dashboard' => [
                'label' => 'Dashboard',
                'permissions' => [
                    'dashboard' => 'Dashboard'
                ]
            ],
            'bookings' => [
                'label' => 'Reservas',
                'permissions' => [
                    'bookings' => 'Consulta Reservas',
                    'services' => 'Consulta Serviços',
                    'operators' => 'Operadores',
                    'bookingsToApprove' => 'Reservas a Aprovar',
                    'clients' => 'Clientes'
                ]
            ],
            'timetable' => [
                'label' => 'Escalas',
                'permissions' => [
                    'timetable' => 'Escalas'
                ]
            ],
            'control' => [
                'label' => 'Controle',
                'permissions' => [
                    'staff' => 'Staff',
                    'operators' => 'Operadores',
                    'suppliers' => 'Fornecedores',
                    'reportsStaff' => 'Relatório Staff',
                    'reportsOperators' => 'Relatório Operadores',
                    'reportsSuppliers' => 'Relatório Fornecedores'
                ]
            ],
            'tables' => [
                'label' => 'Tabelas',
                'permissions' => [
                    'operators' => 'Tabela Operadores',
                    'suppliers' => 'Tabela Fornecedores',
                    'staff' => 'Tabela Staff'
                ]
            ],
            'operators' => [
                'label' => 'Operadores',
                'permissions' => [
                    'operators' => 'Operadores'
                ]
            ],
            'suppliers' => [
                'label' => 'Fornecedores',
                'permissions' => [
                    'suppliers' => 'Fornecedores'
                ]
            ],
            'staff' => [
                'label' => 'Staff',
                'permissions' => [
                    'staff' => 'Staff'
                ]
            ],
            'vehicles' => [
                'label' => 'Viaturas',
                'permissions' => [
                    'vehicles' => 'Viaturas'
                ]
            ],
            'services' => [
                'label' => 'Serviços',
                'permissions' => [
                    'serviceTypes' => 'Tipos de serviço',
                    'serviceStates' => 'Estados de serviço'
                ]
            ],
            'routes' => [
                'label' => 'Rotas',
                'permissions' => [
                    'zones' => 'Zonas',
                    'routes' => 'Rotas',
                    'locations' => 'Locais'
                ]
            ],
            'users' => [
                'label' => 'Utilizadores',
                'permissions' => [
                    'users' => 'Utilizadores',
                    'profilesAndPrivileges' => 'Perfis e privilégios'
                ]
            ],
            'companies' => [
                'label' => 'Empresa',
                'permissions' => [
                    'companies' => 'Empresa'
                ]
            ],
            'app' => [
                'label' => 'App',
                'permissions' => [
                    'app' => 'App'
                ]
            ],
            'recycling' => [
                'label' => 'Reciclagem',
                'permissions' => [
                    'recycling' => 'Reciclagem'
                ]
            ]
        ];
    }
}
