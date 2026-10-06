@extends('gentelella::page')

@section('title', 'Treatment Plans')
@section('page_key', 'treatment-plans')

@section('content')
    <x-gentelella::page-header title="Treatment Plans" pretitle="Clinical">
        <x-slot:actions>
            @can('treatment_plans.create')
                <a href="{{ route('admin.treatment-plans.create') }}" class="btn btn-primary">
                    New Treatment Plan
                </a>
            @endcan
        </x-slot:actions>
    </x-gentelella::page-header>

    <x-gentelella::card flush>
        <div class="card-body" style="border-bottom: 1px solid var(--border-color-light);">
            <form method="GET" class="form-row cols-3" style="align-items: end;">
                <div class="form-group">
                    <label class="form-label" for="discipline">Discipline</label>
                    <select id="discipline" name="discipline" class="form-control">
                        <option value="">All disciplines</option>
                        @foreach (['physiotherapy' => 'Physiotherapy', 'occupational_therapy' => 'Occupational Therapy', 'speech' => 'Speech & Language', 'psychology' => 'Psychology', 'social_work' => 'Social Work', 'education' => 'Education', 'multi_disciplinary' => 'Multi-Disciplinary'] as $v => $l)
                            <option value="{{ $v }}" @selected(request('discipline') === $v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="">All</option>
                        @foreach (['draft' => 'Draft', 'active' => 'Active', 'under_review' => 'Under Review', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $v => $l)
                            <option value="{{ $v }}" @selected(request('status') === $v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="overdue">Review status</label>
                    <select id="overdue" name="overdue" class="form-control">
                        <option value="">All plans</option>
                        <option value="1" @selected(request('overdue') == '1')>Overdue for review</option>
                    </select>
                </div>

                <div class="form-actions" style="margin: 0; padding: 0; border: none;">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.treatment-plans.index') }}" class="btn btn-outline">Reset</a>
                </div>
            </form>
        </div>

        <x-gentelella::table>
            <thead>
                <tr>
                    <th>Child</th>
                    <th>Discipline</th>
                    <th>Lead</th>
                    <th>Progress</th>
                    <th>Next Review</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($plans as $plan)
                    <tr>
                        <td>
                            <a href="{{ route('admin.children.show', $plan->child) }}">
                                {{ $plan->child->full_name }}
                            </a>
                            <div style="font-size: 11px; color: var(--text-muted);">
                                {{ $plan->child->child_number }}
                            </div>
                        </td>
                        <td class="cell-strong">
                            <a href="{{ route('admin.treatment-plans.show', $plan) }}">
                                {{ $plan->discipline_label }}
                            </a>
                        </td>
                        <td>{{ $plan->leadStaff->full_name ?? '—' }}</td>
                        <td>
                            @php $progress = $plan->overall_progress; @endphp
                            @if ($progress === null)
                                <span style="color: var(--text-muted); font-size: 12px;">No goals</span>
                            @else
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="flex: 1; height: 5px; background: var(--border-color-light); border-radius: 3px; overflow: hidden; min-width: 60px;">
                                        <div style="height: 100%; width: {{ $progress }}%; background: var(--primary);"></div>
                                    </div>
                                    <span style="font-size: 12px; color: var(--text-muted); min-width: 32px;">{{ $progress }}%</span>
                                </div>
                            @endif
                        </td>
                        <td>
                            @if ($plan->next_review_date)
                                {{ $plan->next_review_date->format('d M Y') }}
                                @if ($plan->isOverdueForReview())
                                    <x-gentelella::badge tone="red">Overdue</x-gentelella::badge>
                                @endif
                            @else
                                <span style="color: var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td>
                            <x-gentelella::badge :tone="$plan->status_tone">
                                {{ $plan->status_label }}
                            </x-gentelella::badge>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="{{ route('admin.treatment-plans.show', $plan) }}" class="btn btn-sm btn-outline">View</a>
                            @can('update', $plan)
                                <a href="{{ route('admin.treatment-plans.edit', $plan) }}" class="btn btn-sm btn-outline">Edit</a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 32px 16px; color: var(--text-muted);">
                            No treatment plans found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-gentelella::table>

        @if ($plans->hasPages())
            <div style="padding: 12px 16px; border-top: 1px solid var(--border-color-light);">
                {{ $plans->links() }}
            </div>
        @endif
    </x-gentelella::card>
@endsection