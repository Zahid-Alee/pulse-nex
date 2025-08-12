@include('includes.header')

<main class="payment-section">
    <div class="payment-container">
        <!-- Left Column: Plan Details -->
        <div class="plan-details">
            <h2 class="plan-title">{{ $planName }}</h2>
            <p class="plan-price">${{ number_format($amount, 2) }} <span>/month</span></p>
            <ul class="plan-features">
                @if ($planName === 'Free')
                    <li><i class="checkmark">✓</i>Up to 5 websites</li>
                    <li><i class="checkmark">✓</i>Check every 5 minutes</li>
                    <li><i class="checkmark">✓</i>Uptime/Downtime Monitoring</li>
                    <li><i class="checkmark">✓</i>Email Alerts</li>
                    <li><i class="checkmark">✓</i>Uptime Reports (7 days)</li>
                @elseif($planName === 'Pro')
                    <li><i class="checkmark">✓</i>Up to 15 websites</li>
                    <li><i class="checkmark">✓</i>Check every 3 minutes</li>
                    <li><i class="checkmark">✓</i>Uptime/Downtime Monitoring</li>
                    <li><i class="checkmark">✓</i>Email Alerts</li>
                    <li><i class="checkmark">✓</i>Uptime Reports (30 days)</li>
                @elseif($planName === 'Business')
                    <li><i class="checkmark">✓</i>Up to 50 websites</li>
                    <li><i class="checkmark">✓</i>Check every 1 minute</li>
                    <li><i class="checkmark">✓</i>Uptime/Downtime Monitoring</li>
                    <li><i class="checkmark">✓</i>Email Alerts</li>
                    <li><i class="checkmark">✓</i>Uptime Reports (30 days)</li>
                @endif
            </ul>
        </div>

        <div class="payment-card">
            <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">
                Complete Payment
            </h2>
            <form id="payment-form">
                <div id="payment-element"></div>
                <button id="submit" class="paynow-btn">Pay Now</button>
            </form>
        </div>
    </div>
</main>

@include('includes.footer')

<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe("{{ config('services.stripe.key') }}");

    const options = {
        clientSecret: "{{ $clientSecret }}",
        appearance: {
            theme: 'stripe'
        }
    };

    const elements = stripe.elements(options);
    const paymentElement = elements.create("payment");
    paymentElement.mount("#payment-element");

    // Add animation to features on load
    document.addEventListener('DOMContentLoaded', function() {
        const features = document.querySelectorAll('.plan-features li');
        features.forEach((feature, index) => {
            setTimeout(() => {
                feature.classList.add('animate-in');
            }, index * 150);
        });
    });

    document.getElementById("payment-form").addEventListener("submit", async (e) => {
        e.preventDefault();
        const submitBtn = document.getElementById("submit");
        submitBtn.classList.add('loading');

        const {
            error
        } = await stripe.confirmPayment({
            elements,
            confirmParams: {
                return_url: "{{ route('payment.success') }}"
            }
        });

        if (error) {
            submitBtn.classList.remove('loading');
            alert(error.message);
        }
    });
</script>

<style>
    /* Base styles */
    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        margin: 0;
        padding: 0;
    }

    /* Two-column layout */
    .payment-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        max-width: 1000px;
        margin: 50px auto;
        padding: 20px;
        animation: slideUp 0.6s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Left: Plan details */
    .plan-details {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: 32px;
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }

    .plan-details:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    }

    .plan-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 16px;
        color: #1e293b;
        text-align: center;
    }

    .plan-price {
        font-size: 2.5rem;
        font-weight: 800;
        color: #3b82f6;
        margin-bottom: 2rem;
        text-align: center;
        position: relative;
    }

    .plan-price span {
        font-size: 1rem;
        color: #64748b;
        font-weight: 500;
    }

    .plan-features {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .plan-features li {
        display: flex;
        align-items: center;
        padding: 14px 0;
        border-bottom: 1px solid rgba(241, 245, 249, 0.8);
        color: #374151;
        font-weight: 500;
        opacity: 0;
        transform: translateX(-20px);
        transition: all 0.4s ease;
    }

    .plan-features li:last-child {
        border-bottom: none;
    }

    .plan-features li.animate-in {
        opacity: 1;
        transform: translateX(0);
    }

    .checkmark {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
        margin-right: 12px;
        flex-shrink: 0;
        animation: checkPop 0.5s ease-out;
    }

    @keyframes checkPop {
        0% {
            transform: scale(0);
        }

        50% {
            transform: scale(1.2);
        }

        100% {
            transform: scale(1);
        }
    }

    /* Right: Payment card */
    .payment-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: 32px;
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }

    .payment-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    }

    .payment-card h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 24px;
        color: #1e293b;
        text-align: center;
        padding-bottom: 16px;
        border-bottom: 2px solid #f1f5f9;
    }

    #payment-element {
        margin-bottom: 24px;
    }

    /* Pay Now Button */
    .paynow-btn {
        width: 100%;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        padding: 16px 24px;
        font-size: 16px;
        font-weight: 600;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .paynow-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
    }

    .paynow-btn:active {
        transform: translateY(0);
    }

    .paynow-btn.loading {
        background: #9ca3af;
        cursor: not-allowed;
    }

    .paynow-btn.loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin: -10px 0 0 -10px;
        border: 2px solid transparent;
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Dark mode support */
    .dark .plan-details,
    .dark .payment-card {
        background: rgba(31, 41, 55, 0.95);
        color: white;
        border: 1px solid rgba(75, 85, 99, 0.3);
    }

    .dark .plan-title,
    .dark .payment-card h2 {
        color: white;
    }

    .dark .plan-features li {
        color: #e5e7eb;
        border-bottom-color: rgba(75, 85, 99, 0.3);
    }

    .dark .plan-price span {
        color: #9ca3af;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .payment-container {
            grid-template-columns: 1fr;
            gap: 1.5rem;
            margin: 20px auto;
            padding: 15px;
        }

        .plan-details,
        .payment-card {
            padding: 24px;
        }

        .plan-title {
            font-size: 1.5rem;
        }

        .plan-price {
            font-size: 2rem;
        }

        .payment-card h2 {
            font-size: 1.25rem;
        }
    }
</style>
