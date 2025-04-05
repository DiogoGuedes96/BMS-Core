<?php

namespace App\Modules\Clients\Services;

use App\Modules\Clients\Models\ClientResponsible;
use Exception;

class ClientResponsibleService
{
    private $clientResponsible;

    public function __construct()
    {
        $this->clientResponsible = new ClientResponsible();
    }

    public function createOrUpdateClientResponsible($responsible){
        try {

            $phone = $responsible['phone'];
            $name = $responsible['name'];

            $existingResponsible = $this->clientResponsible->where('phone', $phone)->first();

            if ($existingResponsible) {
                $existingResponsible->update(['name' => $name]);
                return $existingResponsible = $this->clientResponsible->where('phone', $phone)->first();
            } else {
               return $this->storeClientResponsible($name, $phone);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function storeClientResponsible($name, $phone) {
        try {
            $newClientResponsible = $this->clientResponsible->create(['name' => $name, 'phone' => $phone]);
            return $newClientResponsible;
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    public function getClientResponsibleByPhoneNumber($number, $name = null) {
        try {
            $result = null;
        
            $query = $this->clientResponsible->where('phone', $number);
    
            if ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            }
    
            $responsible = $query->first();

            return $responsible;
        } catch (Exception $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], $e->getCode());
        }
    }

    function getClientsFromResponsible($responsibleId){
        $responsible = $this->clientResponsible->findOrFail($responsibleId);
        $clients = $responsible->clients;
        return $clients;
    }
}
