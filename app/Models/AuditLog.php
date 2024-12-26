<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['import_id', 'auditable_id', 'auditable_type', 'row', 'column', 'value'];

    /**
     * Get the owning model (Order, Product, etc.).
     */
    public function auditable()
    {
        return $this->morphTo();
    }
}
