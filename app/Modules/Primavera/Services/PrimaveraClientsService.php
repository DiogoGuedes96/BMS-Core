<?php

namespace App\Modules\Primavera\Services;

use App\Modules\Primavera\Models\PrimaveraClients;
use Throwable;

class PrimaveraClientsService
{
    private $primaveraAuth;

    public function __construct()
    {
        $this->primaveraAuth = new PrimaveraAuthService();
    }

    public function getAllClients()
    {
        $customers = $this->primaveraAuth->requestPrimaveraApi('GET', '/WebApi/ApiExtended/LstClientes');

        return $customers;
    }

    /**
     * It gets all the clients from the Primavera API, and then it updates the database with the new
     * information
     */
    public function updateOrCreateClients($command)
    {
        $customers = $this->getAllClients();

        foreach ($customers as $customer) {
            if ($customer->Cliente == '***1' || $customer->Cliente == '***9' || $customer->Cliente == 'VD') {
                continue;
            }
            if ($customer->Pais == 'PT'){ //To facilitate and simplify the program, only sanitize Portuguese numbers
                $phones = $this->sanitizePhone($customer);
            }

            try {
                PrimaveraClients::updateOrCreate(
                    ['primavera_id' => $customer->Cliente],
                    [
                        'name' => $customer->Nome ?? "",
                        'address' => $customer->Fac_Mor ?? "",
                        'postal_code' => $customer->Fac_Cp ?? "",
                        'postal_code_address' => $customer->Fac_Cploc ?? "",
                        'country' => $customer->Pais ?? "",
                        'tax_number' => $customer->NumContrib ?? "",
                        'phone_1' => $phones["phone_1"] ?? "",
                        'phone_2' => $phones["phone_2"] ?? "",
                        'phone_3' => $phones["phone_3"] ?? "",
                        'payment_method' => $customer->ModoPag ?? "",
                        'payment_condition' => $customer->CondPag ?? "",
                        'email' => $customer->EnderecoWeb ?? "",
                        'total_debt' => $customer->TotalDeb ?? "",
                        'age_debt' => $customer->IdadeSaldoCob >= 0
                            ? $customer->IdadeSaldoCob : 0,
                        'status' => $customer->Situacao ?? "",
                        'rec_mode' => $customer->ModoRec ?? "",
                        'fiscal_name' => $customer->NomeFiscal ?? "",
                        'notes' => $customer->Notas ?? "",
                        'zone' => $customer->Zona ?? "",
                        'zone_description' => $customer->ZonaDescricao ?? "",
                        'discount_1' => $customer->DescontoCli ?? 0,
                        'discount_2' => $customer->DescontoCli2 ?? 0,
                        'discount_3' => $customer->DescontoCli3 ?? 0,
                    ]
                );
                $command->info('Client Saved ' . $customer->Cliente);
            } catch (Throwable $th) {
                $command->error('Error to save Client ' . $customer->Cliente);
            }
        }
    }

    public function sanitizePhone($customer)
    {
        $phones = array();

        if(isset($customer->Fac_Tel)){
            $explode = explode("/", $customer->Fac_Tel);

            if(isset($explode[0])){
                $phones["phone_1"] = intval(str_replace(' ', '', $explode[0]));
            }

            if(isset($explode[1])){
                if(strlen(str_replace(' ', '', $explode[1])) >= 9){ //If the final string has 9 or more chars (Normal number does not have less than 9)
                    $phones["phone_3"] = intval(str_replace(' ', '', $explode[1]));
                }
            }
        }

        if(isset($customer->Telefone2)){
            $phones["phone_2"] =intval(str_replace(' ', '', $customer->Telefone2));
        }

        return $phones;
    }
}
