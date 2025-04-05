<?php

namespace App\Modules\Business\Resources;

use App\Modules\Business\Models\BusinessKanban;
use App\Modules\Business\Models\BusinessKanbanColumns;
use App\Modules\Products\Models\Products;
use App\Modules\UniClients\Models\UniClients;
use App\Modules\Users\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessHistoricResource extends JsonResource
{
    public function toArray($request)
    {
        $newValuesKeys = array_keys($this->new_values);
        $oldValuesKeys = array_keys($this->old_values);

        $newValues = [];
        $oldValues = [];

        if (!empty($newValuesKeys)) {
            foreach ($newValuesKeys as $key => $value) {
                if ($value === 'business_kanban_id') {
                    $newValue = BusinessKanban::find($this->new_values[$value]);
                    $newValues[$value] = !empty($newValue) ? $newValue->type : null;
                } else if ($value === 'stage') {
                    $newValue = BusinessKanbanColumns::find($this->new_values[$value]);
                    $newValues[$value] = !empty($newValue) ? $newValue->name : null;
                } else if ($value === 'coach_id' || $value === 'referrer_id' || $value === 'closer_id') {
                    $newValue = User::find($this->new_values[$value]);
                    $newValues[$value] = !empty($newValue) ? $newValue->name : null;
                } else if ($value === 'product_id') {
                    $newValue = Products::find($this->new_values[$value]);
                    $newValues[$value] = !empty($newValue) ? $newValue->name : null;
                } else if ($value === 'client_id') {
                    $newValue = UniClients::find($this->new_values[$value]);
                    $newValues[$value] = !empty($newValue) ? $newValue->name : null;
                } else {
                    $newValues[$value] = $this->new_values[$value];
                }
            }
        }
        if (!empty($oldValuesKeys)) {
            foreach ($oldValuesKeys as $key => $value) {
                if ($value === 'business_kanban_id') {
                    $oldValue = BusinessKanban::find($this->old_values[$value]);
                    $oldValues[$value] = !empty($oldValue) ? $oldValue->type : null;
                } else if ($value === 'stage') {
                    $oldValue = BusinessKanbanColumns::find($this->old_values[$value]);
                    $oldValues[$value] = !empty($oldValue) ? $oldValue->name : null;
                } else if ($value === 'coach_id' || $value === 'referrer_id' || $value === 'closer_id') {
                    $oldValue = User::find($this->old_values[$value]);
                    $oldValues[$value] = !empty($oldValue) ? $oldValue->name : null;
                } else if ($value === 'product_id') {
                    $oldValue = Products::find($this->old_values[$value]);
                    $oldValues[$value] = !empty($oldValue) ? $oldValue->name : null;

                } else if ($value === 'client_id') {
                    $oldValue = UniClients::find($this->old_values[$value]);
                    $oldValues[$value] = !empty($oldValue) ? $oldValue->name : null;
                    
                } else {
                    $oldValues[$value] = $this->old_values[$value];
                }
            }
        }

        

        return [
            'id' => $this->id,
            'event' => $this->event,
            'user_type' => $this->user_type,
            'user_id' => $this->user_id,
            'old_values' => !empty($oldValues) ? $oldValues : $this->old_values,
            'new_values' => !empty($newValues) ? $newValues : $this->new_values,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'avatar' => $this->user->avatar
            ] : null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }
}
