<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ContractMilestone extends Model
{
    use BelongsToTenant;

    // optional (Eloquent will infer this correctly, but explicit is fine)
    protected $table = 'contract_milestones';

    protected $fillable = [
        'tenant_id',
        'contract_id',
        'name',
        'description',
        'due_date',
        'amount',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount'   => 'decimal:2',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
