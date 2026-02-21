<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resetowanie hasła - MasterUrCode</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo span {
            background-color: #00D9FF;
            color: #0a0f1c;
            padding: 10px 15px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 18px;
        }
        h1 {
            color: #1a1a1a;
            font-size: 24px;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            background-color: #00D9FF;
            color: #0a0f1c !important;
            text-decoration: none;
            padding: 14px 30px;
            border-radius: 25px;
            font-weight: bold;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #00c4e6;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #999;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <span>MasterUrCode</span>
        </div>
        
        <h1>Resetowanie hasła</h1>
        
        <p>Cześć{{ isset($user->profile) && $user->profile->nickname ? ', ' . $user->profile->nickname : '' }}!</p>
        
        <p>Otrzymaliśmy prośbę o zresetowanie hasła do Twojego konta. Kliknij przycisk poniżej, aby ustawić nowe hasło:</p>
        
        <p style="text-align: center;">
            <a href="{{ $resetUrl }}" class="button">Zresetuj hasło</a>
        </p>
        
        <div class="warning">
            ⚠️ Ten link jest ważny przez 5 minut. Jeśli nie prosiłeś o reset hasła, zignoruj tę wiadomość.
        </div>
        
        <p>Jeśli przycisk nie działa, skopiuj i wklej poniższy link do przeglądarki:</p>
        <p style="word-break: break-all; font-size: 12px; color: #0066cc;">{{ $resetUrl }}</p>
        
        <div class="footer">
            <p>Ta wiadomość została wysłana automatycznie przez system MasterUrCode.</p>
            <p>© {{ date('Y') }} MasterUrCode. Wszystkie prawa zastrzeżone.</p>
        </div>
    </div>
</body>
</html>
