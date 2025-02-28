<?php
// Start session for login functionality
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediMart - Your One-Stop Medical Solution</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a73e8;
            --secondary-color: #34a853;
            --accent-color: #ea4335;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }
        
        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color);
        }
        
        .nav-link {
            font-weight: 500;
            color: var(--dark-color);
            transition: color 0.3s;
        }
        
        .nav-link:hover {
            color: var(--primary-color);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-success {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .hero {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 100px 0;
            margin-bottom: 50px;
        }
        
        .hero h1 {
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--dark-color);
        }
        
        .feature-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--primary-color);
        }
        
        .category-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        
        .category-card:hover {
            transform: translateY(-5px);
        }
        
        .category-card img {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        
        .testimonial {
            background-color: var(--light-color);
            padding: 80px 0;
            margin: 50px 0;
        }
        
        .testimonial-card {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .quote-icon {
            font-size: 2rem;
            color: var(--primary-color);
            opacity: 0.3;
        }
        
        footer {
            background-color: var(--dark-color);
            color: white;
            padding: 50px 0 20px;
        }
        
        .footer-links h5 {
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .footer-links ul {
            list-style: none;
            padding-left: 0;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .social-icon {
            font-size: 1.5rem;
            margin-right: 15px;
            color: rgba(255, 255, 255, 0.8);
            transition: color 0.3s;
        }
        
        .social-icon:hover {
            color: white;
        }

        .doctor-card {
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .doctor-card:hover {
            transform: translateY(-5px);
        }
        
        .doctor-card img {
            height: 250px;
            object-fit: cover;
        }
        
        .login-box {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .app-badge img {
            height: 50px;
            margin-right: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-heartbeat text-danger me-2"></i>MediMart
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-home me-1"></i>Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-pills me-1"></i>Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-user-md me-1"></i>Doctors</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-flask me-1"></i>Lab Tests</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-question-circle me-1"></i>About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-phone me-1"></i>Contact</a>
                    </li>
                </ul>
                <form class="d-flex me-3">
                    <div class="input-group">
                        <input class="form-control" type="search" placeholder="Search products..." aria-label="Search">
                        <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </form>
                <div class="d-flex align-items-center">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="dropdown me-3">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i>My Account
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-shopping-bag me-2"></i>Orders</a></li>
                                <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'doctor'): ?>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-users me-2"></i>My Patients</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-medical me-2"></i>Add Lab Report</a></li>
                                <?php else: ?>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-medical-alt me-2"></i>Lab Reports</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="#login-section" class="btn btn-outline-primary me-2"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
                        <a href="#" class="btn btn-primary"><i class="fas fa-user-plus me-1"></i>Register</a>
                    <?php endif; ?>
                    <a href="#" class="btn btn-outline-success ms-2 position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            0
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1>Your Health, Our Priority</h1>
                    <p class="lead mb-4">Get access to quality healthcare products, connect with certified doctors, and manage your health records all in one place.</p>
                    <div class="d-flex flex-wrap">
                        <a href="#" class="btn btn-primary btn-lg me-2 mb-2"><i class="fas fa-shopping-cart me-1"></i>Shop Now</a>
                        <a href="#" class="btn btn-outline-dark btn-lg mb-2"><i class="fas fa-calendar-check me-1"></i>Book Appointment</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="https://via.placeholder.com/600x400" alt="Healthcare services" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="container mb-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Why Choose MediMart?</h2>
            <p class="text-muted">We provide comprehensive healthcare solutions for all your needs</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card feature-card p-4 text-center">
                    <div class="card-body">
                        <i class="fas fa-pills feature-icon"></i>
                        <h4 class="fw-bold mb-3">Quality Products</h4>
                        <p class="text-muted">Access a wide range of authentic medicines and healthcare products delivered to your doorstep.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card p-4 text-center">
                    <div class="card-body">
                        <i class="fas fa-user-md feature-icon"></i>
                        <h4 class="fw-bold mb-3">Expert Doctors</h4>
                        <p class="text-muted">Connect with verified healthcare professionals for consultations and personalized care.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card p-4 text-center">
                    <div class="card-body">
                        <i class="fas fa-file-medical-alt feature-icon"></i>
                        <h4 class="fw-bold mb-3">Digital Records</h4>
                        <p class="text-muted">Access your lab reports and medical history securely from anywhere, anytime.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Categories -->
    <section class="container mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Popular Categories</h2>
            <a href="#" class="btn btn-outline-primary">View All <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="row">
            <div class="col-6 col-md-4 col-lg-3">
                <div class="category-card">
                    <img src="https://via.placeholder.com/300x200?text=Medicines" class="card-img-top" alt="Medicines">
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold">Medicines</h5>
                        <a href="#" class="btn btn-sm btn-outline-primary">Shop Now</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="category-card">
                    <img src="https://via.placeholder.com/300x200?text=Devices" class="card-img-top" alt="Medical Devices">
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold">Medical Devices</h5>
                        <a href="#" class="btn btn-sm btn-outline-primary">Shop Now</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="category-card">
                    <img src="https://via.placeholder.com/300x200?text=Wellness" class="card-img-top" alt="Wellness">
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold">Wellness</h5>
                        <a href="#" class="btn btn-sm btn-outline-primary">Shop Now</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="category-card">
                    <img src="https://via.placeholder.com/300x200?text=Personal+Care" class="card-img-top" alt="Personal Care">
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold">Personal Care</h5>
                        <a href="#" class="btn btn-sm btn-outline-primary">Shop Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Doctors -->
    <section class="container mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Our Specialist Doctors</h2>
            <a href="#" class="btn btn-outline-primary">View All <i class="fas fa-arrow-right ms-1"></i></a>
        </div>
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="doctor-card">
                    <img src="https://via.placeholder.com/300x300?text=Dr.+Smith" class="card-img-top" alt="Dr. Smith">
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold">Dr. John Smith</h5>
                        <p class="text-muted mb-2">Cardiologist</p>
                        <div class="mb-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star-half-alt text-warning"></i>
                            <span class="ms-1">4.5</span>
                        </div>
                        <button class="btn btn-primary btn-sm mt-2"><i class="far fa-calendar-alt me-1"></i>Book Appointment</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="doctor-card">
                    <img src="https://via.placeholder.com/300x300?text=Dr.+Johnson" class="card-img-top" alt="Dr. Johnson">
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold">Dr. Emily Johnson</h5>
                        <p class="text-muted mb-2">Neurologist</p>
                        <div class="mb-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="far fa-star text-warning"></i>
                            <span class="ms-1">4.0</span>
                        </div>
                        <button class="btn btn-primary btn-sm mt-2"><i class="far fa-calendar-alt me-1"></i>Book Appointment</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="doctor-card">
                    <img src="https://via.placeholder.com/300x300?text=Dr.+Brown" class="card-img-top" alt="Dr. Brown">
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold">Dr. David Brown</h5>
                        <p class="text-muted mb-2">Orthopedic</p>
                        <div class="mb-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <span class="ms-1">5.0</span>
                        </div>
                        <button class="btn btn-primary btn-sm mt-2"><i class="far fa-calendar-alt me-1"></i>Book Appointment</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="doctor-card">
                    <img src="https://via.placeholder.com/300x300?text=Dr.+Garcia" class="card-img-top" alt="Dr. Garcia">
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold">Dr. Maria Garcia</h5>
                        <p class="text-muted mb-2">Dermatologist</p>
                        <div class="mb-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star-half-alt text-warning"></i>
                            <span class="ms-1">4.7</span>
                        </div>
                        <button class="btn btn-primary btn-sm mt-2"><i class="far fa-calendar-alt me-1"></i>Book Appointment</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonial">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">What Our Customers Say</h2>
                <p class="text-muted">Feedback from our valued users</p>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="testimonial-card">
                        <i class="fas fa-quote-left quote-icon mb-3"></i>
                        <p class="mb-4">MediMart has revolutionized how I manage my healthcare needs. Quick delivery and authentic products every time!</p>
                        <div class="d-flex align-items-center">
                            <img src="https://via.placeholder.com/50x50" class="rounded-circle me-3" alt="Customer">
                            <div>
                                <h6 class="fw-bold mb-0">Sarah Johnson</h6>
                                <small class="text-muted">Regular Customer</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="testimonial-card">
                        <i class="fas fa-quote-left quote-icon mb-3"></i>
                        <p class="mb-4">The doctor consultation feature saved me so much time. Got my prescription and medicines delivered the same day!</p>
                        <div class="d-flex align-items-center">
                            <img src="https://via.placeholder.com/50x50" class="rounded-circle me-3" alt="Customer">
                            <div>
                                <h6 class="fw-bold mb-0">Michael Davis</h6>
                                <small class="text-muted">New User</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="testimonial-card">
                        <i class="fas fa-quote-left quote-icon mb-3"></i>
                        <p class="mb-4">Having all my lab reports in one place is so convenient. I can access them anytime and share with my doctor instantly.</p>
                        <div class="d-flex align-items-center">
                            <img src="https://via.placeholder.com/50x50" class="rounded-circle me-3" alt="Customer">
                            <div>
                                <h6 class="fw-bold mb-0">Lisa Robertson</h6>
                                <small class="text-muted">Premium Member</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Login/Register Section -->
    <section class="container mb-5" id="login-section">
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="login-box">
                    <h3 class="fw-bold mb-4">Login to Your Account</h3>
                    <form>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email or Phone</label>
                            <input type="text" class="form-control" id="email" placeholder="Enter your email or phone">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" placeholder="Enter your password">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">Remember me</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
                        <div class="text-center">
                            <a href="#" class="text-decoration-none">Forgot password?</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="login-box">
                    <h3 class="fw-bold mb-4">Register New Account</h3>
                    <form>
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullName" placeholder="Enter your full name">
                        </div>
                        <div class="mb-3">
                            <label for="regEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="regEmail" placeholder="Enter your email">
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" placeholder="Enter your phone number">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <div class="d-flex">
                                <div class="form-check me-4">
                                    <input class="form-check-input" type="radio" name="role" id="patientRole" checked>
                                    <label class="form-check-label" for="patientRole">
                                        Patient
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="role" id="doctorRole">
                                    <label class="form-check-label" for="doctorRole">
                                        Doctor
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="regPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="regPassword" placeholder="Create a password">
                        </div>
                        <div class="mb-4">
                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm your password">
                        </div>
                        <button type="submit" class="btn btn-success w-100">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Mobile App Section -->
    <section class="container mb-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 class="fw-bold mb-3">Download Our Mobile App</h2>
                <p class="lead mb-4">Get the full MediMart experience on your smartphone. Order medicines, book appointments, and track your health on the go.</p>
                <div class="app-badge">
                    <a href="#"><img src="https://via.placeholder.com/160x50?text=App+Store" alt="App Store"></a>
                    <a href="#"><img src="https://via.placeholder.com/160x50?text=Google+Play" alt="Google Play"></a>
                </div>
                <div class="mt-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-check-circle text-success me-2 fs-5"></i>
                        <span>Track orders in real-time</span>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-check-circle text-success me-2 fs-5"></i>
                        <span>Exclusive app-only discounts</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle text-success me-2 fs-5"></i>
                        <span>Set medication reminders</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="https://via.placeholder.com/300x600?text=App+Screenshot" alt="Mobile App" class="img-fluid rounded shadow">
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="container mb-5">
        <div class="card bg-primary text-white p-4">
            <div class="card-body text-center py-4">
                <h3 class="fw-bold mb-3">Subscribe to Our Newsletter</h3>
                <p class="mb-4">Stay updated with new products, health tips, and exclusive offers</p>
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <form class="d-flex">
                            <input type="email" class="form-control me-2" placeholder="Your email address">
                            <button type="submit" class="btn btn-light">Subscribe</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <h5 class="text-white mb-4"><i class="fas fa-heartbeat text-danger me-2"></i>MediMart</h5>
                    <p class="text-white-50">Your