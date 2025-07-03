// Main JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            if (targetSection) {
                targetSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe elements for animation
    const animatedElements = document.querySelectorAll('.process-step, .step-card, .feature-card, .testimonial-card');
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
        observer.observe(el);
    });

    // Add hover effects for cards
    const cards = document.querySelectorAll('.feature-card, .testimonial-card');
    cards.forEach((card) => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
            this.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 8px 32px rgba(0, 0, 0, 0.08)';
        });
    });

    // Add hover effects for step cards
    const stepCards = document.querySelectorAll('.step-card');
    stepCards.forEach((card) => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(10px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0) scale(1)';
        });
    });

    // Animate stat numbers
    const statNumbers = document.querySelectorAll('.stat-number');
    statNumbers.forEach(number => {
        const finalNumber = parseInt(number.textContent);
        number.textContent = '0';
        
        const animateCounter = () => {
            let current = 0;
            const increment = finalNumber / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= finalNumber) {
                    current = finalNumber;
                    clearInterval(timer);
                }
                number.textContent = Math.floor(current) + (number.textContent.includes('+') ? '+' : '');
            }, 30);
        };

        // Trigger counter animation when element comes into view
        const numberObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        animateCounter();
                    }, 500);
                    numberObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        numberObserver.observe(number);
    });

    // Add parallax effect to hero section
    const heroSection = document.querySelector('.hero-section');
    if (heroSection) {
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.3;
            heroSection.style.transform = `translateY(${rate}px)`;
        });
    }
    
    // Services tab functionality
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    if (tabBtns.length > 0 && tabContents.length > 0) {
        // Function to initialize tabs
        function initializeTabs() {
            // Set first tab as active by default if none are active
            if (!document.querySelector('.tab-btn.active')) {
                tabBtns[0].classList.add('active');
                tabContents[0].classList.add('active');
            }
            
            // Add click event listeners to all tab buttons
            tabBtns.forEach(btn => {
                // Remove any existing event listeners first to prevent duplicates
                const newBtn = btn.cloneNode(true);
                btn.parentNode.replaceChild(newBtn, btn);
                
                newBtn.addEventListener('click', function() {
                    // First, get fresh references to all tab buttons and contents
                    const allTabBtns = document.querySelectorAll('.tab-btn');
                    const allTabContents = document.querySelectorAll('.tab-content');
                    
                    // Remove active class from all buttons and contents
                    allTabBtns.forEach(b => b.classList.remove('active'));
                    allTabContents.forEach(content => content.classList.remove('active'));
                    
                    // Add active class to clicked button only
                    this.classList.add('active');
                    
                    // Show corresponding content
                    const tabId = this.getAttribute('data-tab');
                    const tabContent = document.getElementById(tabId);
                    if (tabContent) {
                        tabContent.classList.add('active');
                    } else {
                        console.error('Tab content not found for ID:', tabId);
                    }
                });
            });
        }
        
        // Initialize tabs when page loads
        initializeTabs();
        
        // Re-initialize tabs when DOM changes (for dynamic content)
        const observer = new MutationObserver(initializeTabs);
        observer.observe(document.querySelector('.services-tabs'), { childList: true, subtree: true });
    }
    
    // Feature card hover effects
    const featureCards = document.querySelectorAll('.feature-card');

    featureCards.forEach(card => {
        const hoverElement = card.querySelector('.feature-hover');
        if (hoverElement) {
            card.addEventListener('mouseenter', () => {
                hoverElement.style.opacity = '1';
                hoverElement.style.transform = 'translateY(0)';
            });
            
            card.addEventListener('mouseleave', () => {
                hoverElement.style.opacity = '0';
                hoverElement.style.transform = 'translateY(20px)';
            });
        }
    });
    
    // Services image hover effect
    const servicesImage = document.querySelector('.services-image');

    if (servicesImage) {
        const img = servicesImage.querySelector('img');
        if (img) {
            servicesImage.addEventListener('mouseenter', () => {
                img.style.transform = 'scale(1.05)';
            });
            
            servicesImage.addEventListener('mouseleave', () => {
                img.style.transform = 'scale(1)';
            });
        }
    }
    
    // About section hover effects
    const aboutStory = document.querySelector('.about-story');
    const valueCards = document.querySelectorAll('.value-card');

    if (aboutStory) {
        aboutStory.addEventListener('mouseenter', () => {
            aboutStory.style.transform = 'translateY(-5px)';
            aboutStory.style.boxShadow = '0 15px 30px rgba(0,0,0,0.1)';
        });
        
        aboutStory.addEventListener('mouseleave', () => {
            aboutStory.style.transform = 'translateY(0)';
            aboutStory.style.boxShadow = '0 10px 30px rgba(0,0,0,0.05)';
        });
    }

    if (valueCards) {
        valueCards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-5px)';
                card.style.boxShadow = '0 10px 20px rgba(0,0,0,0.1)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0)';
                card.style.boxShadow = '0 5px 15px rgba(0,0,0,0.05)';
            });
        });
    }
});