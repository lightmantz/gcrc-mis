<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Print') — {{ config('gentelella.brand_name', 'GCRC') }}</title>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.45;
            color: #1e2633;
            background: #fff;
            padding: 16mm;
        }

        /* ─── Header ─────────────────────────────────── */
        .print-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 12px;
            margin-bottom: 20px;
            border-bottom: 2px solid #1ABB9C;
        }

        .print-header .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .print-header .brand-mark {
            width: 42px;
            height: 42px;
            background: #1ABB9C;
            color: #fff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
        }

        .print-header .brand-text h1 {
            font-size: 14pt;
            font-weight: 600;
            letter-spacing: -0.2px;
            color: #1e2633;
            margin-bottom: 2px;
        }

        .print-header .brand-text .subtitle {
            font-size: 9pt;
            color: #7e8896;
        }

        .print-header .meta {
            text-align: right;
            font-size: 9pt;
            color: #7e8896;
            line-height: 1.5;
        }

        .print-header .meta strong {
            color: #1e2633;
            font-weight: 500;
        }

        /* ─── Document title ──────────────────────────── */
        .doc-title {
            margin-bottom: 20px;
        }

        .doc-title h2 {
            font-size: 16pt;
            font-weight: 600;
            letter-spacing: -0.3px;
            margin-bottom: 4px;
        }

        .doc-title .pretitle {
            font-size: 10pt;
            color: #7e8896;
        }

        /* ─── Cards / sections ────────────────────────── */
        .section {
            margin-bottom: 16px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 9pt;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #7e8896;
            padding-bottom: 6px;
            border-bottom: 1px solid #e6e7eb;
            margin-bottom: 10px;
        }

        .field-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px 24px;
        }

        .field-grid.cols-1 { grid-template-columns: 1fr; }
        .field-grid.cols-3 { grid-template-columns: repeat(3, 1fr); }

        .field {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .field .label {
            font-size: 8.5pt;
            color: #7e8896;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .field .value {
            font-size: 10pt;
            color: #1e2633;
            white-space: pre-line;
            word-wrap: break-word;
        }

        .field.full-width {
            grid-column: 1 / -1;
        }

        /* ─── Badges ──────────────────────────────────── */
        .badge {
            display: inline-block;
            padding: 1px 6px;
            font-size: 8pt;
            font-weight: 500;
            border-radius: 3px;
            background: #f5f7fb;
            color: #626d7d;
            border: 1px solid #e6e7eb;
        }

        .badge-green { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
        .badge-red { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
        .badge-yellow { background: #fffbeb; color: #b45309; border-color: #fde68a; }
        .badge-blue { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
        .badge-teal { background: #f0fdfa; color: #0f766e; border-color: #99f6e4; }

        /* ─── Tables ──────────────────────────────────── */
        table.print-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
        }

        table.print-table th {
            text-align: left;
            padding: 6px 8px;
            background: #f5f7fb;
            font-weight: 600;
            color: #1e2633;
            border-bottom: 1px solid #e6e7eb;
            font-size: 8.5pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        table.print-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #eff0f3;
            vertical-align: top;
        }

        /* ─── Footer ─────────────────────────────────── */
        .print-footer {
            margin-top: 30px;
            padding-top: 12px;
            border-top: 1px solid #e6e7eb;
            font-size: 8.5pt;
            color: #7e8896;
            display: flex;
            justify-content: space-between;
        }

        /* ─── Action bar (hidden when printing) ───────── */
        .actions-bar {
            position: fixed;
            top: 16px;
            right: 16px;
            display: flex;
            gap: 8px;
            z-index: 100;
        }

        .actions-bar button,
        .actions-bar a {
            padding: 8px 14px;
            background: #1ABB9C;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 9pt;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            font-family: inherit;
        }

        .actions-bar a.secondary {
            background: #fff;
            color: #626d7d;
            border: 1px solid #e6e7eb;
        }

        .actions-bar button:hover { background: #169f85; }
        .actions-bar a.secondary:hover { background: #f5f7fb; }

        /* ─── Print-specific rules ──────────────────── */
        @media print {
            @page {
                margin: 14mm;
                size: A4;
            }

            body {
                padding: 0;
                font-size: 10pt;
            }

            .actions-bar { display: none !important; }

            .page-break { page-break-before: always; }

            .section { page-break-inside: avoid; }

            a { color: inherit; text-decoration: none; }
        }
    </style>
</head>
<body>
    {{-- Floating action bar (hidden on print) --}}
    <div class="actions-bar">
        <button type="button" onclick="window.print()">Print / Save as PDF</button>
        <a href="#" onclick="window.close(); return false;" class="secondary">Close</a>
    </div>

    {{-- Header --}}
    <div class="print-header">
        <div class="brand">
            <div class="brand-mark">{{ config('gentelella.brand_initial', 'G') }}</div>
            <div class="brand-text">
                <h1>{{ \App\Models\Setting::get('center.name', config('gentelella.brand_name', 'GCRC')) }}</h1>
                <div class="subtitle">
                    {{ \App\Models\Setting::get('center.address', '') }}
                    @if (\App\Models\Setting::get('center.phone', ''))
                        · {{ \App\Models\Setting::get('center.phone') }}
                    @endif
                </div>
            </div>
        </div>
        <div class="meta">
            <div><strong>Generated</strong></div>
            <div>{{ now()->format('d M Y, H:i') }}</div>
            <div style="margin-top: 4px;">
                <strong>By:</strong> {{ auth()->user()->name ?? 'System' }}
            </div>
        </div>
    </div>

    {{-- Document title --}}
    <div class="doc-title">
        <h2>@yield('document_title', 'Document')</h2>
        @hasSection('document_subtitle')
            <div class="pretitle">@yield('document_subtitle')</div>
        @endif
    </div>

    {{-- Content --}}
    @yield('content')

    {{-- Footer --}}
    <div class="print-footer">
        <div>
            {{ config('gentelella.brand_name', 'GCRC') }} —
            {{ config('gentelella.title', 'Management Information System') }}
        </div>
        <div>
            Document generated on {{ now()->format('d M Y \a\t H:i') }}
        </div>
    </div>
</body>
</html>