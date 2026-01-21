<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = [
        'email',
        'role',
        'company_id',
        'company_name',
        'token',
        'accepted_at',
    ];

    /**
     * Get the company associated with the invitation.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
