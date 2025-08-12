@php
    $page_title = 'Pricing - PulseNex';
    $active_page = 'pricing';

    $plans = [
        [
            'name' => 'Free',
            'slug' => 'Free',
            'price' => 0,
            'interval' => '/month',
            'info' => 'Up to 5 websites · Every 5 minutes',
            'monitors_limit' => 5,
            'check_interval' => 5,
            'features' => ['Uptime/Downtime Monitoring', 'Email Alerts', 'Uptime Reports (last 7 days)'],
            'popular' => false,
        ],
        [
            'name' => 'Pro',
            'price' => 5,
            'slug' => 'Pro',
            'interval' => '/month',
            'info' => 'Up to 15 websites · Every 3 minutes',
            'monitors_limit' => 15,
            'check_interval' => 3,
            'features' => ['Uptime/Downtime Monitoring', 'Email Alerts', 'Uptime Reports (last 30 days)', 'Priority Support'],
            'popular' => true,
        ],
        [
            'name' => 'Business',
            'slug' => 'Business',
            'price' => 15,
            'interval' => '/month',
            'info' => 'Up to 50 websites · Every 1 minute',
            'monitors_limit' => 50,
            'check_interval' => 1,
            'features' => ['Uptime/Downtime Monitoring', 'Email Alerts', 'Uptime Reports (last 30 days)', 'Priority Support', 'Advanced Analytics'],
            'popular' => false,
        ],
    ];

    // Get current user's subscription
    $userSubscription = Auth::check() ? Auth::user()->subscription : null;
    $currentPlan = $userSubscription ? $userSubscription->plan_name : null;
@endphp

@include('includes.header')

<main class="pricing-section">
    <section class="pricing-hero">
        <div class="container">
            <h1>Choose Your Perfect Plan</h1>
            <p>Select the plan that suits your website monitoring needs. Upgrade or downgrade anytime.</p>
        </div>
    </section>

    <div class="pricing-container">
        <div class="pricing-grid">
            @foreach ($plans as $index => $plan)
                @php
                    $isCurrentPlan = $currentPlan === $plan['slug'];
                    $currentPlanIndex = $currentPlan ? array_search($currentPlan, array_column($plans, 'slug')) : -1;
                    $isUpgrade = Auth::check() && $currentPlanIndex !== false && $currentPlanIndex < $index;
                    $isDowngrade = Auth::check() && $currentPlanIndex !== false && $currentPlanIndex > $index;
                @endphp

                <div class="pricing-card {{ $plan['popular'] ? 'popular' : '' }} {{ $isCurrentPlan ? 'current-plan' : '' }}">
                    @if($plan['popular'] && !$isCurrentPlan)
                        <div class="popular-badge">Most Popular</div>
                    @endif
                    
                    @if($isCurrentPlan)
                        <div class="current-badge">Current Plan</div>
                    @endif

                    <div class="plan-header">
                        <h3 class="plan-title">{{ $plan['name'] }}</h3>
                        <div class="plan-price">
                            <span class="currency">$</span>
                            <span class="amount">{{ $plan['price'] }}</span>
                            <span class="interval">{{ $plan['interval'] }}</span>
                        </div>
                        <p class="plan-info">{{ $plan['info'] }}</p>
                    </div>

                    <ul class="features-list">
                        @foreach ($plan['features'] as $feature)
                            <li>
                                <svg class="feature-icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>

                    <div class="plan-action">
                        @auth
                            @if ($isCurrentPlan)
                                <button class="plan-btn current-plan-btn" disabled>
                                    <svg class="btn-icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    Active Plan
                                </button>
                            @elseif($isUpgrade)
                                <form action="{{ route('payment.create') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="plan_name" value="{{ $plan['name'] }}">
                                    <input type="hidden" name="amount" value="{{ $plan['price'] }}">
                                    <input type="hidden" name="monitors_limit" value="{{ $plan['monitors_limit'] }}">
                                    <input type="hidden" name="check_interval" value="{{ $plan['check_interval'] }}">
                                    <button type="submit" class="plan-btn upgrade-btn">
                                        <svg class="btn-icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Upgrade to {{ $plan['name'] }}
                                    </button>
                                </form>
                            @elseif($isDowngrade)
                                <form action="{{ route('payment.create') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="plan_name" value="{{ $plan['name'] }}">
                                    <input type="hidden" name="amount" value="{{ $plan['price'] }}">
                                    <input type="hidden" name="monitors_limit" value="{{ $plan['monitors_limit'] }}">
                                    <input type="hidden" name="check_interval" value="{{ $plan['check_interval'] }}">
                                    <button type="submit" class="plan-btn downgrade-btn">
                                        <svg class="btn-icon" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Switch to {{ $plan['name'] }}
                                    </button>
                                </form>
                            @else
                                {{-- No current plan, first purchase --}}
                                <form action="{{ route('payment.create') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="plan_name" value="{{ $plan['name'] }}">
                                    <input type="hidden" name="amount" value="{{ $plan['price'] }}">
                                    <input type="hidden" name="monitors_limit" value="{{ $plan['monitors_limit'] }}">
                                    <input type="hidden" name="check_interval" value="{{ $plan['check_interval'] }}">
                                    <button type="submit" class="plan-btn get-started-btn">
                                        Get Started
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('register') }}" class="plan-btn signup-btn">
                                Sign Up to Get Started
                            </a>
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</main>

