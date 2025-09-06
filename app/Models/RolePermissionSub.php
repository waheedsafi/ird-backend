<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RolePermissionSub extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\RolePermissionSubFactory> */
    use HasFactory;
    protected $guarded = [];
}
