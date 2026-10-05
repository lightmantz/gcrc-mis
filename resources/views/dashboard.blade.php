@extends('gentelella::page')

@section('title', 'Dashboard')
@section('page_key', 'dashboard')

@section('content')
    <x-gentelella::page-header title="Dashboard" pretitle="Overview" />

    {{-- ═══ Headline stat tiles ═══ --}}
    <div class="row col-3">
        @if ($stats['children'])
            <x-gentelella::card>
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted);">
                            Children Enrolled
                        </div>
                        <div style="font-size: 26px; font-weight: 600; line-height: 1.1; margin: 6px 0;">
                            {{ number_format($stats['children']['total']) }}
                        </div>
                        <div style="font-size: 12px; color: var(--text-muted);">
                            <strong style="color: var(--green);">{{ $stats['children']['active'] }}</strong> active
                            · {{ $stats['children']['new_30'] }} new
                        </div>
                    </div>
                    <div style="width: 40px; height: 40px; border-radius: var(--radius); background: var(--primary-lt); color: var(--primary); display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="4"/><path d="M5 20c0-3.9 3.1-7 7-7s7 3.1 7 7"/></svg>
                    </div>
                </div>
            </x-gentelella::card>
        @endif

        @if ($stats['assessments'])
            <x-gentelella::card>
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted);">
                            Assessments
                        </div>
                        <div style="font-size: 26px; font-weight: 600; line-height: 1.1; margin: 6px 0;">
                            {{ number_format($stats['assessments']['total']) }}
                        </div>
                        <div style="font-size: 12px; color: var(--text-muted);">
                            <strong style="color: var(--yellow);">{{ $stats['assessments']['drafts'] }}</strong> drafts
                            · {{ $stats['assessments']['finalized'] }} finalized
                        </div>
                    </div>
                    <div style="width: 40px; height: 40px; border-radius: var(--radius); background: var(--blue-lt); color: var(--blue); display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/></svg>
                    </div>
                </div>
            </x-gentelella::card>
        @endif

        @if ($stats['diagnoses'])
            <x-gentelella::card>
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted);">
                            Active Diagnoses
                        </div>
                        <div style="font-size: 26px; font-weight: 600; line-height: 1.1; margin: 6px 0;">
                            {{ number_format($stats['diagnoses']['active']) }}
                        </div>
                        <div style="font-size: 12px; color: var(--text-muted);">
                            {{ $stats['diagnoses']['this_month'] }} recorded this month
                        </div>
                    </div>
                    <div style="width: 40px; height: 40px; border-radius: var(--radius); background: var(--purple-lt); color: var(--purple); display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 3v18M5 7h14M5 17h14"/></svg>
                    </div>
                </div>
            </x-gentelella::card>
        @endif

        @if ($stats['referrals'])
            <x-gentelella::card>
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted);">
                            Referrals
                        </div>
                        <div style="font-size: 26px; font-weight: 600; line-height: 1.1; margin: 6px 0;">
                            {{ number_format($stats['referrals']['total']) }}
                        </div>
                        <div style="font-size: 12px; color: var(--text-muted);">
                            <strong style="color: var(--blue);">{{ $stats['referrals']['pending'] }}</strong> pending
                            · {{ $stats['referrals']['new_30'] }} this month
                        </div>
                    </div>
                    <div style="width: 40px; height: 40px; border-radius: var(--radius); background: var(--green-lt); color: var(--green); display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h10l6 6v10H4z"/><path d="M14 4v6h6"/></svg>
                    </div>
                </div>
            </x-gentelella::card>
        @endif

        @if ($stats['staff'])
            <x-gentelella::card>
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted);">
                            Staff
                        </div>
                        <div style="font-size: 26px; font-weight: 600; line-height: 1.1; margin: 6px 0;">
                            {{ number_format($stats['staff']['total']) }}
                        </div>
                        <div style="font-size: 12px; color: var(--text-muted);">
                            <strong style="color: var(--green);">{{ $stats['staff']['active'] }}</strong> active
                            · {{ $stats['staff']['clinical'] }} clinical
                        </div>
                    </div>
                    <div style="width: 40px; height: 40px; border-radius: var(--radius); background: var(--cyan-lt); color: var(--cyan); display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="4"/><path d="M5 20c0-3.9 3.1-7 7-7s7 3.1 7 7"/></svg>
                    </div>
                </div>
            </x-gentelella::card>
        @endif

        @if ($stats['guardians'])
            <x-gentelella::card>
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px; color: var(--text-muted);">
                            Guardians
                        </div>
                        <div style="font-size: 26px; font-weight: 600; line-height: 1.1; margin: 6px 0;">
                            {{ number_format($stats['guardians']['total']) }}
                        </div>
                        <div style="font-size: 12px; color: var(--text-muted);">
                            Registered family contacts
                        </div>
                    </div>
                    <div style="width: 40px; height: 40px; border-radius: var(--radius); background: var(--yellow-lt); color: var(--yellow); display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                </div>
            </x-gentelella::card>
        @endif
    </div>

    {{-- ═══ Charts row 1: registrations + assessments ═══ --}}
    @if ($stats['children'] || $stats['assessments'])
        <div class="row col-8-4" style="margin-top: 16px;">
            @if ($stats['children'])
                <x-gentelella::card title="Children Registered — Last 12 Months">
                    <div id="chart-registrations" style="height: 260px;"></div>
                </x-gentelella::card>
            @endif

            @if ($stats['children'])
                <x-gentelella::card title="Children by Condition">
                    <div id="chart-conditions" style="height: 260px;"></div>
                </x-gentelella::card>
            @endif
        </div>
    @endif

    {{-- ═══ Charts row 2: assessment activity + referral sources ═══ --}}
    @if ($stats['assessments'] || $stats['referrals'])
        <div class="row col-8-4" style="margin-top: 16px;">
            @if ($stats['assessments'])
                <x-gentelella::card title="Assessment Activity — Last 12 Months">
                    <div id="chart-assessments" style="height: 260px;"></div>
                </x-gentelella::card>
            @endif

            @if ($stats['referrals'])
                <x-gentelella::card title="Referral Sources">
                    <div id="chart-referrals" style="height: 260px;"></div>
                </x-gentelella::card>
            @endif
        </div>
    @endif

    {{-- ═══ Charts row 3: severity + activity feed ═══ --}}
    @if ($stats['diagnoses'] || $activity->isNotEmpty())
        <div class="row col-8-4" style="margin-top: 16px;">
            @if ($stats['diagnoses'])
                <x-gentelella::card title="Active Diagnoses by Severity">
                    <div id="chart-severity" style="height: 260px;"></div>
                </x-gentelella::card>
            @endif

            @if ($activity->isNotEmpty())
                <x-gentelella::card title="Recent Activity">
                    <div style="display: flex; flex-direction: column; gap: 12px; max-height: 260px; overflow-y: auto;">
                        @foreach ($activity as $item)
                            <div style="display: flex; gap: 10px; align-items: flex-start;">
                                <div style="width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: var(--{{ $item['tone'] }}-lt); color: var(--{{ $item['tone'] }});">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="8"/></svg>
                                </div>
                                <div style="min-width: 0; flex: 1;">
                                    <a href="{{ $item['url'] }}" style="font-size: 12.5px; color: var(--text); text-decoration: none;">
                                        {{ $item['text'] }}
                                    </a>
                                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">
                                        {{ $item['time']->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-gentelella::card>
            @endif
        </div>
    @endif

    {{-- ═══ Recent assessments + pending ═══ --}}
    <div class="row col-8-4" style="margin-top: 16px;">
        <div>
            @if ($recentAssessments->isNotEmpty())
                <x-gentelella::card title="Recent Assessments" flush>
                    <x-gentelella::table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Child</th>
                                <th>Type</th>
                                <th>Assessor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentAssessments as $a)
                                <tr>
                                    <td>{{ $a->assessment_date->format('d M') }}</td>
                                    <td>
                                        <a href="{{ route('admin.children.show', $a->child) }}">
                                            {{ $a->child->full_name }}
                                        </a>
                                    </td>
                                    <td>{{ $a->type_label }}</td>
                                    <td>{{ $a->assessor->full_name ?? '—' }}</td>
                                    <td>
                                        <x-gentelella::badge :tone="$a->status_tone">
                                            {{ $a->status_label }}
                                        </x-gentelella::badge>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-gentelella::table>
                </x-gentelella::card>
            @endif

            @if ($pendingItems->isNotEmpty())
                <x-gentelella::card title="Pending Your Action" style="margin-top: 16px;">
                    @foreach ($pendingItems as $item)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid var(--border-color-light);">
                            <div>
                                <a href="{{ $item['url'] }}" style="font-size: 13px; font-weight: 500;">
                                    {{ $item['label'] }}
                                </a>
                                <div style="font-size: 11.5px; color: var(--text-muted);">
                                    {{ $item['subtitle'] }}
                                    — {{ $item['date']->format('d M Y') }}
                                </div>
                            </div>
                            <a href="{{ $item['url'] }}" class="btn btn-sm btn-outline">Open</a>
                        </div>
                    @endforeach
                </x-gentelella::card>
            @endif
        </div>

        <div>
            @if ($staffByCategory->isNotEmpty())
                <x-gentelella::card title="Staff by Category">
                    <div id="chart-staff" style="height: 220px;"></div>
                </x-gentelella::card>
            @endif
        </div>
    </div>

    {{-- ═══ Charts initialisation ═══ --}}
    <script type="module">
        const ApexCharts = window.ApexCharts;

        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const textColor = isDark ? '#e6ebf2' : '#1e2633';
        const mutedColor = isDark ? '#8a93a3' : '#7e8896';
        const borderColor = isDark ? 'rgba(255,255,255,.08)' : '#e6e7eb';
        const primary = '#1ABB9C';

        const baseChart = {
            chart: {
                fontFamily: 'Inter, sans-serif',
                toolbar: { show: false },
                zoom: { enabled: false },
                animations: { enabled: true, speed: 400 },
            },
            grid: {
                borderColor: borderColor,
                strokeDashArray: 4,
                padding: { left: 8, right: 8 },
            },
            tooltip: { theme: isDark ? 'dark' : 'light' },
        };

        // 1 — Children registered per month (area chart)
        const regData = @json($registrationChart ?? ['labels' => [], 'values' => []]);
        const regEl = document.getElementById('chart-registrations');
        if (regEl && regData.labels.length) {
            new ApexCharts(regEl, {
                ...baseChart,
                chart: { ...baseChart.chart, type: 'area', height: 260 },
                series: [{ name: 'Children', data: regData.values }],
                xaxis: {
                    categories: regData.labels,
                    labels: { style: { colors: mutedColor, fontSize: '11px' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                },
                yaxis: {
                    labels: { style: { colors: mutedColor, fontSize: '11px' } },
                    forceNiceScale: true,
                    min: 0,
                },
                colors: [primary],
                stroke: { curve: 'smooth', width: 2 },
                fill: {
                    type: 'gradient',
                    gradient: { shadeIntensity: 1, opacityFrom: .35, opacityTo: .05, stops: [0, 90, 100] },
                },
                dataLabels: { enabled: false },
                markers: { size: 4, colors: [primary], strokeWidth: 2, strokeColors: isDark ? '#1a2332' : '#fff', hover: { size: 6 } },
            }).render();
        }

        // 2 — Children by condition (donut)
        const condData = @json($conditionChart ?? ['labels' => [], 'values' => []]);
        const condEl = document.getElementById('chart-conditions');
        if (condEl && condData.labels.length) {
            new ApexCharts(condEl, {
                ...baseChart,
                chart: { ...baseChart.chart, type: 'donut', height: 260 },
                series: condData.values,
                labels: condData.labels,
                colors: ['#1ABB9C', '#4299e1', '#ae3ec9', '#f59f00', '#2fb344', '#d63939', '#17a2b8', '#f76707', '#066fd1', '#d6336c'],
                legend: {
                    position: 'bottom',
                    labels: { colors: textColor },
                    fontSize: '12px',
                    markers: { width: 8, height: 8, radius: 4 },
                },
                dataLabels: { enabled: false },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                name: { color: mutedColor, fontSize: '12px' },
                                value: { color: textColor, fontSize: '20px', fontWeight: 600 },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    color: mutedColor,
                                    formatter: (w) => w.globals.seriesTotals.reduce((a, b) => a + b, 0),
                                },
                            },
                        },
                    },
                },
                stroke: { width: 0 },
            }).render();
        }

        // 3 — Assessment activity (stacked bar)
        const assessData = @json($assessmentChart ?? ['labels' => [], 'draft' => [], 'finalized' => []]);
        const assessEl = document.getElementById('chart-assessments');
        if (assessEl && assessData.labels.length) {
            new ApexCharts(assessEl, {
                ...baseChart,
                chart: { ...baseChart.chart, type: 'bar', height: 260, stacked: true },
                series: [
                    { name: 'Finalized', data: assessData.finalized },
                    { name: 'Draft', data: assessData.draft },
                ],
                xaxis: {
                    categories: assessData.labels,
                    labels: { style: { colors: mutedColor, fontSize: '11px' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                },
                yaxis: {
                    labels: { style: { colors: mutedColor, fontSize: '11px' } },
                    forceNiceScale: true,
                    min: 0,
                },
                colors: ['#2fb344', '#f59f00'],
                plotOptions: {
                    bar: { borderRadius: 3, columnWidth: '55%', borderRadiusApplication: 'end' },
                },
                dataLabels: { enabled: false },
                legend: { position: 'top', horizontalAlign: 'right', labels: { colors: textColor } },
            }).render();
        }

        // 4 — Referral sources (horizontal bar)
        const refData = @json($referralChart ?? ['labels' => [], 'values' => []]);
        const refEl = document.getElementById('chart-referrals');
        if (refEl && refData.labels.length) {
            new ApexCharts(refEl, {
                ...baseChart,
                chart: { ...baseChart.chart, type: 'bar', height: 260 },
                series: [{ name: 'Referrals', data: refData.values }],
                xaxis: {
                    categories: refData.labels,
                    labels: { style: { colors: mutedColor, fontSize: '11px' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                },
                yaxis: {
                    labels: { style: { colors: mutedColor, fontSize: '11px' } },
                    forceNiceScale: true,
                    min: 0,
                },
                colors: ['#4299e1'],
                plotOptions: {
                    bar: { horizontal: false, borderRadius: 3, columnWidth: '50%', borderRadiusApplication: 'end' },
                },
                dataLabels: { enabled: false },
            }).render();
        }

        // 5 — Diagnoses by severity (pie)
        const sevData = @json($severityChart ?? ['labels' => [], 'values' => []]);
        const sevEl = document.getElementById('chart-severity');
        if (sevEl && sevData.labels.length) {
            new ApexCharts(sevEl, {
                ...baseChart,
                chart: { ...baseChart.chart, type: 'pie', height: 260 },
                series: sevData.values,
                labels: sevData.labels,
                colors: ['#2fb344', '#f59f00', '#d63939', '#ae3ec9', '#7e8896'],
                legend: {
                    position: 'bottom',
                    labels: { colors: textColor },
                    fontSize: '12px',
                    markers: { width: 8, height: 8, radius: 4 },
                },
                dataLabels: {
                    enabled: true,
                    style: { fontSize: '11px', fontWeight: 500, colors: ['#fff'] },
                    dropShadow: { enabled: false },
                },
                stroke: { width: 0 },
            }).render();
        }

        // 6 — Staff by category (small donut)
        const staffEl = document.getElementById('chart-staff');
        const staffData = @json($staffByCategory ?? []);
        if (staffEl && Object.keys(staffData).length) {
            const labels = Object.keys(staffData).map((k) => k.replace('_', ' ').replace(/\b\w/g, (c) => c.toUpperCase()));
            const values = Object.values(staffData);

            new ApexCharts(staffEl, {
                ...baseChart,
                chart: { ...baseChart.chart, type: 'donut', height: 220 },
                series: values,
                labels: labels,
                colors: ['#1ABB9C', '#4299e1', '#ae3ec9', '#f59f00', '#2fb344', '#17a2b8'],
                legend: {
                    position: 'bottom',
                    labels: { colors: textColor },
                    fontSize: '11.5px',
                    markers: { width: 8, height: 8, radius: 4 },
                },
                dataLabels: { enabled: false },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '68%',
                            labels: {
                                show: true,
                                name: { color: mutedColor, fontSize: '11px' },
                                value: { color: textColor, fontSize: '18px', fontWeight: 600 },
                                total: {
                                    show: true,
                                    label: 'Staff',
                                    color: mutedColor,
                                    formatter: (w) => w.globals.seriesTotals.reduce((a, b) => a + b, 0),
                                },
                            },
                        },
                    },
                },
                stroke: { width: 0 },
            }).render();
        }
    </script>
@endsection