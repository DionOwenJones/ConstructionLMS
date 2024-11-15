<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    protected $fillable = [
        'name',
        // other fillable fields...
    ];

    // Relationship to employees
    public function employees()
    {
        return $this->hasMany(BusinessEmployee::class);
    }

    // Relationship to course purchases
    public function coursePurchases()
    {
        return $this->hasMany(BusinessCoursePurchase::class);
    }

    // Relationship to course allocations through employees
    public function courseAllocations()
    {
        return $this->hasManyThrough(
            BusinessCourseAllocation::class,
            BusinessEmployee::class,
            'business_id', // Foreign key on business_employees table
            'business_employee_id', // Foreign key on business_course_allocations table
            'id', // Local key on businesses table
            'id' // Local key on business_employees table
        )
        ->join('business_course_purchases', 'business_course_allocations.business_course_purchase_id', '=', 'business_course_purchases.id')
        ->select('business_course_allocations.*', 'business_course_purchases.course_id');
    }

    public function countDistinctCourses()
    {
        return BusinessCourseAllocation::join('business_employees', 'business_employees.id', '=', 'business_course_allocations.business_employee_id')
            ->join('business_course_purchases', 'business_course_purchases.id', '=', 'business_course_allocations.business_course_purchase_id')
            ->where('business_employees.business_id', $this->id)
            ->distinct('business_course_purchases.course_id')
            ->count('business_course_purchases.course_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    // Helper method to get all purchased courses
    public function purchasedCourses()
    {
        return $this->hasManyThrough(
            Course::class,
            BusinessCoursePurchase::class,
            'business_id', // Foreign key on business_course_purchases
            'id', // Local key on courses
            'id', // Local key on businesses
            'course_id' // Foreign key on business_course_purchases
        );
    }
}
