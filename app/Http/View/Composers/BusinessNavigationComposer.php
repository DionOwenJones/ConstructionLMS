<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\BusinessCoursePurchase;
use Illuminate\Support\Facades\Auth;
class BusinessNavigationComposer
{
    public function compose(View $view)
    {
        $courseCount = 0;

        if (Auth::check() && Auth::user()->business) {
            $courseCount = BusinessCoursePurchase::where('business_id', Auth::user()->business->id)
                ->distinct('course_id')
                ->count();
        }

        $view->with('courseCount', $courseCount);
    }
}
