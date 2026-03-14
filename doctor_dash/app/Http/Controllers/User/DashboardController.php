<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (! $request->session()->has('welcomed_user')) {
            $request->session()->put('welcomed_user', true);
            $request->session()->flash('welcome', 'Welcome back, ' . $user->name . '!');
        }

        $reportsCount = $user->reports()->count();
        $reviewedCasesCount = $user->reports()->where('status', '!=', 'Pending')->count();
        $pendingCasesCount = $user->reports()->where('status', 'Pending')->count();

        $recentCases = $user->reports()->with('updatedBy')->latest()->limit(5)->get();

        $doctors = User::whereIn('role', ['admin', 'assistant'])
            ->orderBy('name')
            ->get();

        $doctorNotifications = $doctors->map(function (User $doctor) use ($user) {
            $conversation = Conversation::where('type', 'admin_user')
                ->where(function ($query) use ($user, $doctor) {
                    $query->where([
                        'admin_id' => $user->id,
                        'participant_id' => $doctor->id,
                    ])->orWhere(function ($query) use ($user, $doctor) {
                        $query->where([
                            'admin_id' => $doctor->id,
                            'participant_id' => $user->id,
                        ]);
                    });
                })
                ->with(['messages' => function ($query) {
                    $query->latest()->limit(1)->with('sender');
                }])
                ->first();

            $last = $conversation && $conversation->messages ? $conversation->messages->first() : null;
            $doctor->last_message = $last ? $last->body : null;
            $doctor->last_message_at = $last ? $last->created_at : null;
            $doctor->last_message_from_self = $last ? ($last->sender_id === $user->id) : false;

            return $doctor;
        })->filter(function (User $doctor) {
            return $doctor->last_message && empty($doctor->last_message_from_self) === true;
        })->values();

        $unreadMessagesCount = $doctorNotifications->count();

        return view('user.dashboard', [
            'user' => $user,
            'reportsCount' => $reportsCount,
            'reviewedCasesCount' => $reviewedCasesCount,
            'pendingCasesCount' => $pendingCasesCount,
            'recentCases' => $recentCases,
            'unreadMessagesCount' => $unreadMessagesCount,
            'doctorNotifications' => $doctorNotifications,
        ]);
    }
}
