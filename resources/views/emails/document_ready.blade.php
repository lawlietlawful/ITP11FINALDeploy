<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Document is Ready</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }
        .header {
            background-color: #0ea5e9;
            color: #ffffff;
            text-align: center;
            padding: 24px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 32px;
        }
        .content p {
            margin-bottom: 16px;
            font-size: 16px;
        }
        .details-box {
            background-color: #f1f5f9;
            border-radius: 8px;
            padding: 20px;
            margin-top: 24px;
            margin-bottom: 24px;
        }
        .details-row {
            margin-bottom: 10px;
        }
        .details-row:last-child {
            margin-bottom: 0;
        }
        .details-label {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .details-value {
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
        }
        .button-container {
            text-align: center;
            margin-top: 32px;
        }
        .btn {
            display: inline-block;
            background-color: #0ea5e9;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 13px;
            color: #94a3b8;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>VistáBarangay</h1>
        </div>
        
        <div class="content">
            <p>Hello <strong>{{ $request_item->resident->first_name }}</strong>,</p>
            <p>Great news! The document you requested is now <strong>printed and ready for pickup</strong> at the Barangay Hall.</p>
            
            <div class="details-box">
                <div class="details-row">
                    <div class="details-label">Document Type</div>
                    <div class="details-value">{{ $request_item->documentType->name }}</div>
                </div>
                <div class="details-row" style="margin-top: 16px;">
                    <div class="details-label">Tracking Code</div>
                    <div class="details-value" style="font-family: monospace;">{{ $request_item->tracking_code }}</div>
                </div>
            </div>

            <p><strong>What to bring:</strong></p>
            <ul style="padding-left: 20px; margin-bottom: 24px; color: #475569;">
                <li>A valid ID (for identity verification)</li>
                <li>Your tracking code</li>
                <li>Any exact change for processing fees (if applicable)</li>
            </ul>

            <div class="button-container">
                <a href="{{ route('public.track', ['tracking_code' => $request_item->tracking_code]) }}" class="btn">Track Your Request Online</a>
            </div>
        </div>
        
        <div class="footer">
            <p>This is an automated notification from the VistáBarangay System.<br>Please do not reply directly to this email.</p>
        </div>
    </div>
</body>
</html>
