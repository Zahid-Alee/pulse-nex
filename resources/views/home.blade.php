@php
    $page_title = 'Pulse Nex - Flexible Website Uptime & Performance Monitoring';
    $active_page = 'home';
@endphp

@include('includes.header')

<main>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Keep Your Websites Online, All the Time</h1>
                <p>Pulse Nex gives you real-time monitoring, instant downtime alerts, and powerful analytics, all from
                    one dashboard.</p>
                <div class="hero-buttons">
                    <a href="{{ route('register') }}" class="btn btn-primary">Start Monitoring Now</a>
                    <a href="{{ route('pricing') }}" class="btn btn-secondary">View Plans</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="{{ asset('assets/images/dashboard-preview.jpeg') }}" alt="Pulse Nex Dashboard Preview">
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2>Everything You Need to Monitor Your Websites</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon uptime-icon"></div>
                    <h3>Reliable Uptime Monitoring</h3>
                    <p>Continuously monitor your websites’ availability with scheduled checks at regular intervals.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon ssl-icon"></div>
                    <h3>Instant Downtime Alerts</h3>
                    <p>Receive email notifications the moment your website goes down so you can act quickly.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon response-icon"></div>
                    <h3>Detailed Analytics & Reports</h3>
                    <p>Track uptime statistics, response patterns, and performance trends with easy-to-read analytics.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon domain-icon"></div>
                    <h3>Website History</h3>
                    <p>Access historical uptime and downtime data to analyze your website’s reliability over time.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- How It Works -->
    <section id="how-it-works" class="how-it-works">
        <div class="container">
            <h2>How Pulse Nex Works</h2>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Add Your Websites</h3>
                    <p>Enter the URLs of the websites you want to monitor in your dashboard.</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Set Your Monitoring Options</h3>
                    <p>Choose uptime checks, SSL monitoring, response time tracking, and more.</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Get Instant Alerts</h3>
                    <p>Receive email notifications the moment your website goes down.</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <h3>Analyze Performance</h3>
                    <p>View uptime history, average response times, and trends from detailed graphs.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section id="testimonials" class="testimonials">
        <div class="container">
            <h2>What Our Users Say</h2>
            <div class="testimonial-slider">
                <div class="testimonial">
                    <p>"Pulse Nex has helped us catch outages before our customers even noticed. It’s a must-have for
                        any serious website owner."</p>
                    <div class="testimonial-author">
                        <img src="{{ asset('assets/images/testimonial-1.jpg') }}" alt="Sarah Johnson">
                        <div>
                            <h4>Sarah Johnson</h4>
                            <p>CTO, TechStart Inc.</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial">
                    <p>"The analytics are amazing. We can track uptime across all our websites from a single dashboard."
                    </p>
                    <div class="testimonial-author">
                        <img src="{{ asset('assets/images/testimonial-2.jpg') }}" alt="Michael Chen">
                        <div>
                            <h4>Michael Chen</h4>
                            <p>Web Developer, DigitalEdge</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial">
                    <p>"Flexible plans mean we only pay for what we need. The alerts are fast and reliable."</p>
                    <div class="testimonial-author">
                        <img src="{{ asset('assets/images/testimonial-3.jpg') }}" alt="Elen Carter">
                        <div>
                            <h4>Elen Carter</h4>
                            <p>IT Operations Manager, BrightWare Solutions</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial">
                    <p>"We’ve reduced downtime incidents by over 90% since switching to Pulse Nex."</p>
                    <div class="testimonial-author">
                        <img src="{{ asset('assets/images/testimonial-4.jpg') }}" alt="Jason Patel">
                        <div>
                            <h4>Jason Patel</h4>
                            <p>CTO, MarketNest Agency</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial">
                    <p>"One prevented downtime pays for the subscription many times over. Highly recommended."</p>
                    <div class="testimonial-author">
                        <img src="{{ asset('assets/images/testimonial-5.jpg') }}" alt="Brad Morris">
                        <div>
                            <h4>Brad Morris</h4>
                            <p>DevOps Engineer, Cloudfinity</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial">
                    <p>"Real-time alerts have transformed how our support team handles outages — we’re always one step
                        ahead."</p>
                    <div class="testimonial-author">
                        <img src="{{ asset('assets/images/testimonial-6.jpg') }}" alt="Sandra Kim">
                        <div>
                            <h4>Sandra Kim</h4>
                            <p>Director of E-commerce, NovaRetail</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Monitor Smarter with Pulse Nex</h2>
            <p>Join businesses worldwide who rely on Pulse Nex for real-time monitoring and actionable insights.</p>
            <a href="{{ route('register') }}" class="btn btn-primary">Get Started for Free</a>
        </div>
    </section>
</main>

@include('includes.footer')
