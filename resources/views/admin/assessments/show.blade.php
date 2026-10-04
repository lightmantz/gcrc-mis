@extends('gentelella::page')

@section('title', 'Assessment')
@section('page_key', 'assessments')

@section('content')
    <x-gentelella::page-header title="{{ $assessment->type_label }}"
                                pretitle="{{ $assessment->child->full_name }}">
        <x-slot:actions>
            @can('update', $assessment)
                <a href="{{ route('admin.assessments.edit', $assessment) }}" class="btn btn-outline">Edit Draft</a>
            @endcan
            @can('finalize', $assessment)
                <form method="POST" action="{{ route('admin.assessments.finalize', $assessment) }}"
                      style="display: inline;"
                      onsubmit="return confirm('Finalize this assessment? It will become read-only.');">
                    @csrf
                    <button type="submit" class="btn btn-primary">Finalize</button>
                </form>
            @endcan
        </x-slot:actions>
    </x-gentelella::page-header>

    <div class="row col-8-4">
        <div>
            <x-gentelella::card title="Summary">
                <dl style="display: grid; grid-template-columns: auto 1fr; gap: 8px 16px; font-size: 13px;">
                    <dt style="color: var(--text-muted);">Child</dt>
                    <dd>
                        <a href="{{ route('admin.children.show', $assessment->child) }}">
                            {{ $assessment->child->full_name }}
                        </a>
                        <span style="color: var(--text-muted); font-size: 12px;">
                            ({{ $assessment->child->child_number }})
                        </span>
                    </dd>

                    <dt style="color: var(--text-muted);">Type</dt>
                    <dd>{{ $assessment->type_label }}</dd>

                    <dt style="color: var(--text-muted);">Date</dt>
                    <dd>{{ $assessment->assessment_date->format('d M Y') }}</dd>

                    <dt style="color: var(--text-muted);">Assessor</dt>
                    <dd>{{ $assessment->assessor->full_name ?? '—' }}</dd>

                    <dt style="color: var(--text-muted);">Status</dt>
                    <dd>
                        <x-gentelella::badge :tone="$assessment->status_tone">
                            {{ $assessment->status_label }}
                        </x-gentelella::badge>
                        @if ($assessment->isFinalized())
                            <span style="color: var(--text-muted); font-size: 12px;">
                                finalized {{ $assessment->finalized_at->diffForHumans() }}
                                @if ($assessment->finalizedBy)
                                    by {{ $assessment->finalizedBy->name }}
                                @endif
                            </span>
                        @endif
                    </dd>
                </dl>
            </x-gentelella::card>

            @if ($type)
                @foreach ($type->sections() as $sectionLabel => $fields)
                    <x-gentelella::card title="{{ $sectionLabel }}" style="margin-top: 16px;">
                        <dl style="display: grid; grid-template-columns: auto 1fr; gap: 8px 16px; font-size: 13px;">
                            @foreach ($fields as $name => $def)
                                <dt style="color: var(--text-muted);">{{ $def['label'] ?? ucfirst($name) }}</dt>
                                <dd style="white-space: pre-line;">
                                    @php
                                        $value = $assessment->finding($name);
                                        $display = $value;
                                        if (($def['type'] ?? null) === 'select' && isset($def['options'][$value])) {
                                            $display = $def['options'][$value];
                                        }
                                        if ($display === null || $display === '') $display = '—';
                                    @endphp
                                    {{ $display }}
                                </dd>
                            @endforeach
                        </dl>
                    </x-gentelella::card>
                @endforeach
            @endif

            @if ($assessment->summary)
                <x-gentelella::card title="Summary" style="margin-top: 16px;">
                    <p style="white-space: pre-line; font-size: 13px;">{{ $assessment->summary }}</p>
                </x-gentelella::card>
            @endif

            @if ($assessment->recommendations)
                <x-gentelella::card title="Recommendations" style="margin-top: 16px;">
                    <p style="white-space: pre-line; font-size: 13px;">{{ $assessment->recommendations }}</p>
                </x-gentelella::card>
            @endif
        </div>

        <div>
            @include('admin._shared._documents', [
                'documentable' => $assessment,
                'title' => 'Attachments',
            ])

            @can('delete', $assessment)
                <x-gentelella::card title="Danger Zone" style="margin-top: 16px;">
                    <form method="POST" action="{{ route('admin.assessments.destroy', $assessment) }}"
                          onsubmit="return confirm('Delete this draft assessment?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Draft</button>
                    </form>
                </x-gentelella::card>
            @endcan
        </div>
    </div>
@endsection