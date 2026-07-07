<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A new appointment has been booked</title>
</head>
<body style="margin:0; padding:0; background-color:#f3f4f6; font-family:ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6; padding:24px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%;">

                    {{-- Page title --}}
                    <tr>
                        <td style="padding:0 4px 20px;">
                            <p style="margin:0 0 4px; font-size:10px; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:#37b44a;">
                                New Appointment
                            </p>
                            <h1 style="margin:0; font-size:22px; font-weight:700; color:#575876;">
                                A new appointment has been booked
                            </h1>
                        </td>
                    </tr>

                    {{-- Card --}}
                    <tr>
                        <td style="background-color:#ffffff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">

                                {{-- Green accent bar --}}
                                <tr>
                                    <td style="height:4px; background-color:#37b44a; font-size:0; line-height:0;">&nbsp;</td>
                                </tr>

                                {{-- Reference --}}
                                <tr>
                                    <td style="padding:24px 24px 16px;">
                                        <p style="margin:0 0 4px; font-size:11px; font-weight:600; letter-spacing:1px; text-transform:uppercase; color:#6b7280;">
                                            Reference
                                        </p>
                                        <p style="margin:0; font-size:24px; font-weight:700; letter-spacing:2px; color:#111827;">
                                            {{ $referenceNumber }}
                                        </p>
                                    </td>
                                </tr>

                                {{-- Summary rows --}}
                                <tr>
                                    <td style="padding:0 24px 24px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb; border-radius:8px; background-color:#f9fafb;">
                                            <tr>
                                                <td style="padding:12px 16px; border-bottom:1px solid #e5e7eb;">
                                                    <span style="display:block; font-size:12px; font-weight:500; color:#6b7280;">Location</span>
                                                    <span style="display:block; margin-top:2px; font-size:14px; font-weight:600; color:#111827;">{{ $locationName }}</span>
                                                    <span style="display:block; margin-top:2px; font-size:12px; color:#6b7280;">{{ $locationAddress }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:12px 16px; border-bottom:1px solid #e5e7eb;">
                                                    <span style="display:block; font-size:12px; font-weight:500; color:#6b7280;">Date</span>
                                                    <span style="display:block; margin-top:2px; font-size:14px; color:#111827;">{{ $scheduledDate }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:12px 16px; border-bottom:1px solid #e5e7eb;">
                                                    <span style="display:block; font-size:12px; font-weight:500; color:#6b7280;">Time</span>
                                                    <span style="display:block; margin-top:2px; font-size:14px; color:#111827;">{{ $scheduledTime }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:12px 16px; border-bottom:1px solid #e5e7eb;">
                                                    <span style="display:block; font-size:12px; font-weight:500; color:#6b7280;">PO Number(s)</span>
                                                    <span style="display:block; margin-top:2px; font-size:14px; color:#111827;">{{ implode(', ', $purchaseOrders) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:12px 16px; border-bottom:1px solid #e5e7eb;">
                                                    <span style="display:block; font-size:12px; font-weight:500; color:#6b7280;">Driver</span>
                                                    <span style="display:block; margin-top:2px; font-size:14px; color:#111827;">{{ $driversName }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:12px 16px;">
                                                    <span style="display:block; font-size:12px; font-weight:500; color:#6b7280;">Driver's Phone Number</span>
                                                    <span style="display:block; margin-top:2px; font-size:14px; color:#111827;">{{ $driversPhone }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:16px 4px 0;" align="center">
                            <p style="margin:0; font-size:11px; color:#9ca3af;">
                                This is an automated notification from the {{ config('app.name') }} scheduling system.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
