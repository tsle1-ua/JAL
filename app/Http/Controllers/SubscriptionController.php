<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function create(Listing $listing): View
    {
        return view('subscriptions.create', compact('listing'));
    }

    public function store(Request $request, Listing $listing): RedirectResponse
    {
        $validated = $request->validate([
            'monthly_amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', 'max:255'],
        ]);

        Subscription::create([
            'user_id' => $request->user()->id,
            'listing_id' => $listing->id,
            'monthly_amount' => $validated['monthly_amount'],
            'start_date' => now()->toDateString(),
            'next_payment_date' => now()->addMonth()->toDateString(),
            'payment_method' => $validated['payment_method'],
        ]);

        return redirect()->route('listings.show', $listing)->with('success', 'Suscripción creada correctamente.');
    }
}
