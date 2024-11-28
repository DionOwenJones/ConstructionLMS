<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminDiscountController extends Controller
{
    public function index()
    {
        $discounts = DiscountCode::orderBy('created_at', 'desc')->get();
        return view('admin.discounts.index', compact('discounts'));
    }

    public function create()
    {
        return view('admin.discounts.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:discount_codes,code|max:50',
            'description' => 'nullable|string|max:255',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'valid_from' => 'required|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        DiscountCode::create($request->all());

        return redirect()
            ->route('admin.discounts.index')
            ->with('success', 'Discount code created successfully.');
    }

    public function edit(DiscountCode $discount)
    {
        return view('admin.discounts.edit', compact('discount'));
    }

    public function update(Request $request, DiscountCode $discount)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:discount_codes,code,' . $discount->id . '|max:50',
            'description' => 'nullable|string|max:255',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'valid_from' => 'required|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $discount->update($request->all());

        return redirect()
            ->route('admin.discounts.index')
            ->with('success', 'Discount code updated successfully.');
    }

    public function destroy(DiscountCode $discount)
    {
        $discount->delete();

        return redirect()
            ->route('admin.discounts.index')
            ->with('success', 'Discount code deleted successfully.');
    }

    /**
     * Toggle the active status of a discount code.
     *
     * @param DiscountCode $discount
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(DiscountCode $discount)
    {
        $discount->update([
            'is_active' => !$discount->is_active
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Discount code status updated successfully.',
            'is_active' => $discount->is_active
        ]);
    }
}
