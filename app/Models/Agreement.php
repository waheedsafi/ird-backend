<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    use Auditable;
    protected $guarded = [];
}
