@include('includes.header')

<main>
    <section class="contact-hero">
        <div class="container">
            <h1>Get in Touch</h1>
            <p>Have questions? We're here to help!</p>
        </div>
    </section>

    <section class="contact-section">
        <div class="container">
            <div class="contact-grid">
                <div class="contact-info">
                    <div class="info-card">
                        <i class="fas fa-envelope"></i>
                        <h3>Email Us</h3>
                        <p>support@pulsenex.com</p>
                        <p>sales@pulsenex.com</p>
                    </div>

                    <div class="info-card">
                        <i class="fas fa-phone"></i>
                        <h3>Call Us</h3>
                        <p>+1 (555) 123-4567</p>
                        <p>Mon-Fri, 9am-6pm EST</p>
                    </div>

                    <div class="info-card">
                        <i class="fas fa-map-marker-alt"></i>
                        <h3>Visit Us</h3>
                        <p>123 Monitoring Street</p>
                        <p>New York, NY 10001</p>
                    </div>

                    <div class="social-links">
                        <h3>Connect With Us</h3>
                        <div class="social-icons">
                            <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" aria-label="GitHub"><i class="fab fa-github"></i></a>
                        </div>
                    </div>
                </div>

                <div class="contact-form-container">
                    <form id="contactForm" class="contact-form">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" name="subject" required>
                        </div>

                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" rows="6" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Send Message</button>
                    </form>
                    <div id="successMessage" class="success-message" style="display: none;">
                        <i class="fas fa-check-circle"></i>
                        <h3>Message Sent!</h3>
                        <p>Thank you for contacting us. We'll get back to you shortly.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
    document.getElementById('contactForm').addEventListener('submit', async function(event) {
        event.preventDefault();

        const formData = new FormData(event.target);
        const data = {
            name: formData.get('name'),
            email: formData.get('email'),
            subject: formData.get('subject'),
            message: formData.get('message')
        };

        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        try {
            const response = await fetch('/api/contact', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (response.ok) {
                document.getElementById('successMessage').style.display = 'block';
                event.target.reset();
            } else {
                alert('Failed to send the message. Please try again later.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again later.');
        }
    });
</script>




<style>
    /* Contact Page Specific Styles */
    .contact-hero {
        background-color: var(--color-primary);
        color: var(--color-white);
        padding: calc(var(--header-height) + var(--space-16)) 0 var(--space-16);
        text-align: center;
    }

    .contact-hero h1 {
        font-size: var(--font-size-4xl);
        margin-bottom: var(--space-4);
    }

    .contact-hero p {
        font-size: var(--font-size-xl);
        opacity: 0.9;
    }

    .contact-section {
        padding: var(--space-16) 0;
        background-color: var(--color-gray-50);
    }

    .contact-grid {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: var(--space-8);
    }

    .contact-info {
        display: flex;
        flex-direction: column;
        gap: var(--space-6);
    }

    .info-card {
        background-color: var(--color-white);
        padding: var(--space-6);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow);
    }

    .info-card i {
        font-size: 2rem;
        color: var(--color-primary);
        margin-bottom: var(--space-4);
    }

    .info-card h3 {
        margin-bottom: var(--space-2);
    }

    .info-card p {
        color: var(--color-gray-600);
        margin: 0;
    }

    .social-links {
        background-color: var(--color-white);
        padding: var(--space-6);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow);
    }

    .social-links h3 {
        margin-bottom: var(--space-4);
    }

    .social-icons {
        display: flex;
        gap: var(--space-4);
    }

    .social-icons a {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--color-gray-100);
        color: var(--color-gray-700);
        border-radius: var(--border-radius-full);
        transition: var(--transition-base);
    }

    .social-icons a:hover {
        background-color: var(--color-primary);
        color: var(--color-white);
        transform: translateY(-2px);
    }

    .contact-form-container {
        background-color: var(--color-white);
        padding: var(--space-8);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow);
    }

    .contact-form {
        display: flex;
        flex-direction: column;
        gap: var(--space-4);
    }

    .form-group {
        margin-bottom: var(--space-4);
    }

    .form-group label {
        display: block;
        margin-bottom: var(--space-2);
        font-weight: 500;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: var(--space-3);
        border: 1px solid var(--color-gray-300);
        border-radius: var(--border-radius);
        transition: var(--transition-base);
    }

    .form-group input:focus,
    .form-group textarea:focus {
        border-color: var(--color-primary);
        outline: none;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .error-message {
        color: var(--color-error);
        font-size: var(--font-size-sm);
        margin-top: var(--space-1);
    }

    .success-message {
        text-align: center;
        padding: var(--space-8);
    }

    .success-message i {
        font-size: 3rem;
        color: var(--color-success);
        margin-bottom: var(--space-4);
    }

    .success-message h3 {
        margin-bottom: var(--space-2);
    }

    .success-message p {
        color: var(--color-gray-600);
    }

    .faq-section {
        padding: var(--space-16) 0;
        background-color: var(--color-white);
    }

    .faq-section h2 {
        text-align: center;
        margin-bottom: var(--space-12);
    }

    .faq-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: var(--space-6);
    }

    .faq-item {
        padding: var(--space-6);
        background-color: var(--color-gray-50);
        border-radius: var(--border-radius-lg);
    }

    .faq-item h3 {
        margin-bottom: var(--space-3);
        color: var(--color-gray-900);
    }

    .faq-item p {
        color: var(--color-gray-600);
        margin: 0;
    }

    @media (max-width: 1024px) {
        .contact-grid {
            grid-template-columns: 1fr;
        }

        .contact-info {
            order: 2;
        }

        .contact-form-container {
            order: 1;
        }
    }

    @media (max-width: 768px) {
        .contact-hero h1 {
            font-size: var(--font-size-3xl);
        }

        .faq-grid {
            grid-template-columns: 1fr;
        }

        .contact-section {
            padding: var(--space-8) 0;
        }

        .contact-form-container {
            padding: var(--space-6);
        }
    }
</style>

@include('includes.footer')
