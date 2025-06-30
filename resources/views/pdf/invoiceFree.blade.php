<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ticket Invoice</title>
    <style type="text/css">
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f8f8f8;
            color: #222;
            margin: 0;
            padding: 0;
        }
        .invoice-container {
            background: #fff;
            width: 600px;
            margin: 40px auto;
            border: 1px solid #ddd;
            padding: 32px 24px;
        }
        .header {
            border-bottom: 1px solid #eee;
            margin-bottom: 24px;
            padding-bottom: 16px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 8px 0;
            font-size: 2em;
            color: #2c3e50;
        }
        .header p {
            margin: 0;
            color: #555;
        }
        .info-row {
            margin-bottom: 14px;
            /* Removed display: flex and justify-content: space-between for CSS 2.1 compatibility */
        }
        .info-row span {
            display: inline-block;
            min-width: 150px;
            vertical-align: top;
        }
        .info-row span:first-child {
            font-weight: bold;
            color: #333;
        }
        .qr-section {
            margin: 24px 20px;
            text-align: center;
        }
        .qr-section img {
            border: 1px solid #ccc;
            padding: 6px;
            background: #fafafa;
            margin: 4px;
            width: 120px;
            height: 120px;
        }
        .footer {
            border-top: 1px solid #eee;
            margin-top: 24px;
            padding-top: 16px;
            font-size: 0.95em;
            color: #555;
            text-align: center;
        }
        a {
            color: #2980b9;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="invoice-container">
    <div class="header">
        <h1>Event Ticket</h1>
        <p>Thank you for Booking!</p>
        {{-- <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-24 h-24">  --}}
    </div>
    <div class="info-row">
        <span>Ticket ID:</span>
        <span>{{$ticket->id}}</span>
    </div>
    <div class="info-row">
        <span>Booking Date:</span>
        <span>{{$ticket->created_at}}</span>
    </div>
    <div class="info-row">
        <span>Event Name:</span>
        <span>{{$ticket->event?->name}}</span>
    </div>
    <div class="info-row">
        <span>Event Date and Time:</span>
        <span>{{$ticket->event?->date}}</span>
    </div>
    <div class="info-row">
        <span>Ticket Type:</span>
        <span>Free</span>
    </div>
    <div class="qr-section">
        <img src="{{ $qrCode }}" alt="QR Code for Ticket ID: {{$ticket->id}}">
    </div>
    <div class="footer">
        <p>Please present this ticket at the event for entry.</p>
    </div>
</div>
</body>
</html>
