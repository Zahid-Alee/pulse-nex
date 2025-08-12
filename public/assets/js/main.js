document.addEventListener('DOMContentLoaded', function() {
  // Mobile menu toggle
  const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
  const mobileMenu = document.querySelector('.mobile-menu');
  
  if (mobileMenuToggle && mobileMenu) {
    mobileMenuToggle.addEventListener('click', function() {
      mobileMenu.classList.toggle('open');
      document.body.classList.toggle('menu-open');
      
      // Transform hamburger into X
      this.classList.toggle('active');
      
      // Animate the hamburger menu lines
      const spans = this.querySelectorAll('span');
      if (this.classList.contains('active')) {
        spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
        spans[1].style.opacity = '0';
        spans[2].style.transform = 'rotate(-45deg) translate(7px, -6px)';
      } else {
        spans[0].style.transform = 'none';
        spans[1].style.opacity = '1';
        spans[2].style.transform = 'none';
      }
    });
    
    // Close mobile menu when clicking on a link
    const mobileMenuLinks = mobileMenu.querySelectorAll('a');
    mobileMenuLinks.forEach(link => {
      link.addEventListener('click', function() {
        mobileMenu.classList.remove('open');
        document.body.classList.remove('menu-open');
        mobileMenuToggle.classList.remove('active');
        
        const spans = mobileMenuToggle.querySelectorAll('span');
        spans[0].style.transform = 'none';
        spans[1].style.opacity = '1';
        spans[2].style.transform = 'none';
      });
    });
  }
  
  // EXTRA
  // Header scroll effect
  // const header = document.querySelector('header');
  // if (header) {
  //   window.addEventListener('scroll', function() {
  //     if (window.scrollY > 50) {
  //       header.classList.add('scrolled');
  //       header.classList.remove('header-transparent');
  //     } else {
  //       if (header.classList.contains('header-transparent')) {
  //         header.classList.remove('scrolled');
  //       }
  //     }
  //   });
    
  //   // Trigger scroll event on page load
  //   window.dispatchEvent(new Event('scroll'));
  // }
  
  // Testimonial slider auto-scroll
  const testimonialSlider = document.querySelector('.testimonial-slider');
  if (testimonialSlider && testimonialSlider.children.length > 1) {
    let scrollPosition = 0;
    const testimonialWidth = testimonialSlider.querySelector('.testimonial').offsetWidth;
    const testimonialCount = testimonialSlider.children.length;
    const gap = 32; // 2rem = 32px
    
    setInterval(() => {
      scrollPosition += testimonialWidth + gap;
      if (scrollPosition >= (testimonialWidth + gap) * testimonialCount) {
        scrollPosition = 0;
      }
      testimonialSlider.scrollTo({
        left: scrollPosition,
        behavior: 'smooth'
      });
    }, 5000);
  }
  
  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      const targetId = this.getAttribute('href');
      if (targetId !== '#') {
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
          e.preventDefault();
          
          const headerHeight = document.querySelector('header').offsetHeight;
          const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight;
          
          window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
          });
        }
      }
    });
  });
  
  // Feature cards animation on scroll
  const featureCards = document.querySelectorAll('.feature-card');
  if (featureCards.length > 0) {
    const animateCards = () => {
      featureCards.forEach(card => {
        const cardTop = card.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        
        if (cardTop < windowHeight * 0.8) {
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }
      });
    };
    
    // Set initial state
    featureCards.forEach(card => {
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)';
      card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    });
    
    // Trigger animation on scroll
    window.addEventListener('scroll', animateCards);
    // Trigger once on load
    window.addEventListener('load', animateCards);
  }
});