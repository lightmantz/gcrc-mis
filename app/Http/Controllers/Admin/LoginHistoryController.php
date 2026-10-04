<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $entries = $this->buildQuery($request)
            ->with('user')
            ->latest('logged_in_at')
            ->paginate(30)
            ->withQueryString();

        $filters = [
            'user'   => $request->string('user')->toString(),
            'outcome' => $request->string('outcome')->toString(),
            'ip'     => $request->string('ip')->toString(),
            'from'   => $request->string('from')->toString(),
            'to'     => $request->string('to')->toString(),
        ];

        $users = $this->distinctUsers();

        return view('admin.login-history.index', compact('entries', 'filters', 'users'));
    }

    public function show(LoginHistory $loginHistory): View
    {
        $loginHistory->load('user');

        return view('admin.login-history.show', compact('loginHistory'));
    }

    public function purge(Request $request): RedirectResponse
    {
        $days = max(1, (int) $request->input('days', 90));

        $cutoff = now()->subDays($days);

        $count = LoginHistory::where('created_at', '<', $cutoff)->count();

        LoginHistory::where('created_at', '<', $cutoff)->delete();

        return redirect()
            ->route('admin.login-history.index')
            ->with('status', "Purged {$count} login records older than {$days} days.");
    }

    /* -----------------------------------------------------------------
     | Helpers
     * ----------------------------------------------------------------- */

    private function buildQuery(Request $request)
    {
        return LoginHistory::query()
            ->when($request->string('user')->toString(), function ($q, $userId) {
                $q->where('user_id', $userId);
            })
            ->when($request->string('outcome')->toString(), function ($q, $outcome) {
                $q->where('successful', $outcome === 'successful');
            })
            ->when($request->string('ip')->toString(), function ($q, $ip) {
                $q->where('ip_address', 'like', "%{$ip}%");
            })
            ->when($request->string('from')->toString(), function ($q, $from) {
                $q->whereDate('logged_in_at', '>=', $from);
            })
            ->when($request->string('to')->toString(), function ($q, $to) {
                $q->whereDate('logged_in_at', '<=', $to);
            });
    }

    private function distinctUsers(): array
    {
        return LoginHistory::query()
            ->whereNotNull('user_id')
            ->with('user:id,name')
            ->get()
            ->pluck('user')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->map(fn ($u) => ['id' => $u->id, 'name' => $u->name])
            ->values()
            ->all();
    }
}