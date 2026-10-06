<x-gentelella::card title="Goals" style="margin-top: 16px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
        <span style="color: var(--text-muted); font-size: 12px;">
            {{ $plan->goals->count() }} {{ Str::plural('goal', $plan->goals->count()) }}
        </span>
    </div>

    @forelse ($plan->goals as $goal)
        <div style="border: 1px solid var(--border-color); border-radius: var(--radius); padding: 14px; margin-bottom: 12px;">
            {{-- Header row --}}
            <div style="display: flex; justify-content: space-between; align-items: start; gap: 12px; margin-bottom: 8px;">
                <div style="min-width: 0; flex: 1;">
                    <div style="font-size: 14px; font-weight: 500;">{{ $goal->title }}</div>
                    <div style="display: flex; flex-wrap: wrap; gap: 4px; margin-top: 6px;">
                        <x-gentelella::badge :tone="$goal->category_tone">{{ $goal->category_label }}</x-gentelella::badge>
                        <x-gentelella::badge :tone="$goal->priority_tone">{{ $goal->priority_label }}</x-gentelella::badge>
                        <x-gentelella::badge :tone="$goal->status_tone">{{ $goal->status_label }}</x-gentelella::badge>
                        @if ($goal->is_overdue)
                            <x-gentelella::badge tone="red">Overdue</x-gentelella::badge>
                        @endif
                    </div>
                </div>

                @if ($goal->target_date)
                    <div style="text-align: right; font-size: 11.5px; color: var(--text-muted);">
                        Target<br>
                        <strong style="color: var(--text); font-size: 12.5px;">{{ $goal->target_date->format('d M Y') }}</strong>
                    </div>
                @endif
            </div>

            {{-- Description --}}
            @if ($goal->description)
                <p style="font-size: 12.5px; color: var(--text-secondary); margin-bottom: 8px;">
                    {{ $goal->description }}
                </p>
            @endif

            {{-- Baseline / Target / Measure --}}
            @if ($goal->baseline || $goal->target || $goal->measure)
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin: 10px 0; font-size: 12px;">
                    @if ($goal->baseline)
                        <div>
                            <div style="font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.3px; color: var(--text-muted); margin-bottom: 2px;">Baseline</div>
                            <div>{{ $goal->baseline }}</div>
                        </div>
                    @endif
                    @if ($goal->target)
                        <div>
                            <div style="font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.3px; color: var(--text-muted); margin-bottom: 2px;">Target</div>
                            <div>{{ $goal->target }}</div>
                        </div>
                    @endif
                    @if ($goal->measure)
                        <div>
                            <div style="font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.3px; color: var(--text-muted); margin-bottom: 2px;">Measure</div>
                            <div>{{ $goal->measure }}</div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Progress bar --}}
            <div style="margin: 10px 0;">
                <div style="display: flex; justify-content: space-between; font-size: 11.5px; color: var(--text-muted); margin-bottom: 4px;">
                    <span>Progress</span>
                    <span>{{ $goal->progress_percentage }}%</span>
                </div>
                <div style="height: 6px; background: var(--border-color-light); border-radius: 3px; overflow: hidden;">
                    <div style="height: 100%; width: {{ $goal->progress_percentage }}%; background: {{ $goal->status === 'achieved' ? 'var(--green)' : 'var(--primary)' }};"></div>
                </div>
            </div>

            {{-- Progress notes --}}
            @if ($goal->progressNotes->isNotEmpty())
                <details style="margin-top: 10px;">
                    <summary style="cursor: pointer; font-size: 12px; color: var(--text-muted);">
                        Progress notes ({{ $goal->progressNotes->count() }})
                    </summary>
                    <div style="margin-top: 8px; padding-left: 12px; border-left: 2px solid var(--border-color-light);">
                        @foreach ($goal->progressNotes as $note)
                            <div style="padding: 6px 0; font-size: 12.5px; border-bottom: 1px solid var(--border-color-light);">
                                <div style="color: var(--text-muted); font-size: 11.5px; margin-bottom: 2px;">
                                    {{ $note->recorded_on->format('d M Y') }}
                                    @if ($note->recordedBy)
                                        — {{ $note->recordedBy->full_name }}
                                    @endif
                                    @if ($note->progress_percentage !== null)
                                        — {{ $note->progress_percentage }}%
                                    @endif
                                </div>
                                <div style="white-space: pre-line;">{{ $note->note }}</div>
                            </div>
                        @endforeach
                    </div>
                </details>
            @endif

            {{-- Actions (only if not on a closed plan) --}}
            @can('update', $goal)
                <div style="margin-top: 12px; display: flex; flex-wrap: wrap; gap: 8px;">
                    {{-- Update goal --}}
                    <details style="display: inline-block; position: relative;">
                        <summary class="btn btn-sm btn-outline" style="cursor: pointer; list-style: none;">
                            Update Goal
                        </summary>
                        <div style="position: absolute; z-index: 20; left: 0; top: 36px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius); box-shadow: 0 8px 24px rgba(15,23,42,0.12); padding: 16px; width: 440px;">
                            <form method="POST" action="{{ route('admin.treatment-plans.goals.update', [$plan, $goal]) }}">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control">
                                        @foreach (['not_started' => 'Not Started', 'in_progress' => 'In Progress', 'achieved' => 'Achieved', 'partially_achieved' => 'Partially Achieved', 'discontinued' => 'Discontinued'] as $v => $l)
                                            <option value="{{ $v }}" @selected($goal->status === $v)>{{ $l }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Progress ({{ $goal->progress_percentage }}%)</label>
                                    <input type="range" name="progress_percentage" min="0" max="100" step="5"
                                           value="{{ $goal->progress_percentage }}" class="form-control"
                                           oninput="this.nextElementSibling.textContent = this.value + '%'">
                                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
                                        {{ $goal->progress_percentage }}%
                                    </div>
                                </div>

                                {{-- Hidden fields to preserve other values --}}
                                <input type="hidden" name="title" value="{{ $goal->title }}">
                                <input type="hidden" name="category" value="{{ $goal->category }}">
                                <input type="hidden" name="priority" value="{{ $goal->priority }}">

                                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                            </form>
                        </div>
                    </details>

                    {{-- Log progress note --}}
                    @can('recordProgress', $goal)
                        <details style="display: inline-block; position: relative;">
                            <summary class="btn btn-sm btn-outline" style="cursor: pointer; list-style: none;">
                                Log Progress
                            </summary>
                            <div style="position: absolute; z-index: 20; left: 0; top: 36px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius); box-shadow: 0 8px 24px rgba(15,23,42,0.12); padding: 16px; width: 440px;">
                                <form method="POST" action="{{ route('admin.treatment-plans.goals.progress.store', [$plan, $goal]) }}">
                                    @csrf

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Date</label>
                                            <input type="date" name="recorded_on" class="form-control"
                                                   value="{{ now()->toDateString() }}"
                                                   max="{{ now()->toDateString() }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">New progress %</label>
                                            <input type="number" name="progress_percentage" class="form-control"
                                                   min="0" max="100" value="{{ $goal->progress_percentage }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">New status (optional)</label>
                                        <select name="status_at_record" class="form-control">
                                            <option value="">— Leave unchanged —</option>
                                            @foreach (['not_started' => 'Not Started', 'in_progress' => 'In Progress', 'achieved' => 'Achieved', 'partially_achieved' => 'Partially Achieved', 'discontinued' => 'Discontinued'] as $v => $l)
                                                <option value="{{ $v }}">{{ $l }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Note</label>
                                        <textarea name="note" class="form-control" rows="3" required
                                                  placeholder="What happened in this session?"></textarea>
                                    </div>

                                    <label class="form-check">
                                        <input type="checkbox" name="update_goal" value="1" checked>
                                        <span>Also update the goal's status and progress</span>
                                    </label>

                                    <button type="submit" class="btn btn-primary btn-sm" style="margin-top: 10px;">Record</button>
                                </form>
                            </div>
                        </details>
                    @endcan

                    {{-- Delete (only on draft plans) --}}
                    @can('delete', $goal)
                        <form method="POST" action="{{ route('admin.treatment-plans.goals.destroy', [$plan, $goal]) }}"
                              style="display: inline;"
                              onsubmit="return confirm('Delete this goal?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    @endcan
                </div>
            @endcan
        </div>
    @empty
        <p style="color: var(--text-muted); font-size: 13px;">No goals recorded yet.</p>
    @endforelse

    {{-- Add goal (only if the plan isn't closed) --}}
    @if (! $plan->isClosed())
        @can('update', $plan)
            <details style="margin-top: 16px;">
                <summary class="btn btn-outline" style="cursor: pointer; list-style: none;">
                    Add Goal
                </summary>

                <div style="margin-top: 12px; padding: 16px; border: 1px solid var(--border-color); border-radius: var(--radius);">
                    <form method="POST" action="{{ route('admin.treatment-plans.goals.store', $plan) }}">
                        @csrf

                        <div class="form-group">
                            <label class="form-label">Title <span class="required">*</span></label>
                            <input type="text" name="title" class="form-control" required
                                   placeholder="e.g. Improve standing balance">
                        </div>

                        <div class="form-row cols-3">
                            <div class="form-group">
                                <label class="form-label">Category <span class="required">*</span></label>
                                <select name="category" class="form-control" required>
                                    @foreach (\App\Support\GoalCategories::options() as $k => $label)
                                        <option value="{{ $k }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Priority <span class="required">*</span></label>
                                <select name="priority" class="form-control" required>
                                    <option value="high">High</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="low">Low</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Target date</label>
                                <input type="date" name="target_date" class="form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="form-row cols-3">
                            <div class="form-group">
                                <label class="form-label">Baseline</label>
                                <textarea name="baseline" class="form-control" rows="2"
                                          placeholder="Starting point — e.g. holds head for 10 seconds"></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Target</label>
                                <textarea name="target" class="form-control" rows="2"
                                          placeholder="Success criteria — e.g. holds head for 60 seconds"></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Measure</label>
                                <textarea name="measure" class="form-control" rows="2"
                                          placeholder="How progress is judged"></textarea>
                            </div>
                        </div>

                        <div class="form-row cols-3">
                            <div class="form-group">
                                <label class="form-label">Initial status</label>
                                <select name="status" class="form-control">
                                    <option value="not_started">Not Started</option>
                                    <option value="in_progress">In Progress</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Initial progress %</label>
                                <input type="number" name="progress_percentage" class="form-control"
                                       min="0" max="100" value="0">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Add Goal</button>
                    </form>
                </div>
            </details>
        @endcan
    @endif
</x-gentelella::card>