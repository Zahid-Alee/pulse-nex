@php
    $page_title = 'Features - Pulse Nex';
    $active_page = 'features';
@endphp

@include('includes.header')

<main>
    <!-- Hero Section -->
    <section class="features-hero">
        <div class="container">
            <h1>Powerful Website Uptime Monitoring</h1>
            <p>Track your websites’ uptime, downtime, and performance — all in one simple dashboard with instant alerts.</p>
        </div>
    </section>

    <!-- Feature Sections -->
    <section class="feature-section" id="uptime">
        <div class="container">
            <div class="feature-content">
                <div class="feature-text">
                    <h2>Flexible Website Monitoring</h2>
                    <p>Monitor your websites 24/7 according to your plan limits. Get notified instantly whenever your site goes down.</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check"></i> Continuous uptime checks</li>
                        <li><i class="fas fa-check"></i> Instant downtime alerts via email</li>
                        <li><i class="fas fa-check"></i> Average uptime summaries</li>
                        <li><i class="fas fa-check"></i> Complete downtime history</li>
                    </ul>
                </div>
                <div class="feature-image">
                    <img src="/assets/images/screenshot1.png"
                        alt="Uptime Monitoring Dashboard">
                </div>
            </div>
        </div>
    </section>

    <section class="feature-section alternate" id="response">
        <div class="container">
            <div class="feature-content reverse">
                <div class="feature-text">
                    <h2>Response Time Tracking</h2>
                    <p>Measure how fast your website responds and identify performance issues before they affect users.</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check"></i> Real-time response time checks</li>
                        <li><i class="fas fa-check"></i> Alerts for slow response times</li>
                        <li><i class="fas fa-check"></i> Trend & performance analysis</li>
                        <li><i class="fas fa-check"></i> Data from multiple locations</li>
                    </ul>
                </div>
                <div class="feature-image">
                    <img src="/assets/images/screenshot.png"
                        alt="Response Time Analytics">
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Grid -->
    <section class="additional-features">
        <div class="container">
            <h2>More Essential Tools</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <i class="fas fa-bell"></i>
                    <h3>Instant Alerts</h3>
                    <p>Receive email notifications the moment downtime is detected so you can act fast.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-chart-line"></i>
                    <h3>Detailed Analytics</h3>
                    <p>View uptime percentages, downtime events, and average response times across all your websites.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-mobile-alt"></i>
                    <h3>Dedicated Dashboard</h3>
                    <p>Manage and monitor all your websites from one simple, mobile-friendly interface.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-clock"></i>
                    <h3>Historical Data</h3>
                    <p>Review past performance to identify trends and recurring issues.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Start Monitoring Your Websites Today</h2>
            <p>Join Pulse Nex and keep your websites online, fast, and reliable — with real-time alerts and in-depth analytics.</p>
            <a href="{{ route('register') }}" class="btn btn-primary">Get Started for Free</a>
        </div>
    </section>
</main>


<style>
    /* Features Page Specific Styles */
    .features-hero {
        background-color: var(--color-primary);
        color: var(--color-white);
        padding: calc(var(--header-height) + var(--space-16)) 0 var(--space-16);
        text-align: center;
    }

    .features-hero h1 {
        font-size: var(--font-size-4xl);
        margin-bottom: var(--space-4);
    }

    .features-hero p {
        font-size: var(--font-size-xl);
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto;
    }

    .feature-section {
        padding: var(--space-16) 0;
    }

    .feature-section.alternate {
        background-color: var(--color-gray-50);
    }

    .feature-content {
        display: flex;
        align-items: center;
        gap: var(--space-12);
    }

    .feature-content.reverse {
        flex-direction: row-reverse;
    }

    .feature-text {
        flex: 1;
    }

    .feature-text h2 {
        font-size: var(--font-size-3xl);
        margin-bottom: var(--space-4);
    }

    .feature-text p {
        font-size: var(--font-size-lg);
        color: var(--color-gray-600);
        margin-bottom: var(--space-6);
    }

    .feature-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .feature-list li {
        display: flex;
        align-items: center;
        margin-bottom: var(--space-3);
        font-size: var(--font-size-lg);
    }

    .feature-list li i {
        color: var(--color-success);
        margin-right: var(--space-3);
    }

    .feature-image {
        flex: 1;
    }

    .feature-image img {
        width: 100%;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-lg);
    }

    .additional-features {
        padding: var(--space-16) 0;
        background-color: var(--color-white);
        text-align: center;
    }

    .additional-features h2 {
        margin-bottom: var(--space-12);
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: var(--space-6);
    }

    .feature-card {
        padding: var(--space-6);
        background-color: var(--color-white);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow);
        transition: var(--transition-smooth);
    }

    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .feature-card i {
        font-size: 2rem;
        color: var(--color-primary);
        margin-bottom: var(--space-4);
    }

    .feature-card h3 {
        margin-bottom: var(--space-3);
    }

    .feature-card p {
        color: var(--color-gray-600);
        margin: 0;
    }

    @media (max-width: 1024px) {
        .feature-content {
            gap: var(--space-8);
        }
    }

    @media (max-width: 768px) {
        .features-hero h1 {
            font-size: var(--font-size-3xl);
        }

        .feature-content {
            flex-direction: column;
            text-align: center;
        }

        .feature-content.reverse {
            flex-direction: column;
        }

        .feature-list li {
            justify-content: center;
        }

        .feature-image {
            margin-top: var(--space-8);
        }
    }
</style>

@include('includes.footer')
