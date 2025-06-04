<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RoomieMatchService;

use App\Models\Message;
use App\Events\MessageSent;
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

    public function like(Request $request, int $userId)
    {
        $result = $this->service->likeUser(Auth::id(), $userId);

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        if ($result['is_mutual_match'] ?? false) {
            session()->flash('new_match', $result['other_user']->name);
        }

        return back();
    }

    public function dislike(Request $request, int $userId)
    {
        $result = $this->service->dislikeUser(Auth::id(), $userId);

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return back();
    }

    public function sendMessage(Request $request, int $matchId)
    {
        $validated = $request->validate(['content' => 'required|string']);

        $message = Message::create([
            'match_id' => $matchId,
            'sender_id' => Auth::id(),
            'content' => $validated['content'],
        ]);

        broadcast(new MessageSent($message))->toOthers();

        if ($request->wantsJson()) {
            return response()->json($message->load('sender'));
        }

        return back();
    }

    public function conversation(int $matchId): View
    {
        $match = RoomMatch::findOrFail($matchId);
        $messages = $match->messages()->with('sender')->orderBy('created_at')->get();
        return view('roomie.conversation', compact('match', 'messages'));
    }
}
