@component('mail::message')
# 🚨 Website Downtime Alert

Hello {{ $website->user->name }},

We detected that your website **{{ $website->name }}** ({{ $website->url }}) is **DOWN**.

**Error:** {{ $errorMessage ?? 'Unknown error' }}  
**Checked at:** {{ now()->format('Y-m-d H:i:s') }}

@component('mail::button', ['url' => $website->url])
Visit Website
@endcomponent

Thanks,  
The {{ config('app.name') }} Team
@endcomponent
