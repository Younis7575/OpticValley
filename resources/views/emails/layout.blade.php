@php
    $store = setting('store_name', 'OpticValley');
    $gold = '#b8915a'; $ink = '#13171c'; $muted = '#6b7178'; $line = '#ece6db'; $paper = '#f6f3ee';
@endphp
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="x-apple-disable-message-reformatting">
<meta name="color-scheme" content="light only"><meta name="supported-color-schemes" content="light">
<title>{{ $title ?? $store }}</title>
<style>
  @media only screen and (max-width:620px){ .container{width:100%!important} .px{padding-left:22px!important;padding-right:22px!important} .stack{display:block!important;width:100%!important} .hide-sm{display:none!important} }
  a{color:{{ $ink }}}
</style>
</head>
<body style="margin:0;padding:0;background:{{ $paper }};font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;color:{{ $ink }};-webkit-font-smoothing:antialiased;">
@isset($preheader)<div style="display:none;max-height:0;overflow:hidden;opacity:0;">{{ $preheader }}&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;</div>@endisset
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:{{ $paper }};">
<tr><td align="center" style="padding:32px 12px;">
  <table role="presentation" class="container" width="600" cellpadding="0" cellspacing="0" style="width:600px;max-width:600px;">
    <tr><td align="center" style="padding:6px 0 26px;">
      <a href="{{ url('/') }}" style="text-decoration:none;"><img src="{{ asset('images/logo.png') }}" width="190" alt="{{ $store }}" style="display:block;width:190px;height:auto;border:0;"></a>
    </td></tr>
    <tr><td style="background:#ffffff;border-radius:18px;border:1px solid {{ $line }};overflow:hidden;">
      <div style="height:4px;background:{{ $gold }};background:linear-gradient(90deg,#d8b47e,{{ $gold }},#8a6a35);border-radius:18px 18px 0 0;"></div>
      {!! $slot !!}
    </td></tr>
    <tr><td align="center" class="px" style="padding:28px 36px 8px;font-size:12px;line-height:20px;color:{{ $muted }};">
      <p style="margin:0 0 10px;">
        <a href="{{ url('/shop') }}" style="color:{{ $ink }};text-decoration:none;font-weight:bold;">Shop</a> &nbsp;·&nbsp;
        <a href="{{ route('track') }}" style="color:{{ $ink }};text-decoration:none;font-weight:bold;">Track order</a> &nbsp;·&nbsp;
        <a href="{{ route('contact') }}" style="color:{{ $ink }};text-decoration:none;font-weight:bold;">Contact</a>
        @if (setting('whatsapp_number')) &nbsp;·&nbsp; <a href="https://wa.me/{{ preg_replace('/\D/', '', setting('whatsapp_number')) }}" style="color:{{ $ink }};text-decoration:none;font-weight:bold;">WhatsApp</a>@endif
      </p>
      <p style="margin:0;">{{ setting('store_address') }}<br>{{ setting('store_phone') }} · {{ setting('store_email') }}</p>
      <p style="margin:12px 0 0;color:#a0a4a9;">© {{ date('Y') }} {{ $store }}. All rights reserved.</p>
    </td></tr>
  </table>
</td></tr>
</table>
</body>
</html>
