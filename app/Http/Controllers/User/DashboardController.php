<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard.
     */
    public function index()
    {
        $user = auth('customer')->user();

        $ordersCount    = $user->orders()->count();
        $pendingCount   = $user->orders()->where('status', 'pending')->count();
        $deliveredCount = $user->orders()->where('status', 'delivered')->count();
        $totalSpent     = (float) $user->orders()->where('payment_status', 'paid')->sum('total_amount');

        $recentOrders = $user->orders()
            ->with('items')
            ->latest()
            ->take(5)
            ->get();

        return view('front.user.dashboard', compact(
            'user', 'ordersCount', 'pendingCount', 'deliveredCount', 'totalSpent', 'recentOrders'
        ));
    }

    /**
     * Display user orders with filters.
     */
    public function orders()
    {
        $user = auth('customer')->user();

        $query = $user->orders()->with('items')->latest();
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(10)->appends(request()->query());

        return view('front.user.orders', compact('user', 'orders'));
    }

    public function showOrder($id)
    {
        $user = auth('customer')->user();

        $order = $user->orders()
            ->with([
                'items.product.media',
                'items.variation.attributeValues.attribute',
                'items.image',
                'histories',
            ])
            ->findOrFail($id);

        return view('front.user.order-show', compact('user', 'order'));
    }

    /**
     * Display profile settings.
     */
    public function profile()
    {
        $user = auth('customer')->user();
        return view('front.user.profile', compact('user'));
    }

    /**
     * Update the customer's name and email.
     */
    public function updateProfile(Request $request)
    {
        $user = auth('customer')->user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('customer_users', 'email')->ignore($user->id)],
        ]);

        if ($validated['email'] !== $user->email) {
            $validated['email_verified_at'] = null;
        }

        $user->update($validated);

        return back()->with('status', 'Profile updated successfully.');
    }

    /**
     * Update the customer's password.
     */
    public function updatePassword(Request $request)
    {
        $user = auth('customer')->user();

        $request->validate([
            'current_password' => ['required', 'current_password:customer'],
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'Password updated successfully.');
    }
}
