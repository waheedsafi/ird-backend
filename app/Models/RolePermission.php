<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RolePermission extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\RolePermissionFactory> */
    use HasFactory;
    protected $guarded = [];
}
