<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\PermissionGrouper;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\View\View;
use OwenIt\Auditing\Models\Audit;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditController extends Controller
{
    /**
     * Overrides for models whose route or label don't follow the convention
     * admin.{plural}.show. Keep this list tiny and only for real exceptions.
     */
    private const MODEL_OVERRIDES = [
        // 'App\Models\IEP' => ['label' => 'IEPs', 'route' => 'admin.ieps.show'],
    ];

    public function index(Request $request): View
    {
        $audits = $this->buildQuery($request)
            ->with('user')
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $filters = [
            'user'       => $request->string('user')->toString(),
            'event'      => $request->string('event')->toString(),
            'model'      => $request->string('model')->toString(),
            'from'       => $request->string('from')->toString(),
            'to'         => $request->string('to')->toString(),
        ];

        $users = $this->distinctUsers();
        $events = ['created', 'updated', 'deleted', 'restored'];
        $models = $this->distinctModels();

        return view('admin.audit.index', compact('audits', 'filters', 'users', 'events', 'models'));
    }

    public function show(Audit $audit): View
    {
        $audit->load('user');

        $old = $audit->old_values ?? [];
        $new = $audit->new_values ?? [];

        // Which keys actually changed
        $changedKeys = collect(array_keys($new))
            ->merge(array_keys($old))
            ->unique()
            ->reject(fn ($k) => ($old[$k] ?? null) === ($new[$k] ?? null))
            ->values()
            ->all();

        $model = $this->humaniseModel($audit->auditable_type);
        $recordId = $audit->auditable_id;

        $recordUrl = $model['route'] && Route::has($model['route'])
            ? route($model['route'], $recordId)
            : null;

        // Humanise permission arrays when the auditable is a Role
        $oldPermissions = $this->maybePermissionSummary($audit->auditable_type, $old);
        $newPermissions = $this->maybePermissionSummary($audit->auditable_type, $new);

        return view('admin.audit.show', compact(
            'audit', 'old', 'new', 'changedKeys',
            'model', 'recordUrl',
            'oldPermissions', 'newPermissions'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $filename = 'audit-log-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($request) {
            $out = fopen('php://output', 'w');

            fputcsv($out, [
                'Timestamp', 'User', 'Event', 'Model', 'Record ID',
                'IP Address', 'URL', 'Changed Fields',
            ]);

            $this->buildQuery($request)
                ->with('user')
                ->latest()
                ->chunk(500, function (Collection $chunk) use ($out) {
                    foreach ($chunk as $audit) {
                        $changed = collect(array_keys($audit->new_values ?? []))
                            ->merge(array_keys($audit->old_values ?? []))
                            ->unique()
                            ->implode(', ');

                        fputcsv($out, [
                            $audit->created_at->toDateTimeString(),
                            $audit->user?->name ?? 'System',
                            $audit->event,
                            class_basename($audit->auditable_type),
                            $audit->auditable_id,
                            $audit->ip_address,
                            $audit->url,
                            $changed,
                        ]);
                    }
                });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /* -----------------------------------------------------------------
     | Helpers
     * ----------------------------------------------------------------- */

    private function buildQuery(Request $request)
    {
        return Audit::query()
            ->when($request->string('user')->toString(), function ($q, $userId) {
                $q->where('user_id', $userId);
            })
            ->when($request->string('event')->toString(), function ($q, $event) {
                $q->where('event', $event);
            })
            ->when($request->string('model')->toString(), function ($q, $model) {
                $q->where('auditable_type', $model);
            })
            ->when($request->string('from')->toString(), function ($q, $from) {
                $q->whereDate('created_at', '>=', $from);
            })
            ->when($request->string('to')->toString(), function ($q, $to) {
                $q->whereDate('created_at', '<=', $to);
            });
    }

    /**
     * @return array<int, array{id: int, name: string}>
     */
    private function distinctUsers(): array
    {
        return Audit::query()
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

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function distinctModels(): array
    {
        return Audit::query()
            ->distinct()
            ->pluck('auditable_type')
            ->map(fn ($fqcn) => [
                'value' => $fqcn,
                'label' => $this->humaniseModel($fqcn)['label'],
            ])
            ->sortBy('label')
            ->values()
            ->all();
    }

    /**
     * Derive a human label and route name from a fully-qualified class name.
     * Convention: App\Models\Child => route admin.children.show, label "Children".
     *
     * @return array{label: string, route: ?string}
     */
    private function humaniseModel(string $fqcn): array
    {
        if (isset(self::MODEL_OVERRIDES[$fqcn])) {
            return self::MODEL_OVERRIDES[$fqcn];
        }

        $short  = class_basename($fqcn);
        $plural = Str::plural(Str::lower($short));

        return [
            'label' => Str::pluralStudly($short),
            'route' => "admin.{$plural}.show",
        ];
    }

    /**
     * When a Role is audited, its `permissions` column holds a JSON array of
     * permission names. Rather than dumping that JSON, summarise it by module.
     */
    private function maybePermissionSummary(string $fqcn, array $values): ?array
    {
        if (! str_ends_with($fqcn, '\\Role')) {
            return null;
        }

        $permissions = $values['permissions'] ?? null;

        if (! is_array($permissions)) {
            return null;
        }

        return PermissionGrouper::summarise($permissions);
    }
}