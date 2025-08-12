@php
    $page_title = 'Features - PulseNex';
    $active_page = 'features';
@endphp

@include('includes.header')

<main>
    <!-- Hero Section -->
    <section class="features-hero">
        <div class="container">
            <h1>Powerful Website Monitoring Features</h1>
            <p>Everything you need to keep your websites running smoothly</p>
        </div>
    </section>

    <!-- Feature Sections -->
    <section class="feature-section" id="uptime">
        <div class="container">
            <div class="feature-content">
                <div class="feature-text">
                    <h2>Uptime Monitoring</h2>
                    <p>Monitor your websites 24/7 with intervals as low as 15 seconds. Get instant notifications when
                        your site goes down.</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check"></i> Real-time monitoring</li>
                        <li><i class="fas fa-check"></i> Instant downtime alerts</li>
                        <li><i class="fas fa-check"></i> Detailed uptime reports</li>
                        <li><i class="fas fa-check"></i> Historical data tracking</li>
                    </ul>
                </div>
                <div class="feature-image">
                    <img src="https://images.pexels.com/photos/1181671/pexels-photo-1181671.jpeg"
                        alt="Uptime Monitoring Dashboard">
                </div>
            </div>
        </div>
    </section>

    <section class="feature-section alternate" id="ssl">
        <div class="container">
            <div class="feature-content reverse">
                <div class="feature-text">
                    <h2>SSL Certificate Monitoring</h2>
                    <p>Never let your SSL certificates expire. Get advance notifications and ensure your website remains
                        secure.</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check"></i> SSL expiration tracking</li>
                        <li><i class="fas fa-check"></i> Certificate validation</li>
                        <li><i class="fas fa-check"></i> Early expiration warnings</li>
                        <li><i class="fas fa-check"></i> SSL health monitoring</li>
                    </ul>
                </div>
                <div class="feature-image">
                    <img src="https://images.pexels.com/photos/60504/security-protection-anti-virus-software-60504.jpeg"
                        alt="SSL Certificate Monitoring">
                </div>
            </div>
        </div>
    </section>

    <section class="feature-section" id="response">
        <div class="container">
            <div class="feature-content">
                <div class="feature-text">
                    <h2>Response Time Tracking</h2>
                    <p>Monitor your website's performance with detailed response time analytics and alerts for slow
                        loading times.</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check"></i> Performance monitoring</li>
                        <li><i class="fas fa-check"></i> Response time alerts</li>
                        <li><i class="fas fa-check"></i> Trend analysis</li>
                        <li><i class="fas fa-check"></i> Global response tracking</li>
                    </ul>
                </div>
                <div class="feature-image">
                    <img src="https://images.pexels.com/photos/48727/pexels-photo-48727.jpeg"
                        alt="Response Time Analytics">
                </div>
            </div>
        </div>
    </section>

    <section class="feature-section alternate" id="domain">
        <div class="container">
            <div class="feature-content reverse">
                <div class="feature-text">
                    <h2>Domain Information Monitoring</h2>
                    <p>Keep track of your domain registrations, DNS records, and WHOIS information all in one place.</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check"></i> Domain expiration alerts</li>
                        <li><i class="fas fa-check"></i> DNS record monitoring</li>
                        <li><i class="fas fa-check"></i> WHOIS tracking</li>
                        <li><i class="fas fa-check"></i> Registrar information</li>
                    </ul>
                </div>
                <div class="feature-image">
                    <img src="https://images.pexels.com/photos/1591060/pexels-photo-1591060.jpeg"
                        alt="Domain Monitoring">
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Grid -->
    <section class="additional-features">
        <div class="container">
            <h2>More Great Features</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <i class="fas fa-bell"></i>
                    <h3>Instant Alerts</h3>
                    <p>Get notified immediately when issues are detected with your websites.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-chart-line"></i>
                    <h3>Detailed Reports</h3>
                    <p>Access comprehensive reports and analytics about your website's performance.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-mobile-alt"></i>
                    <h3>Mobile Friendly</h3>
                    <p>Monitor your websites on the go with our responsive dashboard.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-clock"></i>
                    <h3>Historical Data</h3>
                    <p>Track your website's performance over time with historical data storage.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Ready to Start Monitoring Your Websites?</h2>
            <p>Join thousands of businesses who trust PulseNex for their website monitoring needs.</p>
            <a href="signup.php" class="btn btn-primary">Get Started for Free</a>
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
