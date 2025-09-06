<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class RoleAssignment extends Model
{
    use Auditable;
    protected $guarded = [];
}
