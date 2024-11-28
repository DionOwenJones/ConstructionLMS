<?php

namespace App\Http\Controllers;

use App\Models\DiscountCode;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function validateCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:discount_codes,code'
        ]);

        $discountCode = DiscountCode::where('code', $request->code)->first();

        if (!$discountCode || !$discountCode->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => 'This discount code is invalid or has expired.'
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'discount_percentage' => $discountCode->discount_percentage,
            'message' => 'Valid discount code! ' . $discountCode->discount_percentage . '% off will be applied.'
        ]);
    }

    public function applyDiscount(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:discount_codes,code'
        ]);

        $discountCode = DiscountCode::where('code', $request->code)->first();

        if (!$discountCode || !$discountCode->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'This discount code is invalid or has expired.'
            ], 422);
        }

        // Increment usage count
        $discountCode->incrementUsage();

        return response()->json([
            'success' => true,
            'discount_percentage' => $discountCode->discount_percentage,
            'message' => 'Discount applied successfully!'
        ]);
    }
}
