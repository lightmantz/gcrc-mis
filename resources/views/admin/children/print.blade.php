@extends('layouts.print')

@section('title', $child->full_name)
@section('document_title', 'Child Record')
@section('document_subtitle')
    {{ $child->child_number }} — {{ $child->full_name }}
@endsection

@section('content')
    {{-- ═══ Identity ═══ --}}
    <div class="section">
        <div class="section-title">Identity</div>
        <div class="field-grid cols-3">
            <div class="field">
                <div class="label">Child Number</div>
                <div class="value">{{ $child->child_number }}</div>
            </div>
            <div class="field">
                <div class="label">Full Name</div>
                <div class="value">{{ $child->full_name }}</div>
            </div>
            @if ($child->preferred_name)
                <div class="field">
                    <div class="label">Preferred Name</div>
                    <div class="value">{{ $child->preferred_name }}</div>
                </div>
            @endif
            <div class="field">
                <div class="label">Date of Birth</div>
                <div class="value">{{ $child->date_of_birth->format('d M Y') }} ({{ $child->age_display }})</div>
            </div>
            <div class="field">
                <div class="label">Gender</div>
                <div class="value">{{ ucfirst($child->gender) }}</div>
            </div>
            <div class="field">
                <div class="label">Status</div>
                <div class="value">{{ $child->status_label }}</div>
            </div>
            <div class="field">
                <div class="label">Registered</div>
                <div class="value">{{ $child->registration_date->format('d M Y') }}</div>
            </div>
            @if ($child->referred_by)
                <div class="field">
                    <div class="label">Referred By</div>
                    <div class="value">{{ $child->referred_by }}</div>
                </div>
            @endif
        </div>
    </div>

    {{-- ═══ Contact ═══ --}}
    <div class="section">
        <div class="section-title">Contact</div>
        <div class="field-grid cols-2">
            <div class="field">
                <div class="label">Phone</div>
                <div class="value">{{ $child->phone ?: '—' }}</div>
            </div>
            <div class="field">
                <div class="label">Email</div>
                <div class="value">{{ $child->email ?: '—' }}</div>
            </div>
            <div class="field full-width">
                <div class="label">Address</div>
                <div class="value">{{ $child->address ?: '—' }}</div>
            </div>
            <div class="field">
                <div class="label">District</div>
                <div class="value">{{ $child->district ?: '—' }}</div>
            </div>
            <div class="field">
                <div class="label">Region</div>
                <div class="value">{{ $child->region ?: '—' }}</div>
            </div>
        </div>
    </div>

    {{-- ═══ Medical Summary ═══ --}}
    @if (auth()->user()->can('children.view_medical') && $child->primary_condition)
        <div class="section">
            <div class="section-title">Medical Summary</div>
            <div class="field-grid cols-2">
                <div class="field">
                    <div class="label">Primary Condition</div>
                    <div class="value">{{ $child->primary_condition_label }}</div>
                </div>
                <div class="field">
                    <div class="label">Blood Type</div>
                    <div class="value">{{ $child->blood_type === 'unknown' ? '—' : $child->blood_type }}</div>
                </div>
                <div class="field full-width">
                    <div class="label">Allergies</div>
                    <div class="value">{{ $child->allergies ?: '—' }}</div>
                </div>
                <div class="field full-width">
                    <div class="label">Chronic Conditions</div>
                    <div class="value">{{ $child->chronic_conditions ?: '—' }}</div>
                </div>
                <div class="field full-width">
                    <div class="label">Current Medications</div>
                    <div class="value">{{ $child->current_medications ?: '—' }}</div>
                </div>
                @if ($child->disability_summary)
                    <div class="field full-width">
                        <div class="label">Disability Summary</div>
                        <div class="value">{{ $child->disability_summary }}</div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ═══ Special Care ═══ --}}
    @if ($child->special_care_requirements || $child->feeding_requirements || $child->requires_constant_supervision)
        <div class="section">
            <div class="section-title">Special Care Requirements</div>
            <div class="field-grid cols-2">
                @if ($child->special_care_requirements)
                    <div class="field full-width">
                        <div class="label">Special Care</div>
                        <div class="value">{{ $child->special_care_requirements }}</div>
                    </div>
                @endif
                @if ($child->feeding_requirements)
                    <div class="field full-width">
                        <div class="label">Feeding</div>
                        <div class="value">{{ $child->feeding_requirements }}</div>
                    </div>
                @endif
                @if ($child->mobility_notes)
                    <div class="field full-width">
                        <div class="label">Mobility</div>
                        <div class="value">{{ $child->mobility_notes }}</div>
                    </div>
                @endif
                @if ($child->communication_notes)
                    <div class="field full-width">
                        <div class="label">Communication</div>
                        <div class="value">{{ $child->communication_notes }}</div>
                    </div>
                @endif
                @if ($child->requires_constant_supervision)
                    <div class="field full-width">
                        <div class="value"><strong>⚠ Requires constant supervision</strong></div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ═══ Active Diagnoses ═══ --}}
    @php
        $activeDiagnoses = $child->diagnoses()->where('status', 'active')->orderByDesc('diagnosis_type')->get();
    @endphp
    @if ($activeDiagnoses->isNotEmpty())
        <div class="section">
            <div class="section-title">Active Diagnoses</div>
            <table class="print-table">
                <thead>
                    <tr>
                        <th>Condition</th>
                        <th>Type</th>
                        <th>Severity</th>
                        <th>Diagnosed</th>
                        <th>Diagnosed By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($activeDiagnoses as $diagnosis)
                        <tr>
                            <td>{{ $diagnosis->display_label }}</td>
                            <td>{{ $diagnosis->diagnosis_type_label }}</td>
                            <td>{{ $diagnosis->severity_label }}</td>
                            <td>{{ $diagnosis->diagnosed_at->format('d M Y') }}</td>
                            <td>{{ $diagnosis->diagnosedBy->full_name ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- ═══ Active Treatment Plans ═══ --}}
    @php
        $activePlans = $child->treatmentPlans()->active()->with(['leadStaff', 'goals'])->get();
    @endphp
    @if ($activePlans->isNotEmpty())
        <div class="section">
            <div class="section-title">Active Treatment Plans</div>
            @foreach ($activePlans as $plan)
                <div style="margin-bottom: 14px; padding-left: 10px; border-left: 2px solid #1ABB9C;">
                    <div style="font-weight: 600; margin-bottom: 4px;">
                        {{ $plan->discipline_label }}
                        <span style="font-weight: 400; color: #7e8896; font-size: 9pt;">
                            — Lead: {{ $plan->leadStaff->full_name ?? '—' }}
                            — Started {{ $plan->start_date->format('d M Y') }}
                        </span>
                    </div>

                    @if ($plan->overall_objectives)
                        <div style="font-size: 9.5pt; color: #626d7d; margin-bottom: 6px;">
                            {{ $plan->overall_objectives }}
                        </div>
                    @endif

                    @if ($plan->goals->isNotEmpty())
                        <table class="print-table" style="margin-top: 4px;">
                            <thead>
                                <tr>
                                    <th>Goal</th>
                                    <th>Category</th>
                                    <th style="text-align: center;">Progress</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($plan->goals as $goal)
                                    <tr>
                                        <td>
                                            {{ $goal->title }}
                                            @if ($goal->target)
                                                <div style="font-size: 8.5pt; color: #7e8896;">
                                                    Target: {{ $goal->target }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $goal->category_label }}</td>
                                        <td style="text-align: center;">{{ $goal->progress_percentage }}%</td>
                                        <td>{{ $goal->status_label }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- ═══ Guardians ═══ --}}
    @php
        $guardians = $child->guardians()->orderByPivot('is_primary', 'desc')->get();
    @endphp
    @if ($guardians->isNotEmpty())
        <div class="section">
            <div class="section-title">Guardians</div>
            <table class="print-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Relationship</th>
                        <th>Phone</th>
                        <th>Consent</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($guardians as $guardian)
                        <tr>
                            <td>
                                {{ $guardian->full_name }}
                                @if ($guardian->pivot->is_primary)
                                    <span class="badge badge-teal">Primary</span>
                                @endif
                                @if ($guardian->pivot->is_legal)
                                    <span class="badge badge-blue">Legal</span>
                                @endif
                            </td>
                            <td>{{ ucfirst($guardian->pivot->relationship) }}</td>
                            <td>{{ $guardian->phone }}</td>
                            <td>
                                @php
                                    $consents = [];
                                    if ($guardian->pivot->consent_medical) $consents[] = 'Medical';
                                    if ($guardian->pivot->consent_education) $consents[] = 'Education';
                                    if ($guardian->pivot->consent_photography) $consents[] = 'Photo';
                                @endphp
                                {{ empty($consents) ? '—' : implode(', ', $consents) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- ═══ Emergency Contacts ═══ --}}
    @php
        $emergencyContacts = $child->emergencyContacts()->ordered()->get();
    @endphp
    @if ($emergencyContacts->isNotEmpty())
        <div class="section">
            <div class="section-title">Emergency Contacts</div>
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">Priority</th>
                        <th>Name</th>
                        <th>Relationship</th>
                        <th>Phone</th>
                        <th>Alternate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($emergencyContacts as $contact)
                        <tr>
                            <td>{{ $contact->priority }}</td>
                            <td>{{ $contact->name }}</td>
                            <td>{{ $contact->relationship ?? '—' }}</td>
                            <td>{{ $contact->phone }}</td>
                            <td>{{ $contact->alternate_phone ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- ═══ OPTIONAL SECTIONS (enabled via query params) ═══ --}}

    {{-- Assessment History --}}
    @if (request()->boolean('include_assessments'))
        @php
            $assessments = $child->assessments()->with('assessor')->latest('assessment_date')->limit(20)->get();
        @endphp
        @if ($assessments->isNotEmpty())
            <div class="section page-break">
                <div class="section-title">Assessment History ({{ $assessments->count() }})</div>
                <table class="print-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Assessor</th>
                            <th>Status</th>
                            <th>Summary</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assessments as $a)
                            <tr>
                                <td>{{ $a->assessment_date->format('d M Y') }}</td>
                                <td>{{ $a->type_label }}</td>
                                <td>{{ $a->assessor->full_name ?? '—' }}</td>
                                <td>{{ $a->status_label }}</td>
                                <td style="font-size: 9pt; color: #626d7d;">
                                    {{ \Illuminate\Support\Str::limit($a->summary, 100) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif

    {{-- Past Treatment Plans --}}
    @if (request()->boolean('include_plans'))
        @php
            $pastPlans = $child->treatmentPlans()
                ->whereIn('status', ['completed', 'cancelled'])
                ->with('leadStaff')
                ->latest('start_date')
                ->get();
        @endphp
        @if ($pastPlans->isNotEmpty())
            <div class="section page-break">
                <div class="section-title">Past Treatment Plans ({{ $pastPlans->count() }})</div>
                <table class="print-table">
                    <thead>
                        <tr>
                            <th>Discipline</th>
                            <th>Lead</th>
                            <th>Period</th>
                            <th>Status</th>
                            <th>Outcome</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pastPlans as $plan)
                            <tr>
                                <td>{{ $plan->discipline_label }}</td>
                                <td>{{ $plan->leadStaff->full_name ?? '—' }}</td>
                                <td>
                                    {{ $plan->start_date->format('d M Y') }}
                                    @if ($plan->end_date)
                                        → {{ $plan->end_date->format('d M Y') }}
                                    @endif
                                </td>
                                <td>{{ $plan->status_label }}</td>
                                <td style="font-size: 9pt; color: #626d7d;">
                                    {{ \Illuminate\Support\Str::limit($plan->closure_reason, 80) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif

    {{-- Referrals --}}
    @if (request()->boolean('include_referrals'))
        @php
            $referrals = $child->referrals()->with('registeredBy')->get();
        @endphp
        @if ($referrals->isNotEmpty())
            <div class="section page-break">
                <div class="section-title">Referrals ({{ $referrals->count() }})</div>
                <table class="print-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Source</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($referrals as $referral)
                            <tr>
                                <td>{{ $referral->referral_date->format('d M Y') }}</td>
                                <td>
                                    {{ $referral->source_type_label }}
                                    @if ($referral->source_name)
                                        <div style="font-size: 8.5pt; color: #7e8896;">{{ $referral->source_name }}</div>
                                    @endif
                                </td>
                                <td style="font-size: 9pt;">{{ \Illuminate\Support\Str::limit($referral->reason, 100) }}</td>
                                <td>{{ $referral->status_label }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif

    {{-- Documents list --}}
    @if (request()->boolean('include_documents'))
        @php
            $documents = $child->documents()->with('uploadedBy')->get();
        @endphp
        @if ($documents->isNotEmpty())
            <div class="section page-break">
                <div class="section-title">Attached Documents ({{ $documents->count() }})</div>
                <table class="print-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Title</th>
                            <th>Size</th>
                            <th>Uploaded</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documents as $document)
                            <tr>
                                <td>{{ $document->category_label }}</td>
                                <td>{{ $document->title ?: $document->original_name }}</td>
                                <td>{{ $document->size_for_humans }}</td>
                                <td>{{ $document->created_at->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif

    {{-- ═══ Internal Notes ═══ --}}
    @if ($child->notes && auth()->user()->can('children.edit'))
        <div class="section page-break">
            <div class="section-title">Internal Notes (Confidential)</div>
            <div class="value" style="white-space: pre-line;">{{ $child->notes }}</div>
        </div>
    @endif
@endsection