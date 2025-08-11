<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'contract_number',
        'title', 'purpose',
        // client
        'client_name','client_email','client_phone','client_address',
        // dates
        'start_date','end_date',
        // section 2
        'project_timeline','payment_terms',
        // section 3
        'revisions','ownership_ip','confidentiality','client_responsibilities','termination_clause',
        // section 4
        'dispute_resolution','limitation_of_liability','amendments',
        // totals
        'total_cost',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'total_cost' => 'decimal:2',
    ];

    public function milestones()
    {
        return $this->hasMany(ContractMilestone::class);
    }

    public function recalcTotal(): void
    {
        $this->total_cost = round((float) $this->milestones()->sum('amount'), 2);
    }
}
