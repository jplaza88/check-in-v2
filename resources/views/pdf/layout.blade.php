@php
    use App\Pdf\PdfAssets;

    $font = PdfAssets::fontDataUri();
    $logo = PdfAssets::logoDataUri();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    {{-- A document is always light; never inherit the viewer's dark mode. --}}
    <meta name="color-scheme" content="light">
    <title>{{ $title }} {{ $record['referenceNumber'] }}</title>
    <style>
        @if ($font !== '')
        @font-face {
            font-family: 'Figtree';
            src: url({{ $font }}) format('woff2');
            font-weight: 300 900;
            font-style: normal;
        }
        @endif

        @page {
            size: letter;
            margin: 14mm;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Figtree', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            font-size: 12px;
            line-height: 1.45;
            color: #111827;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .masthead {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 24px;
        }

        .masthead img { height: 34px; }

        .wordmark {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #575876;
        }

        .site {
            text-align: right;
            font-size: 11px;
            color: #6b7280;
        }

        .site strong {
            display: block;
            font-size: 12px;
            color: #575876;
        }

        /* Echoes the 4px green rule on the confirmation email. */
        .rule {
            height: 4px;
            background: #37b44a;
            border-radius: 2px;
            margin: 12px 0 18px;
        }

        .eyebrow-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .eyebrow {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #37b44a;
        }

        .status {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 999px;
            background: #f3f4f6;
            color: #6b7280;
        }

        .status--completed,
        .status--checked-in { background: rgba(55, 180, 74, 0.12); color: #2b8f3b; }
        .status--pending    { background: rgba(245, 158, 11, 0.14); color: #92620a; }
        .status--scheduled  { background: rgba(59, 130, 246, 0.12); color: #1d4ed8; }
        .status--cancelled  { background: #f3f4f6; color: #6b7280; }
        .status--no-show    { background: rgba(244, 63, 94, 0.12); color: #be123c; }

        .hero {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 24px;
            margin-top: 10px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e5e7eb;
        }

        .hero-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #6b7280;
        }

        .hero-reference {
            font-size: 30px;
            font-weight: 700;
            letter-spacing: 3px;
            line-height: 1.1;
            color: #111827;
        }

        .barcode { text-align: right; }
        .barcode svg { height: 46px; width: auto; }

        .barcode-caption {
            margin-top: 2px;
            font-size: 9px;
            letter-spacing: 2px;
            color: #9ca3af;
        }

        .sections {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0 28px;
            margin-top: 18px;
        }

        .section { break-inside: avoid; margin-bottom: 18px; }

        .section h2 {
            margin: 0 0 6px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1.6px;
            text-transform: uppercase;
            color: #37b44a;
        }

        .section dl {
            margin: 0;
            border-top: 1px solid #e5e7eb;
        }

        .row {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 12px;
            padding: 5px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .row dt {
            flex: 0 0 auto;
            font-size: 10px;
            color: #6b7280;
        }

        .row dd {
            margin: 0;
            text-align: right;
            font-size: 11.5px;
            font-weight: 600;
            color: #111827;
            word-break: break-word;
        }

        .row dd.mono { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; }

        /* Purchase orders are unbounded, so they wrap as chips rather than
           overflowing a single comma-joined line. */
        .chips {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            justify-content: flex-end;
        }

        .chip {
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 999px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
        }

        .note {
            grid-column: 1 / -1;
            margin: 0 0 18px;
            padding: 10px 12px;
            border-radius: 8px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            font-size: 11px;
            color: #374151;
        }

        .note strong {
            display: block;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1.6px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .footer {
            margin-top: 4px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #9ca3af;
        }

        .footer .contact { color: #6b7280; }
    </style>
</head>
<body>
    <div class="masthead">
        @if ($logo !== '')
            <img src="{{ $logo }}" alt="{{ config('app.name') }}">
        @else
            <span class="wordmark">{{ config('app.name') }}</span>
        @endif

        <div class="site">
            <strong>{{ $record['locationName'] }}</strong>
            {{ $record['locationAddress'] }}
        </div>
    </div>

    <div class="rule"></div>

    <div class="eyebrow-row">
        <span class="eyebrow">{{ $title }}</span>
        <span class="status status--{{ $record['status'] }}">{{ $statusLabel }}</span>
    </div>

    <div class="hero">
        <div>
            <div class="hero-label">{{ __('messages.accountHistoryRecord.reference') }}</div>
            <div class="hero-reference">{{ $record['referenceNumber'] }}</div>
        </div>

        <div class="barcode">
            {!! $barcode !!}
            <div class="barcode-caption">{{ $record['referenceNumber'] }}</div>
        </div>
    </div>

    <div class="sections">
        @yield('sections')
    </div>

    <div class="footer">
        @if (filled($record['locationPhone']) || filled($record['locationEmail']))
            <div class="contact">
                {{ __('messages.accountHistoryRecord.questions') }}
                @if (filled($record['locationPhone']))
                    {{ $record['locationPhone'] }}@if (filled($record['locationPhoneExt'])) {{ __('messages.accountHistoryRecord.extension') }} {{ $record['locationPhoneExt'] }}@endif
                @endif
                @if (filled($record['locationPhone']) && filled($record['locationEmail'])) &middot; @endif
                {{ $record['locationEmail'] }}
            </div>
        @endif

        <div>
            {{ __('messages.accountHistoryRecord.generatedOn', ['date' => $generatedOn]) }}
            &middot;
            {{ __('messages.accountHistoryRecord.notABillOfLading') }}
        </div>
    </div>
</body>
</html>
