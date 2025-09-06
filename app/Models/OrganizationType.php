<?php

namespace App\Models;

use App\Models\OrganizationTypeTrans;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrganizationType extends Model
{
    use HasFactory;
    protected $guarded = [];

    // In OrganizationType model
    public function organizationTypeTrans()
    {
        return $this->hasMany(OrganizationTypeTrans::class, 'organization_type_id'); // Adjust this according to your schema
    }
}
