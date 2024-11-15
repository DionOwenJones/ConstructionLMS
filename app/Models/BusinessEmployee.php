<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessEmployee extends Model
{
    protected $fillable = [
        'business_id',
        'user_id',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courseAllocations()
    {
        return $this->hasMany(BusinessCourseAllocation::class);
    }
}