@include('includes.footer')

<style>
    /* CSS Variables */
    :root {
        --primary-color: #3b82f6;
        --primary-hover: #2563eb;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        --white: #ffffff;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    }

    /* Pricing Hero */
    .pricing-hero {
        background: linear-gradient(135deg, var(--primary-color) 0%, #1e40af 100%);
        color: var(--white);
        padding: 6rem 0 4rem;
        text-align: center;
    }

    .pricing-hero h1 {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 1rem;
        letter-spacing: -0.025em;
    }

    .pricing-hero p {
        font-size: 1.25rem;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
    }

    /* Pricing Container */
    .pricing-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    /* Pricing Grid */
    .pricing-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 2rem;
        margin: -3rem auto 4rem;
        position: relative;
        z-index: 10;
    }

    /* Pricing Cards */
    .pricing-card {
        background: var(--white);
        border-radius: 1rem;
        box-shadow: var(--shadow-xl);
        border: 1px solid var(--gray-200);
        display: flex;
        flex-direction: column;
        padding: 2rem;
        position: relative;
        transition: all 0.3s ease;
        transform: translateY(0);
    }

    .pricing-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.25);
    }

    /* Popular Plan */
    .pricing-card.popular {
        border: 2px solid var(--primary-color);
        transform: translateY(-1rem);
        box-shadow: 0 25px 50px -12px rgb(59 130 246 / 0.25);
    }

    .pricing-card.popular:hover {
        transform: translateY(-1.5rem);
    }

    /* Current Plan */
    .pricing-card.current-plan {
        border: 2px solid var(--success-color);
        background: linear-gradient(to bottom, #f0fdf4, var(--white));
    }

    /* Badges */
    .popular-badge, .current-badge {
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        padding: 0.5rem 1.5rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--white);
    }

    .popular-badge {
        background: linear-gradient(135deg, var(--primary-color), #1e40af);
    }

    .current-badge {
        background: linear-gradient(135deg, var(--success-color), #059669);
    }

    /* Plan Header */
    .plan-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .plan-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 1rem;
    }

    .plan-price {
        display: flex;
        align-items: baseline;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .currency {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-600);
    }

    .amount {
        font-size: 3rem;
        font-weight: 800;
        color: var(--primary-color);
        margin: 0 0.25rem;
    }

    .interval {
        font-size: 1rem;
        color: var(--gray-500);
    }

    .plan-info {
        color: var(--gray-600);
        font-size: 0.95rem;
        line-height: 1.5;
    }

    /* Features List */
    .features-list {
        list-style: none;
        padding: 0;
        margin: 0 0 2rem;
        flex-grow: 1;
    }

    .features-list li {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
        color: var(--gray-700);
        font-size: 0.95rem;
    }

    .feature-icon {
        width: 1.25rem;
        height: 1.25rem;
        color: var(--success-color);
        margin-right: 0.75rem;
        flex-shrink: 0;
    }

    /* Plan Action Buttons */
    .plan-action {
        margin-top: auto;
    }

    .plan-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 0.875rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        gap: 0.5rem;
    }

    .btn-icon {
        width: 1rem;
        height: 1rem;
    }

    /* Button Variants */
    .get-started-btn, .signup-btn {
        background: var(--primary-color);
        color: var(--white);
    }

    .get-started-btn:hover, .signup-btn:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
        box-shadow: var(--shadow-lg);
    }

    .upgrade-btn {
        background: linear-gradient(135deg, var(--success-color), #059669);
        color: var(--white);
    }

    .upgrade-btn:hover {
        background: linear-gradient(135deg, #059669, #047857);
        transform: translateY(-1px);
        box-shadow: var(--shadow-lg);
    }

    .downgrade-btn {
        background: var(--gray-100);
        color: var(--gray-700);
        border: 2px solid var(--gray-200);
    }

    .downgrade-btn:hover {
        background: var(--gray-200);
        transform: translateY(-1px);
    }

    .current-plan-btn {
        background: var(--success-color);
        color: var(--white);
        cursor: not-allowed;
        opacity: 0.8;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .pricing-hero {
            padding: 4rem 0 3rem;
        }

        .pricing-hero h1 {
            font-size: 2rem;
        }

        .pricing-hero p {
            font-size: 1.125rem;
        }

        .pricing-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
            margin: -2rem auto 3rem;
        }

        .pricing-card {
            padding: 1.5rem;
        }

        .pricing-card.popular {
            transform: translateY(0);
        }

        .pricing-card.popular:hover {
            transform: translateY(-4px);
        }

        .amount {
            font-size: 2.5rem;
        }
    }

    @media (max-width: 480px) {
        .pricing-container {
            padding: 0 0.5rem;
        }

        .pricing-card {
            padding: 1.25rem;
        }

        .popular-badge, .current-badge {
            font-size: 0.75rem;
            padding: 0.375rem 1rem;
        }
    }
</style>