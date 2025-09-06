<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationTran extends Model
{
    protected $guarded = [];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }
}
