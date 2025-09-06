<?php

namespace App\Models;

use App\Traits\Auditable;
use Sway\Traits\InvalidatableToken;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Organization extends Authenticatable
{
    use HasFactory, Notifiable, InvalidatableToken, Auditable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
    public function email()
    {
        return $this->belongsTo(Email::class, 'email_id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function organizationTrans()
    {
        return $this->hasMany(OrganizationTran::class);
    }

    public function organizationType()
    {
        return $this->belongsTo(OrganizationType::class, 'organization_type_id');
    }

    public function organizationStatus()
    {
        return $this->hasOne(OrganizationStatus::class, 'organization_id', 'id');
    }

    public function agreement()
    {
        return $this->hasOne(Agreement::class, 'organization_id');
    }
}
