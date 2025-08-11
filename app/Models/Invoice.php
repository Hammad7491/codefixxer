<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'invoice_number',
        'client_name', 'contact_person', 'client_email', 'client_phone', 'client_address',
        'project_title', 'project_description',
        'discount_name', 'discount_type', 'discount_value', 'tax_percent',
        'bank_name', 'bank_account', 'account_holder', 'terms',
        'subtotal', 'discount_amount', 'tax_amount', 'total',
    ];

    protected $casts = [
        'discount_value'  => 'decimal:2',
        'tax_percent'     => 'decimal:2',
        'subtotal'        => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'total'           => 'decimal:2',
    ];

    protected $attributes = [
        'discount_type'   => 'percent',
        'discount_value'  => 0,
        'tax_percent'     => 0,   // always 0 in your app
        'subtotal'        => 0,
        'discount_amount' => 0,
        'tax_amount'      => 0,
        'total'           => 0,
    ];

    public function milestones()
    {
        return $this->hasMany(InvoiceMilestone::class);
    }

    /**
     * Recalculate totals.
     * NOTE: tax is forced to 0 per your requirement.
     */
    public function recalcTotals(): void
    {
        $subtotal = (float) $this->milestones()->sum('amount');

        // Discount (clamped 0..subtotal)
        $discount = 0.0;
        if ($this->discount_type === 'percent') {
            $discount = $subtotal * ((float) $this->discount_value / 100);
        } else {
            $discount = (float) $this->discount_value;
        }
        $discount = max(0.0, min($discount, $subtotal));

        // TAX DISABLED
        $taxAmount = 0.0;

        $total = max(0.0, $subtotal - $discount + $taxAmount);

        $this->subtotal        = round($subtotal, 2);
        $this->discount_amount = round($discount, 2);
        $this->tax_amount      = 0.00;
        $this->total           = round($total, 2);
    }
}
