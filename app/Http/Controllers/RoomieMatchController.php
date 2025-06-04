<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RoomieMatchService;
use App\Models\RoomMatch;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoomieMatchController extends Controller
{
    private RoomieMatchService $service;

    public function __construct(RoomieMatchService $service)
    {
        $this->service = $service;
    }

    public function index(): View
    {
        if (!Auth::check()) {
            return view('roomie.landing');
        }

        $potential = $this->service->getPotentialMatches(Auth::id());
        return view('roomie.app', ['candidates' => $potential]);
    }

    public function like(int $userId): RedirectResponse
    {
        $result = $this->service->likeUser(Auth::id(), $userId);
        if ($result['is_mutual_match'] ?? false) {
            session()->flash('new_match', $result['other_user']->name);
        }
        return back();
    }

    public function sendMessage(Request $request, int $matchId): RedirectResponse
    {
        $request->validate(['content' => 'required|string']);
        Message::create([
            'match_id' => $matchId,
            'sender_id' => Auth::id(),
            'content' => $request->input('content'),
        ]);
        return back();
    }

    public function conversation(int $matchId): View
    {
        $match = RoomMatch::findOrFail($matchId);
        $messages = $match->messages()->with('sender')->orderBy('created_at')->get();
        return view('roomie.conversation', compact('match', 'messages'));
    }
}
