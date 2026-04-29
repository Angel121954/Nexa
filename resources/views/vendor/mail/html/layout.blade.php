<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>{{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #F9FAFB;
            color: #111827;
            line-height: 1.6;
        }
        
        .wrapper {
            background-color: #F9FAFB;
            padding: 40px 20px;
        }
        
        .content {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .header {
            background: linear-gradient(135deg, #E8375A 0%, #C92E4B 100%);
            padding: 30px 40px;
            text-align: center;
            border-radius: 16px 16px 0 0;
        }
        
        .header img {
            width: 120px;
            height: auto;
        }
        
        .body {
            background: #FFFFFF;
            border-left: 1px solid #E5E7EB;
            border-right: 1px solid #E5E7EB;
            padding: 40px;
        }
        
        .inner-body {
            max-width: 570px;
            margin: 0 auto;
        }
        
        .content-cell {
            font-size: 16px;
            line-height: 1.6;
            color: #374151;
        }
        
        .footer {
            background: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-top: none;
            border-radius: 0 0 16px 16px;
            padding: 30px 40px;
            text-align: center;
        }
        
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #E8375A 0%, #C92E4B 100%);
            color: #FFFFFF !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            box-shadow: 0 4px 12px rgba(232, 55, 90, 0.3);
        }
        
        .button:hover {
            box-shadow: 0 6px 16px rgba(232, 55, 90, 0.4);
        }
        
        h1 {
            color: #111827;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 16px;
        }
        
        p {
            margin-bottom: 16px;
        }
        
        .divider {
            height: 1px;
            background: #E5E7EB;
            margin: 30px 0;
        }
        
        .footer p {
            font-size: 14px;
            color: #9CA3AF;
            margin-bottom: 8px;
        }
        
        .footer a {
            color: #E8375A;
            text-decoration: none;
        }
        
        @media only screen and (max-width: 600px) {
            .wrapper { padding: 20px 10px; }
            .header { padding: 20px; }
            .body { padding: 30px 20px; }
            .footer { padding: 20px; }
        }
    </style>
    {!! $head ?? '' !!}
</head>
<body>
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    {!! $header ?? '' !!}
                    
                    <!-- Email Body -->
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0">
                            <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                                <!-- Body content -->
                                <tr>
                                    <td class="content-cell">
                                        {!! Illuminate\Mail\Markdown::parse($slot) !!}
                                        {!! $subcopy ?? '' !!}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    {!! $footer ?? '' !!}
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
