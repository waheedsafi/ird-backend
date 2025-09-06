<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoleTrans extends Model
{
    use Auditable;

    /** @use HasFactory<\Database\Factories\RoleTransFactory> */
    use HasFactory;
    protected $guarded = [];
}
