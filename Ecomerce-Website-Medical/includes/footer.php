</main>
    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-section">
                <div class="footer-logo">
                    <i class="fas fa-heartbeat pulse"></i>
                    <h3>MediCare</h3>
                </div>
                <p>Your trusted partner in healthcare e-commerce. We provide quality medical supplies and professional services.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>

            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Our Services</a></li>
                    <li><a href="#">Products</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Contact Info</h4>
                <ul class="contact-info">
                    <li><i class="fas fa-phone"></i> +1 234 567 8900</li>
                    <li><i class="fas fa-envelope"></i> info@medicare.com</li>
                    <li><i class="fas fa-map-marker-alt"></i> 123 Health Street, Medical City</li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Newsletter</h4>
                <form class="newsletter-form">
                    <input type="email" placeholder="Enter your email">
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> MediCare. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
            document.querySelector('.nav-links').classList.toggle('active');
            this.classList.toggle('active');
        });
    </script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const navLinks = document.querySelector('.nav-links');
    
    mobileMenuBtn.addEventListener('click', function() {
        this.classList.toggle('active');
        navLinks.classList.toggle('active');
    });
});
</script>
</body>
</html>