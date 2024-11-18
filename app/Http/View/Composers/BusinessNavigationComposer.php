<?php

namespace App\Http\View\Composers;

use App\Models\BusinessCoursePurchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BusinessNavigationComposer
{
    public function compose(View $view)
    {
        $courseCount = 0;
        $employeeCount = 0;
        $hasBusiness = false;

        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isBusiness() && $user->business) {
                $hasBusiness = true;
                $courseCount = BusinessCoursePurchase::where('business_id', $user->business->id)
                    ->distinct('course_id')
                    ->count();
                $employeeCount = $user->business->employees()->count();
            }
        }

        $view->with([
            'courseCount' => $courseCount,
            'employeeCount' => $employeeCount,
            'hasBusiness' => $hasBusiness
        ]);
    }
}
