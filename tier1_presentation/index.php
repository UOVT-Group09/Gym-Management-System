<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Hub | Elite Performance</title>
    <style>
       
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: #0a0a0a; 
            color: #ffffff;
            line-height: 1.6;
        }

        
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 60px;
            background: rgba(10, 10, 10, 0.95);
            border-bottom: 1px solid #222;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar h2 {
            color: #ffffff;
            text-transform: uppercase;
            font-size: 28px;
            letter-spacing: 2px;
            font-weight: 900;
        }

        .navbar h2 span {
            color: #dfff00; 
        }

        .hero-container {
            position: relative;
            height: 85vh;
            width: 100%;
            overflow: hidden;
        }

        .slide {
            position: absolute;
            width: 100%;
            height: 100%;
            display: none;
            background-size: cover;
            background-position: center;
        }

        .slide::after {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.7);
        }

        .hero-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 10;
            width: 80%;
        }

        .hero-content h1 {
            font-size: 90px;
            text-transform: uppercase;
            font-weight: 900;
            line-height: 0.9;
            margin-bottom: 20px;
        }

        .hero-content h1 span {
            color: #dfff00;
            font-style: italic;
        }

        .btn-group {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .btn {
            padding: 18px 50px;
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            text-decoration: none;
            transition: 0.3s;
            border: 2px solid #dfff00;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-primary { background-color: #dfff00; color: #000; }
        .btn-secondary { background-color: transparent; color: #dfff00; }

        .btn:hover {
            transform: scale(1.05);
            background-color: #ffffff;
            border-color: #ffffff;
            color: #000;
        }

        
        .intro-section {
            padding: 100px 60px;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 50px;
        }

        .intro-text {
            flex: 1;
        }

        .section-tag {
            color: #dfff00;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 3px;
            margin-bottom: 15px;
            display: block;
            text-transform: uppercase;
        }

        .intro-text h2 {
            font-size: 48px;
            text-transform: uppercase;
            margin-bottom: 25px;
            line-height: 1.1;
        }

        .intro-text p {
            font-size: 18px;
            color: #bbb;
            margin-bottom: 30px;
        }

        /
        .contact-info {
            background: #111;
            padding: 80px 60px;
            border-top: 1px solid #222;
            text-align: center;
        }

        .contact-grid {
            display: flex;
            justify-content: center;
            gap: 100px;
            margin-top: 40px;
        }

        .info-item h4 {
            color: #dfff00;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }

        .info-item p {
            font-size: 24px;
            font-weight: 700;
        }

        footer {
            padding: 40px 60px;
            background: #000;
            border-top: 1px solid #111;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #555;
            text-transform: uppercase;
        }

        .fade { animation: fadeEffect 1.5s; }
        @keyframes fadeEffect { from {opacity: 0.4} to {opacity: 1} }

    </style>
</head>
<body>

 
    <nav class="navbar">
        <h2>Fitness<span>Hub</span></h2>
        <div>
            <a href="login.php" class="btn" style="padding: 10px 25px; font-size: 12px; background: #dfff00; color: #000;">Join Now</a>
        </div>
    </nav>

   
    <div class="hero-container">
        <div class="slide fade" style="background-image: url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=2070');"></div>
        <div class="slide fade" style="background-image: url('https://images.unsplash.com/photo-1541534741688-6078c6bfb5c5?q=80&w=2069');"></div>
        
        <div class="hero-content">
            <h1>Push Your <span>Limits</span></h1>
            <div class="btn-group">
                <a href="login.php" class="btn btn-primary">Login</a>
                <a href="register.php" class="btn btn-secondary">Register</a>
            </div>
        </div>
    </div>

    
    <section class="intro-section">
        <div class="intro-text">
            <span class="section-tag">Who We Are</span>
            <h2>Our Mission: <br>Relentless Momentum</h2>
            <p>At Fitness Hub, we don't just provide equipment; we provide a high-performance environment designed for those who refuse to settle. Our facility combines world-class coaching with cutting-edge science to help you unlock your true physical potential.</p>
            <p>Whether you are a professional athlete or a dedicated beginner, our community is here to push you further than you ever thought possible.</p>
        </div>
        <div style="flex: 1;">
             <img src="https://images.unsplash.com/photo-1571902943202-507ec2618e8f?q=80&w=1000" alt="Gym Interior" style="width: 100%; border: 1px solid #333;">
        </div>
    </section>

    
    <section class="contact-info">
        <span class="section-tag">Get In Touch</span>
        <h2>Ready to start?</h2>
        
        <div class="contact-grid">
            <div class="info-item">
                <h4>Call Us Today</h4>
                <p>+94(11) 000-1234</p>
            </div>
            <div class="info-item">
                <h4>Visit the Lab</h4>
                <p>123 ,Global Road, Dehiwala </p>
            </div>
            <div class="info-item">
                <h4>Email Support</h4>
                <p>contact@fitnesshub.com</p>
            </div>
        </div>
    </section>

    
    <footer>
        <div>© 2026 Fitness Hub Performance Lab. All Rights Reserved.</div>
        <div>Privacy Policy / Terms of Service</div>
    </footer>

    <script>
        let slideIndex = 0;
        function showSlides() {
            let slides = document.getElementsByClassName("slide");
            for (let i = 0; i < slides.length; i++) { slides[i].style.display = "none"; }
            slideIndex++;
            if (slideIndex > slides.length) {slideIndex = 1}
            slides[slideIndex-1].style.display = "block";
            setTimeout(showSlides, 5000);
        }
        showSlides();
    </script>

</body>
</html>