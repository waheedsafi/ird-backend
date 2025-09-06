<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use Auditable;
    /** @use HasFactory<\Database\Factories\RoleFactory> */
    use HasFactory;
    protected $guarded = [];
}
