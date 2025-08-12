@php
  $page_title = "PulseNex - Website Monitoring Service";
  $active_page = "home";
@endphp

@include('includes.header')

<main>
  <!-- Hero Section -->
  <section class="hero">
    <div class="container">
      <div class="hero-content">
        <h1>Monitor Your Websites 24/7</h1>
        <p>Get instant alerts when your websites go down, SSL certificates expire, or domain issues arise.</p>
        <div class="hero-buttons">
          <a href="signup.php" class="btn btn-primary">Start Monitoring Now</a>
          <a href="pricing.php" class="btn btn-secondary">View Pricing</a>
        </div>
      </div>
      <div class="hero-image">
        <img src="assets/images/dashboard-preview.jpeg" alt="PulseNex Dashboard Preview">
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="features">
    <div class="container">
      <h2>Comprehensive Monitoring Features</h2>
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon uptime-icon"></div>
          <h3>Uptime Monitoring</h3>
          <p>Track your website's availability with configurable check intervals starting from 15 seconds.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon ssl-icon"></div>
          <h3>SSL Certificate Monitoring</h3>
          <p>Stay ahead of SSL certificate expirations and get alerted before they cause problems.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon response-icon"></div>
          <h3>Response Time Tracking</h3>
          <p>Monitor how quickly your websites respond and identify performance issues early.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon domain-icon"></div>
          <h3>Domain Information</h3>
          <p>Keep track of your domain's WHOIS information, DNS records, and registration status.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- How It Works -->
  <section class="how-it-works">
    <div class="container">
      <h2>How PulseNex Works</h2>
      <div class="steps">
        <div class="step">
          <div class="step-number">1</div>
          <h3>Add Your Websites</h3>
          <p>Simply enter the URLs of the websites you want to monitor.</p>
        </div>
        <div class="step">
          <div class="step-number">2</div>
          <h3>Choose Monitoring Options</h3>
          <p>Select what to monitor: uptime, SSL, response time, domain info.</p>
        </div>
        <div class="step">
          <div class="step-number">3</div>
          <h3>Get Notified</h3>
          <p>Receive instant alerts via email when issues are detected.</p>
        </div>
        <div class="step">
          <div class="step-number">4</div>
          <h3>View Detailed Reports</h3>
          <p>Access comprehensive reports and analytics on your dashboard.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials -->
  <section class="testimonials">
    <div class="container">
      <h2>What Our Customers Say</h2>
      <div class="testimonial-slider">
        <div class="testimonial">
          <p>"PulseNex has saved our business countless hours of downtime. The instant notifications have allowed us to resolve issues before most of our customers even notice."</p>
          <div class="testimonial-author">
            <img src="assets/images/testimonial-1.jpg" alt="Sarah Johnson">
            <div>
              <h4>Sarah Johnson</h4>
              <p>CTO, TechStart Inc.</p>
            </div>
          </div>
        </div>
        <div class="testimonial">
          <p>"The SSL certificate monitoring feature alone is worth the subscription. No more last-minute panics about expiring certificates!"</p>
          <div class="testimonial-author">
            <img src="assets/images/testimonial-2.jpg" alt="Michael Chen">
            <div>
              <h4>Michael Chen</h4>
              <p>Web Developer, DigitalEdge</p>
            </div>
          </div>
        </div>
        <div class="testimonial">
          <p>"Since switching to PulseNex, we've had complete peace of mind knowing our site is being monitored 24/7—no more surprise outages."</p>
          <div class="testimonial-author">
            <img src="assets/images/testimonial-3.jpg" alt="Michael Chen">
            <div>
              <h4>Elen Carter</h4>
              <p>IT Operations Manager, BrightWare Solutions</p>
            </div>
          </div>
        </div>
        <div class="testimonial">
          <p>"We love how easy it is to set up and customize monitoring. PulseNex has become an essential part of our infrastructure stack."</p>
          <div class="testimonial-author">
            <img src="assets/images/testimonial-4.jpg" alt="Michael Chen">
            <div>
              <h4>Jason Patel,</h4>
              <p>CTO, MarketNest Agency</p>
            </div>
          </div>
        </div>
        <div class="testimonial">
          <p>"PulseNex pays for itself every month—just one prevented downtime incident covers the cost many times over."</p>
          <div class="testimonial-author">
            <img src="assets/images/testimonial-5.jpg" alt="Michael Chen">
            <div>
              <h4>Brad Morris</h4>
              <p>DevOps Engineer, Cloudfinity</p>
            </div>
          </div>
        </div><div class="testimonial">
          <p>"Our support team is now more proactive thanks to PulseNex's real-time alerts. We fix problems before they become customer complaints"</p>
          <div class="testimonial-author">
            <img src="assets/images/testimonial-6.jpg" alt="Michael Chen">
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
      <h2>Ready to Keep Your Websites Running Smoothly?</h2>
      <p>Join thousands of businesses who trust PulseNex for their website monitoring needs.</p>
      <a href="signup.php" class="btn btn-primary">Get Started for Free</a>
    </div>
  </section>
</main>

@include('includes.footer')
