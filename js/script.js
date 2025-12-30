        // Navigation functionality
        const navbar = document.getElementById('navbar');
        const navbarToggle = document.getElementById('navbarToggle');
        const navbarMenu = document.getElementById('navbarMenu');
        const navLinks = document.querySelectorAll('.nav-link');
        const menuIcon = document.getElementById('menuIcon');

        // Scroll effect for navbar
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Mobile menu toggle
        navbarToggle.addEventListener('click', () => {
            navbarMenu.classList.toggle('active');
            menuIcon.textContent = navbarMenu.classList.contains('active') ? '✕' : '☰';
        });

        // Close mobile menu when clicking a link
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                navbarMenu.classList.remove('active');
                menuIcon.textContent = '☰';
            });
        });

        // Active nav link on scroll
        const sections = document.querySelectorAll('section[id]');
        
        function activateNavLink() {
            const scrollY = window.pageYOffset;

            sections.forEach(section => {
                const sectionHeight = section.offsetHeight;
                const sectionTop = section.offsetTop - 100;
                const sectionId = section.getAttribute('id');
                const navLink = document.querySelector(`.nav-link[href="#${sectionId}"]`);

                if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                    navLinks.forEach(link => link.classList.remove('active'));
                    if (navLink) navLink.classList.add('active');
                }
            });
        }

        window.addEventListener('scroll', activateNavLink);
        activateNavLink();

        // Latest Designs Filter
        const filterButtons = document.querySelectorAll('.filter-btn');
        const designCards = document.querySelectorAll('.design-card');

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                const filter = button.dataset.filter;

                // Update active button
                filterButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');

                // Filter designs
                designCards.forEach(card => {
                    const category = card.dataset.category;
                    if (filter === 'All' || category === filter) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Gallery Tabs
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const tabId = button.dataset.tab;

                // Update active button
                const parentTabs = button.closest('.tabs');
                const siblingButtons = parentTabs.querySelectorAll('.tab-btn');
                const siblingContents = parentTabs.querySelectorAll('.tab-content');

                siblingButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');

                // Show corresponding content
                siblingContents.forEach(content => {
                    content.classList.remove('active');
                    if (content.id === tabId) {
                        content.classList.add('active');
                    }
                });
            });
        });

        // Form submission handling (optional - for demo purposes)
        const contactForm = document.getElementById('contactForm');
        
        contactForm.addEventListener('submit', (e) => {
            // Form will submit to Formspree
            // You can add custom validation or success message here
            console.log('Form submitted');
        });

        // Smooth scroll offset for fixed navbar
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const offsetTop = target.offsetTop - 80;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });