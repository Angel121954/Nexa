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
            background-color: #F3F4F6;
            color: #111827;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }
        
        .wrapper {
            background-color: #F3F4F6;
            padding: 48px 20px;
        }
        
        .content {
            max-width: 560px;
            margin: 0 auto;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 20px rgba(0,0,0,0.04);
            border-radius: 20px;
        }
        
        .header {
            padding: 36px 40px;
            text-align: center;
            border-radius: 20px 20px 0 0;
        }
        
        .header img {
            width: 120px;
            height: auto;
        }
        
        .body {
            background: #FFFFFF;
            padding: 40px;
        }
        
        .inner-body {
            max-width: 100%;
            margin: 0 auto;
        }
        
        .content-cell {
            font-size: 16px;
            line-height: 1.6;
            color: #374151;
        }
        
        .footer {
            background: #FFFFFF;
            border-top: 1px solid #F3F4F6;
            padding: 28px 40px;
            text-align: center;
            border-radius: 0 0 20px 20px;
        }
        
        .footer p {
            font-size: 13px;
            color: #9CA3AF;
        }
        
        .footer a {
            color: #E8375A;
            text-decoration: none;
            font-weight: 600;
        }
        
        @media only screen and (max-width: 600px) {
            .wrapper { padding: 24px 12px; }
            .header { padding: 24px 20px; }
            .body { padding: 28px 20px; }
            .footer { padding: 24px 20px; }
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
