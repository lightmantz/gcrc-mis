@extends('gentelella::page')

@section('title', 'Diagnoses')
@section('page_key', 'diagnoses')

@section('content')
    <x-gentelella::page-header title="Diagnoses" pretitle="Clinical">
        <x-slot:actions>
            @can('diagnoses.create')
                <a href="{{ route('admin.diagnoses.create') }}" class="btn btn-primary">
                    Record Diagnosis
                </a>
            @endcan
        </x-slot:actions>
    </x-gentelella::page-header>

    <x-gentelella::card flush>
        <div class="card-body" style="border-bottom: 1px solid var(--border-color-light);">
            <form method="GET" class="form-row cols-3" style="align-items: end;">
                <div class="form-group">
                    <label class="form-label" for="condition">Condition</label>
                    <select id="condition" name="condition" class="form-control">
                        <option value="">All conditions</option>
                        @foreach (\App\Diagnoses\DiagnosisVocabulary::options() as $key => $label)
                            <option value="{{ $key }}" @selected(request('condition') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="">All</option>
                        @foreach (['active' => 'Active', 'resolved' => 'Resolved', 'ruled_out' => 'Ruled Out', 'transferred' => 'Transferred'] as $v => $l)
                            <option value="{{ $v }}" @selected(request('status') === $v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="type">Type</label>
                    <select id="type" name="type" class="form-control">
                        <option value="">All</option>
                        <option value="primary" @selected(request('type') === 'primary')>Primary</option>
                        <option value="secondary" @selected(request('type') === 'secondary')>Secondary</option>
                    </select>
                </div>

                <div class="form-actions" style="margin: 0; padding: 0; border: none;">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.diagnoses.index') }}" class="btn btn-outline">Reset</a>
                </div>
            </form>
        </div>

        <x-gentelella::table>
            <thead>
                <tr>
                    <th>Child</th>
                    <th>Condition</th>
                    <th>Type</th>
                    <th>Severity</th>
                    <th>Status</th>
                    <th>Diagnosed</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($diagnoses as $diagnosis)
                    <tr>
                        <td>
                            <a href="{{ route('admin.children.show', $diagnosis->child) }}">
                                {{ $diagnosis->child->full_name }}
                            </a>
                            <div style="font-size: 11px; color: var(--text-muted);">
                                {{ $diagnosis->child->child_number }}
                            </div>
                        </td>
                        <td class="cell-strong">
                            <a href="{{ route('admin.diagnoses.show', $diagnosis) }}">
                                {{ $diagnosis->display_label }}
                            </a>
                            @if ($diagnosis->isProvisional())
                                <x-gentelella::badge tone="yellow">Provisional</x-gentelella::badge>
                            @endif
                        </td>
                        <td>
                            <x-gentelella::badge :tone="$diagnosis->diagnosis_type_tone">
                                {{ $diagnosis->diagnosis_type_label }}
                            </x-gentelella::badge>
                        </td>
                        <td>
                            <x-gentelella::badge :tone="$diagnosis->severity_tone">
                                {{ $diagnosis->severity_label }}
                            </x-gentelella::badge>
                        </td>
                        <td>
                            <x-gentelella::badge :tone="$diagnosis->status_tone">
                                {{ $diagnosis->status_label }}
                            </x-gentelella::badge>
                        </td>
                        <td>{{ $diagnosis->diagnosed_at->format('d M Y') }}</td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="{{ route('admin.diagnoses.show', $diagnosis) }}"
                               class="btn btn-sm btn-outline">View</a>
                            @can('update', $diagnosis)
                                <a href="{{ route('admin.diagnoses.edit', $diagnosis) }}"
                                   class="btn btn-sm btn-outline">Edit</a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 32px 16px; color: var(--text-muted);">
                            No diagnoses recorded yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-gentelella::table>

        @if ($diagnoses->hasPages())
            <div style="padding: 12px 16px; border-top: 1px solid var(--border-color-light);">
                {{ $diagnoses->links() }}
            </div>
        @endif
    </x-gentelella::card>
@endsection