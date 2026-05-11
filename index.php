<?php
/**
 * GreenTrans - Landing Page
 * Full-length landing page with Home, About Us, Services, How it Works, and Contact
 */
require_once __DIR__ . '/config/config.php';

// If logged in, maybe show "Go to Dashboard" instead of "Login"
$isLoggedIn = isLoggedIn();
$dashboardUrl = '';
if ($isLoggedIn) {
    $role = getUserRole();
    $dashboardUrl = APP_URL . "/$role/dashboard.php";
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="GreenTrans - Smart Transport & Logistics Management System">
    <title>GreenTrans | Smart Logistics</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Core Theme and Styles -->
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/theme.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
    
    <style>
        /* Landing Page Specific Styles */
        body {
            overflow-x: hidden;
        }
        
        .landing-navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1030;
            padding: 15px 0;
            background: var(--gt-bg-glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--gt-border-color);
            transition: all var(--gt-transition-base);
        }
        
        .landing-navbar.scrolled {
            padding: 10px 0;
            box-shadow: var(--gt-shadow-md);
            background: var(--gt-bg-navbar);
        }
        
        .nav-link {
            color: var(--gt-text-primary);
            font-weight: 600;
            margin: 0 10px;
            font-size: 0.95rem;
            position: relative;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px; left: 0;
            width: 0; height: 2px;
            background: var(--gt-gradient-primary);
            transition: width var(--gt-transition-base);
        }
        
        .nav-link:hover::after { width: 100%; }
        
        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding-top: 80px;
            background: var(--gt-gradient-hero);
            position: relative;
            overflow: hidden;
        }
        
        .hero-content h1 {
            font-size: 4rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 20px;
            background: var(--gt-gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .hero-content p {
            font-size: 1.25rem;
            color: var(--gt-text-secondary);
            margin-bottom: 40px;
            max-width: 600px;
        }
        
        .hero-shapes {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: 0;
            pointer-events: none;
        }
        
        .hero-shape-1 {
            position: absolute;
            top: 10%; right: 10%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(16,185,129,0.15) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
        }
        
        .hero-shape-2 {
            position: absolute;
            bottom: -10%; left: -5%;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(99,102,241,0.15) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
        }
        
        /* Sections */
        .section-padding {
            padding: 100px 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 15px;
        }
        
        .section-title p {
            color: var(--gt-text-secondary);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }
        
        /* Service Cards */
        .service-card {
            height: 100%;
            padding: 40px 30px;
            text-align: center;
        }
        
        .service-icon {
            width: 80px; height: 80px;
            margin: 0 auto 24px;
            border-radius: 20px;
            background: var(--gt-gradient-primary);
            color: #fff;
            display: flex;
            align-items: center; justify-content: center;
            font-size: 2.5rem;
            transform: rotate(-10deg);
            transition: all var(--gt-transition-spring);
        }
        
        .service-card:hover .service-icon {
            transform: rotate(0) scale(1.1);
        }
        
        /* How It Works Timeline */
        .work-step {
            text-align: center;
            position: relative;
        }
        
        .work-step-icon {
            width: 100px; height: 100px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: var(--gt-bg-card);
            border: 2px solid var(--gt-primary);
            display: flex;
            align-items: center; justify-content: center;
            font-size: 2.5rem;
            color: var(--gt-primary);
            box-shadow: 0 0 20px rgba(16,185,129,0.2);
            position: relative;
            z-index: 2;
        }
        
        .work-step-num {
            position: absolute;
            top: -5px; right: 0;
            width: 30px; height: 30px;
            background: var(--gt-accent);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center; justify-content: center;
            font-weight: 800;
            font-size: 0.9rem;
        }
        
        /* Connectors for desktop */
        @media (min-width: 992px) {
            .work-step:not(:last-child)::after {
                content: '';
                position: absolute;
                top: 50px; right: -50%;
                width: 100%; height: 2px;
                background: dashed 2px var(--gt-primary);
                z-index: 1;
                opacity: 0.5;
            }
        }
        
        /* Footer */
        .landing-footer {
            background: var(--gt-bg-sidebar);
            color: #fff;
            padding: 80px 0 30px;
        }
        
        .footer-link {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            display: block;
            margin-bottom: 10px;
            transition: color 0.3s;
        }
        
        .footer-link:hover { color: var(--gt-primary); }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="landing-navbar">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="#" class="d-flex align-items-center gap-2 text-decoration-none">
                <div style="width:40px;height:40px;background:var(--gt-gradient-primary);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.2rem">
                    <i class="bi bi-truck"></i>
                </div>
                <span style="font-family:var(--gt-font-heading);font-weight:800;font-size:1.4rem;color:var(--gt-text-primary)">GreenTrans</span>
            </a>
            
            <div class="d-none d-lg-flex align-items-center">
                <a href="#home" class="nav-link">Home</a>
                <a href="#about" class="nav-link">About Us</a>
                <a href="#services" class="nav-link">Services</a>
                <a href="#how-it-works" class="nav-link">How it Works</a>
                <a href="#contact" class="nav-link">Contact Us</a>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <button class="gt-theme-toggle d-flex align-items-center justify-content-center" style="width:40px;height:40px;font-size:1.1rem;border:1px solid var(--gt-border-color);border-radius:50%;background:var(--gt-bg-card);color:var(--gt-text-primary);" title="Toggle theme">
                    <i class="bi bi-moon-fill"></i>
                </button>
                
                <a href="auth/register.php" class="btn-gt-primary px-4">Get Started</a>
            </div>
        </div>
    </nav>

    <!-- 1. HERO SECTION (Home) -->
    <section id="home" class="hero-section">
        <div class="hero-shapes">
            <div class="hero-shape-1"></div>
            <div class="hero-shape-2"></div>
        </div>
        <div class="container position-relative" style="z-index:2">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0 animate-slide-up">
                    <h1>Smart Logistics<br>Made Simple</h1>
                    <p>GreenTrans revolutionizes the way you manage freight, fleet, and supply chain. Experience real-time tracking, intelligent routing, and seamless operations.</p>
                    <div class="d-flex gap-3">
                        <?php if (!$isLoggedIn): ?>
                        <a href="auth/register.php" class="btn-gt-primary btn-lg">Book a Shipment</a>
                        <?php endif; ?>
                        <a href="#services" class="btn-gt-secondary btn-lg">Explore Features</a>
                    </div>
                    <div class="mt-5 d-flex gap-4">
                        <div>
                            <h3 class="fw-bold mb-0">10k+</h3>
                            <span class="text-muted" style="font-size:0.9rem">Deliveries</span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">99%</h3>
                            <span class="text-muted" style="font-size:0.9rem">On-Time</span>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">500+</h3>
                            <span class="text-muted" style="font-size:0.9rem">Fleet Size</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 animate-slide-left delay-2">
                    <!-- Glassmorphic Mockup Illustration -->
                    <div class="glass-card p-4 position-relative">
                        <div class="position-absolute" style="top:-20px;right:-20px;width:100px;height:100px;background:var(--gt-accent);border-radius:50%;filter:blur(30px);z-index:-1"></div>
                        <svg viewBox="0 0 500 400" xmlns="http://www.w3.org/2000/svg" style="width:100%;height:auto;">
                            <!-- App Window -->
                            <rect x="20" y="20" width="460" height="360" rx="16" fill="var(--gt-bg-secondary)" stroke="var(--gt-border-color)" stroke-width="2"/>
                            <!-- Header -->
                            <rect x="20" y="20" width="460" height="50" rx="16" fill="var(--gt-bg-tertiary)"/>
                            <circle cx="45" cy="45" r="6" fill="#ef4444"/>
                            <circle cx="65" cy="45" r="6" fill="#f59e0b"/>
                            <circle cx="85" cy="45" r="6" fill="#10b981"/>
                            <!-- Map UI -->
                            <rect x="40" y="90" width="280" height="150" rx="8" fill="#e2e8f0"/>
                            <path d="M60 180 Q 150 100 200 150 T 300 120" fill="none" stroke="#6366f1" stroke-width="4" stroke-dasharray="8 8"/>
                            <circle cx="60" cy="180" r="8" fill="#10b981"/>
                            <circle cx="300" cy="120" r="8" fill="#ef4444"/>
                            
                            <!-- Tracking Card -->
                            <rect x="340" y="90" width="120" height="270" rx="8" fill="var(--gt-bg-card)" stroke="var(--gt-border-color)"/>
                            <rect x="355" y="110" width="90" height="10" rx="5" fill="var(--gt-bg-tertiary)"/>
                            <rect x="355" y="140" width="60" height="8" rx="4" fill="var(--gt-primary)"/>
                            <rect x="355" y="160" width="80" height="8" rx="4" fill="var(--gt-text-muted)"/>
                            
                            <!-- Stats Card -->
                            <rect x="40" y="260" width="130" height="100" rx="8" fill="var(--gt-gradient-primary)"/>
                            <rect x="190" y="260" width="130" height="100" rx="8" fill="var(--gt-bg-card)" stroke="var(--gt-border-color)"/>
                            <text x="105" y="315" font-family="Outfit" font-size="24" fill="#fff" font-weight="bold" text-anchor="middle">In Transit</text>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. ABOUT US SECTION -->
    <section id="about" class="section-padding bg-transparent">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="row g-4">
                        <div class="col-6">
                            <div class="neo-card text-center mb-4 animate-slide-up">
                                <i class="bi bi-globe-americas text-primary" style="font-size:3rem"></i>
                                <h4 class="mt-3">Pan India</h4>
                                <p class="text-muted small">Extensive network coverage</p>
                            </div>
                            <div class="neo-card text-center animate-slide-up delay-2">
                                <i class="bi bi-shield-check text-success" style="font-size:3rem"></i>
                                <h4 class="mt-3">100% Secure</h4>
                                <p class="text-muted small">Insured & safe transit</p>
                            </div>
                        </div>
                        <div class="col-6" style="margin-top: 40px;">
                            <div class="neo-card text-center mb-4 animate-slide-up delay-1">
                                <i class="bi bi-lightning-charge text-warning" style="font-size:3rem"></i>
                                <h4 class="mt-3">Fast Delivery</h4>
                                <p class="text-muted small">Express overnight options</p>
                            </div>
                            <div class="neo-card text-center animate-slide-up delay-3">
                                <i class="bi bi-headset text-info" style="font-size:3rem"></i>
                                <h4 class="mt-3">24/7 Support</h4>
                                <p class="text-muted small">Always here to help</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 offset-lg-1">
                    <h2 class="fw-bold mb-4" style="font-family:var(--gt-font-heading);font-size:2.8rem">About GreenTrans</h2>
                    <p class="mb-4 text-muted" style="font-size:1.1rem;line-height:1.8">GreenTrans was founded with a single mission: to digitize and optimize the highly fragmented logistics sector. We believe that shipping goods should be as easy as sending an email.</p>
                    <p class="mb-4 text-muted" style="font-size:1.1rem;line-height:1.8">By leveraging modern cloud technologies, real-time GPS tracking, and a powerful dashboard ecosystem, we provide total visibility and control to businesses, fleet managers, and end customers alike.</p>
                    <ul class="list-unstyled mb-5">
                        <li class="mb-3 d-flex align-items-center"><i class="bi bi-check-circle-fill text-success me-3 fs-5"></i> Advanced Fleet Utilization</li>
                        <li class="mb-3 d-flex align-items-center"><i class="bi bi-check-circle-fill text-success me-3 fs-5"></i> Role-based Intelligent Dashboards</li>
                        <li class="mb-3 d-flex align-items-center"><i class="bi bi-check-circle-fill text-success me-3 fs-5"></i> Transparent Pricing & Billing</li>
                    </ul>
                    <a href="#contact" class="btn-gt-outline">Learn More About Us</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. SERVICES SECTION -->
    <section id="services" class="section-padding" style="background:var(--gt-bg-tertiary)">
        <div class="container">
            <div class="section-title">
                <h2>Our Premium Services</h2>
                <p>We provide a comprehensive suite of logistics solutions tailored for modern businesses.</p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="glass-card service-card">
                        <div class="service-icon"><i class="bi bi-truck-front"></i></div>
                        <h4 class="mb-3">B2B Freight</h4>
                        <p class="text-muted">Full Truck Load (FTL) and Less than Truck Load (LTL) services optimized for business supply chains.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="glass-card service-card" style="border-color:rgba(99,102,241,0.3)">
                        <div class="service-icon" style="background:var(--gt-gradient-secondary)"><i class="bi bi-geo-alt"></i></div>
                        <h4 class="mb-3">Live Tracking</h4>
                        <p class="text-muted">Know exactly where your shipment is with real-time GPS integration and milestone updates.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="glass-card service-card" style="border-color:rgba(245,158,11,0.3)">
                        <div class="service-icon" style="background:linear-gradient(135deg, #f59e0b, #d97706)"><i class="bi bi-speedometer2"></i></div>
                        <h4 class="mb-3">Fleet Management</h4>
                        <p class="text-muted">Tools for managers to monitor vehicle health, driver availability, and optimize routing efficiency.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="glass-card service-card" style="border-color:rgba(239,68,68,0.3)">
                        <div class="service-icon" style="background:linear-gradient(135deg, #ef4444, #dc2626)"><i class="bi bi-box-seam"></i></div>
                        <h4 class="mb-3">Express Parcel</h4>
                        <p class="text-muted">Overnight and express delivery solutions for time-sensitive documents and small parcels.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="glass-card service-card">
                        <div class="service-icon"><i class="bi bi-bar-chart-line"></i></div>
                        <h4 class="mb-3">Data Analytics</h4>
                        <p class="text-muted">Exportable Excel reports, revenue metrics, and performance charts for business intelligence.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="glass-card service-card" style="border-color:rgba(59,130,246,0.3)">
                        <div class="service-icon" style="background:linear-gradient(135deg, #3b82f6, #2563eb)"><i class="bi bi-wallet2"></i></div>
                        <h4 class="mb-3">Automated Billing</h4>
                        <p class="text-muted">Instant invoice generation, secure payment tracking, and transparent cost calculations.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. HOW IT WORKS SECTION -->
    <section id="how-it-works" class="section-padding bg-transparent">
        <div class="container">
            <div class="section-title">
                <h2>How GreenTrans Works</h2>
                <p>A seamless, digitized workflow from booking to delivery.</p>
            </div>
            
            <div class="row mt-5 pt-4">
                <div class="col-lg-3 col-md-6 mb-5 mb-lg-0">
                    <div class="work-step animate-slide-up delay-1">
                        <div class="work-step-icon">
                            <i class="bi bi-phone"></i>
                            <div class="work-step-num">1</div>
                        </div>
                        <h4>Book Online</h4>
                        <p class="text-muted small">Customer enters pickup and delivery details to instantly generate a booking & cost estimate.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-5 mb-lg-0">
                    <div class="work-step animate-slide-up delay-2">
                        <div class="work-step-icon">
                            <i class="bi bi-person-badge"></i>
                            <div class="work-step-num">2</div>
                        </div>
                        <h4>Auto Assign</h4>
                        <p class="text-muted small">Admin/Manager assigns the optimal vehicle and an available driver to the shipment.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-5 mb-lg-0">
                    <div class="work-step animate-slide-up delay-3">
                        <div class="work-step-icon">
                            <i class="bi bi-truck"></i>
                            <div class="work-step-num">3</div>
                        </div>
                        <h4>In Transit</h4>
                        <p class="text-muted small">The driver picks up the cargo. Real-time status updates are sent to the customer.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="work-step animate-slide-up delay-4">
                        <div class="work-step-icon">
                            <i class="bi bi-box2-heart"></i>
                            <div class="work-step-num">4</div>
                        </div>
                        <h4>Delivery</h4>
                        <p class="text-muted small">Package arrives safely. Invoice is generated and proof of delivery is logged.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. CONTACT US SECTION -->
    <section id="contact" class="section-padding" style="background:var(--gt-bg-card)">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 mb-5 mb-lg-0">
                    <h2 class="fw-bold mb-4" style="font-family:var(--gt-font-heading);font-size:2.5rem">Get in Touch</h2>
                    <p class="mb-5 text-muted">Have questions about our logistics solutions? Ready to digitize your transport business? Our team is here to help.</p>
                    
                    <div class="d-flex align-items-center mb-4">
                        <div style="width:50px;height:50px;background:rgba(16,185,129,0.1);color:var(--gt-primary);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin-right:20px">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold">Headquarters</h5>
                            <p class="text-muted mb-0">123 Tech Park, Cyber City, India</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-4">
                        <div style="width:50px;height:50px;background:rgba(99,102,241,0.1);color:var(--gt-secondary);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin-right:20px">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold">Email Us</h5>
                            <p class="text-muted mb-0">support@greentrans.com</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <div style="width:50px;height:50px;background:rgba(245,158,11,0.1);color:var(--gt-accent);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin-right:20px">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold">Call Us</h5>
                            <p class="text-muted mb-0">+91 98765 43210</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 offset-lg-1">
                    <div class="neo-card p-4 p-md-5">
                        <h4 class="mb-4 fw-bold">Send us a Message</h4>
                        <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Thank you! We will get back to you soon.');">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="gt-form-group mb-0">
                                        <label class="gt-label">Your Name</label>
                                        <input type="text" class="gt-input" placeholder="John Doe" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="gt-form-group mb-0">
                                        <label class="gt-label">Your Email</label>
                                        <input type="email" class="gt-input" placeholder="john@company.com" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="gt-form-group mb-0">
                                        <label class="gt-label">Subject</label>
                                        <input type="text" class="gt-input" placeholder="How can we help?" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="gt-form-group mb-0">
                                        <label class="gt-label">Message</label>
                                        <textarea class="gt-input" rows="4" placeholder="Write your message here..." required></textarea>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn-gt-primary w-100 py-3">Send Message <i class="bi bi-send ms-2"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="landing-footer">
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-4 pe-lg-5">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div style="width:36px;height:36px;background:var(--gt-gradient-primary);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem">
                            <i class="bi bi-truck"></i>
                        </div>
                        <span style="font-family:var(--gt-font-heading);font-weight:800;font-size:1.4rem;">GreenTrans</span>
                    </div>
                    <p style="color:rgba(255,255,255,0.6);font-size:0.95rem">Digitalizing the logistics sector with modern technology. Book, track, and manage your freight with ease.</p>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h5 class="fw-bold mb-4">Quick Links</h5>
                    <a href="#home" class="footer-link">Home</a>
                    <a href="#about" class="footer-link">About Us</a>
                    <a href="#services" class="footer-link">Services</a>
                    <a href="#contact" class="footer-link">Contact</a>
                </div>
                <div class="col-lg-3 col-md-4">
                    <h5 class="fw-bold mb-4">Services</h5>
                    <a href="#" class="footer-link">B2B Freight</a>
                    <a href="#" class="footer-link">Express Delivery</a>
                    <a href="#" class="footer-link">Live Tracking</a>
                    <a href="#" class="footer-link">Fleet Management</a>
                </div>
                <div class="col-lg-3 col-md-4">
                    <h5 class="fw-bold mb-4">Newsletter</h5>
                    <p style="color:rgba(255,255,255,0.6);font-size:0.9rem">Subscribe to get the latest updates on our features.</p>
                    <div class="d-flex mt-3">
                        <input type="email" class="form-control bg-dark border-secondary text-white" placeholder="Email Address" style="border-radius:20px 0 0 20px">
                        <button class="btn btn-success" style="background:var(--gt-primary);border:none;border-radius:0 20px 20px 0;padding:0 20px"><i class="bi bi-arrow-right"></i></button>
                    </div>
                </div>
            </div>
            <div class="text-center pt-4" style="border-top:1px solid rgba(255,255,255,0.1)">
                <p style="color:rgba(255,255,255,0.5);font-size:0.9rem;margin:0">&copy; <?= date('Y') ?> GreenTrans Logistics Management System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Script for Navbar scroll effect -->
    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.landing-navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
    <script src="<?= APP_URL ?>/assets/js/theme.js"></script>
    <script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body>
</html>
