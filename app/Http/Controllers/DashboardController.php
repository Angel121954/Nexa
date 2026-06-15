<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserMatch;
use App\Models\Message;
use App\Models\Like;
use App\Models\Story;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $now = now();
        $todayStart = $now->copy()->startOfDay();

        $activeUsers = User::where('last_activity_at', '>=', $now->copy()->subDay())->count();
        $matchesToday = UserMatch::where('created_at', '>=', $todayStart)->count();
        $messagesToday = Message::where('created_at', '>=', $todayStart)->count();

        $churnsMonth = User::where('created_at', '<', $now->copy()->subMonth())
            ->where('last_activity_at', '<', $now->copy()->subMonth())
            ->count();

        $daysShort = ['L','M','M','J','V','S','D'];
        $registrationsWeek = [];
        $registrationsLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $registrationsLabels[] = $daysShort[$date->dayOfWeekIso % 7];
            $registrationsWeek[] = User::whereDate('created_at', $date->toDateString())->count();
        }

        $totalUsers = User::count();
        $premiumCount = User::where('role', 'admin')->count();
        $premiumPct = $totalUsers > 0 ? round($premiumCount / $totalUsers * 100) : 0;
        $freePct = 100 - $premiumPct;
        $inactivePct = 0;

        $recentUsers = User::latest()->take(5)->get();
        $unreadNotifications = auth()->user()->unreadNotifications()->count();

        return view('dashboard.index', [
            'activeUsers'          => $activeUsers,
            'matchesToday'         => $matchesToday,
            'messagesToday'        => $messagesToday,
            'churnsMonth'          => $churnsMonth,
            'registrationsWeek'    => $registrationsWeek,
            'registrationsLabels'  => $registrationsLabels,
            'premiumPct'           => $premiumPct,
            'freePct'              => $freePct,
            'inactivePct'          => $inactivePct,
            'recentUsers'          => $recentUsers,
            'unreadNotifications'  => $unreadNotifications,
        ]);
    }

    public function users(): View
    {
        $allUsers = User::where('id', '!=', auth()->id())->latest()->paginate(50);
        $unreadNotifications = auth()->user()->unreadNotifications()->count();

        return view('dashboard.users', [
            'allUsers'            => $allUsers,
            'unreadNotifications' => $unreadNotifications,
        ]);
    }

    public function activity(): View
    {
        $now = now();
        $todayStart = $now->copy()->startOfDay();

        $likesToday = Like::where('created_at', '>=', $todayStart)->count();
        $storiesToday = Story::where('created_at', '>=', $todayStart)->count();
        $onlineNow = User::where('last_activity_at', '>=', $now->copy()->subMinutes(5))->count();
        $recentActivity = $this->buildRecentActivity();
        $unreadNotifications = auth()->user()->unreadNotifications()->count();

        return view('dashboard.activity', [
            'likesToday'          => $likesToday,
            'storiesToday'        => $storiesToday,
            'onlineNow'           => $onlineNow,
            'recentActivity'      => $recentActivity,
            'unreadNotifications' => $unreadNotifications,
        ]);
    }

    public function toggleBlock(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes bloquearte a ti mismo.');
        }

        $blocked = $user->toggleBlock();

        return back()->with('success', $blocked
            ? "{$user->name} fue bloqueado."
            : "{$user->name} fue desbloqueado.");
    }

    public function toggleAdmin(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes cambiar tu propio rol.');
        }

        $isAdmin = $user->toggleAdmin();

        return back()->with('success', $isAdmin
            ? "{$user->name} ahora es administrador."
            : "{$user->name} ya no es administrador.");
    }

    private function buildRecentActivity(): array
    {
        $activity = [];

        $recentMatches = UserMatch::latest()->take(3)->get();
        foreach ($recentMatches as $match) {
            $u1 = $match->user1;
            $u2 = $match->user2;
            if ($u1 && $u2) {
                $activity[] = [
                    'type' => 'match',
                    'text' => "{$u1->name} y {$u2->name} hicieron match",
                    'time' => $match->created_at->diffForHumans(),
                ];
            }
        }

        $newUsers = User::latest()->take(3)->get();
        foreach ($newUsers as $user) {
            $activity[] = [
                'type' => 'register',
                'text' => "{$user->name} se registró en Nexa",
                'time' => $user->created_at->diffForHumans(),
            ];
        }

        usort($activity, fn($a, $b) => strtotime($b['time']) - strtotime($a['time']));

        return array_slice($activity, 0, 5);
    }
}
