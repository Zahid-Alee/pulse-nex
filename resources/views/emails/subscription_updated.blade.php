<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Subscription Details</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { padding: 25px; border: 1px solid #e2e8f0; border-radius: 8px; max-width: 600px; margin: auto; }
        .header { font-size: 24px; font-weight: bold; }
        .plan-box { background-color: #f7fafc; border: 1px solid #e2e8f0; padding: 20px; margin-top: 20px; border-radius: 8px; }
        ul { list-style: none; padding: 0; }
        li { padding-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="header">Hi, {{ $subscription->user->name }}!</h1>
        <p>
            Your subscription plan has been successfully updated. Here are the details of your current plan:
        </p>

        <div class="plan-box">
            <h2>Your Plan: {{ $subscription->plan_name }}</h2>
            <ul>
                <li><strong>💰 Amount:</strong> ${{ number_format($subscription->amount, 2) }}/month</li>
                <li><strong>🖥️ Monitors Limit:</strong> {{ $subscription->monitors_limit }}</li>
                <li><strong>⏱️ Check Interval:</strong> Every {{ $subscription->check_interval }} minutes</li>
                <li><strong>✅ Plan Active Until:</strong> {{ $subscription->ends_at->format('F d, Y') }}</li>
            </ul>
        </div>

        <p style="margin-top: 25px;">
            Thanks,<br>
            The {{ config('app.name') }} Team
        </p>
    </div>
</body>
</html>