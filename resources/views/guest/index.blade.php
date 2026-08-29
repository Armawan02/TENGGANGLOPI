<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TENGGANGLOPI - Inovasi Infrastruktur Maritim</title>
    <link rel="icon" type="image/png" href="{{ asset('isc.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --bg-light: #f8fafc;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            overflow-x: hidden;
        }
        
        /* Navbar */
        .navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 2.5%;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-bottom: 2px solid rgba(37, 99, 235, 0.1);
            overflow: hidden;
        }
        .nav-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            font-size: 22px;
            color: var(--primary);
            text-decoration: none;
            letter-spacing: 0.5px;
        }
        .nav-logo img { width: auto; height: 90px; border-radius: 0; object-fit: contain;}
        .nav-links {
            display: flex;
            gap: 30px;
        }
        .nav-links a {
            text-decoration: none;
            color: #64748b; /* Warna samar */
            font-weight: 600;
            font-size: 16px;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            padding: 8px 15px;
            border-radius: 8px;
            opacity: 0.7; /* Efek samar */
        }
        .nav-links a.active,
        .nav-links a:hover { 
            color: var(--primary); 
            background: rgba(37, 99, 235, 0.1); /* Cover background */
            opacity: 1; /* Jelas */
        }
        .btn-login {
            background-color: var(--primary);
            color: #fff;
            padding: 10px 24px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
        }
        /* Navbar Simulation Animation */
        .nav-simulation {
            position: absolute;
            bottom: 0px;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
            opacity: 1; /* Made fully opaque for clarity */
        }

        .sim-boat {
            position: absolute;
            bottom: 0px; left: 0;
            width: 60px; height: 60px;
            animation: simBoatSeq 8s ease-in-out infinite;
        }

        .sim-sos {
            position: absolute;
            bottom: 45px; left: 0;
            width: 40px; height: 40px;
            opacity: 0;
            animation: simSosSeq 8s ease-in-out infinite;
            color: #ef4444;
            filter: drop-shadow(0 0 5px rgba(239,68,68,0.6));
        }

        .sim-gateway {
            position: absolute;
            bottom: 5px;
            right: 190px;
            width: 50px; height: 60px;
            opacity: 0.9;
            color: #1e293b;
        }

        .sim-house {
            position: absolute;
            bottom: 5px;
            right: 235px;
            width: 45px; height: 45px;
            opacity: 0.95;
            z-index: 2;
        }

        .sim-land {
            position: absolute;
            bottom: -5px;
            right: 175px;
            width: 140px; height: 25px;
            opacity: 0.9;
            z-index: 1;
        }

        .sim-gateway-signal {
            position: absolute;
            bottom: 25px;
            right: 190px;
            width: 50px; height: 35px;
            opacity: 0;
            animation: simGatewaySeq 8s ease-in-out infinite;
            color: #10b981;
            filter: drop-shadow(0 0 5px rgba(16,185,129,0.6));
        }

        .sim-gateway-alert {
            position: absolute;
            bottom: 30px;
            right: 285px;
            background: #ef4444;
            color: white;
            font-size: 9px;
            font-weight: 800;
            padding: 3px 6px;
            border-radius: 4px;
            opacity: 0;
            animation: simGatewaySeq 8s ease-in-out infinite;
            z-index: 3;
            box-shadow: 0 2px 5px rgba(239,68,68,0.5);
            letter-spacing: 0.5px;
        }

        .sim-sar {
            position: absolute;
            bottom: 0px; left: 0;
            width: 75px; height: 75px;
            animation: simSarSeq 8s ease-in-out infinite;
        }

        /* Sea Waves Effect */
        .sea-waves-container {
            position: absolute;
            bottom: 0; left: 0;
            width: 100%;
            height: 18px;
            overflow: hidden;
            z-index: 2; /* In front of boats */
        }
        .wave-layer {
            position: absolute;
            bottom: 0; left: 0;
            width: 200%;
            height: 100%;
            display: flex;
            animation: waveFlow 8s linear infinite;
        }
        .wave-layer.reverse {
            animation: waveFlow 12s linear infinite reverse;
        }
        .wave-layer svg {
            width: 50%; /* Two SVGs fill 100% of the 200% layer */
            height: 100%;
        }
        @keyframes waveFlow {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        @keyframes simBoatSeq {
            0% { transform: translateX(-10vw) rotate(0deg); opacity: 0; }
            5% { transform: translateX(5vw) rotate(0deg); opacity: 1; }
            20% { transform: translateX(35vw) rotate(0deg); }
            23% { transform: translateX(37vw) rotate(-35deg); } /* Wave */
            26% { transform: translateX(39vw) rotate(170deg); } /* Capsized */
            65% { transform: translateX(39vw) rotate(170deg); opacity: 1; } /* Waiting */
            70% { transform: translateX(39vw) rotate(0deg); opacity: 1; } /* Rescued/Upright */
            90% { transform: translateX(110vw) rotate(0deg); opacity: 1; }
            100% { transform: translateX(110vw) rotate(0deg); opacity: 0; }
        }

        @keyframes simSosSeq {
            0%, 25% { opacity: 0; transform: translateX(39vw) scale(0.5); }
            27%, 45% { opacity: 1; transform: translateX(39vw) scale(1.3); }
            47%, 100% { opacity: 0; transform: translateX(39vw) scale(0.5); }
        }
        
        @keyframes simGatewaySeq {
            0%, 28% { opacity: 0; transform: scale(0.8); }
            30%, 50% { opacity: 1; transform: scale(1.4); } /* Receiving SOS & Dispatching SAR */
            52%, 100% { opacity: 0; transform: scale(0.8); }
        }

        @keyframes simSarSeq {
            0%, 40% { transform: translateX(110vw) scaleX(-1); opacity: 0; } /* Starts from right base, facing left */
            45% { transform: translateX(90vw) scaleX(-1); opacity: 1; } /* Speeding left */
            60% { transform: translateX(45vw) scaleX(-1); } /* Arrives at capsized boat */
            70% { transform: translateX(45vw) scaleX(-1); } /* Evacuating */
            72% { transform: translateX(45vw) scaleX(1); } /* Turn around facing right */
            90% { transform: translateX(115vw) scaleX(1); opacity: 1; } /* Escorting back right */
            100% { transform: translateX(115vw) scaleX(1); opacity: 0; }
        }
        
        .nav-content-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            position: relative;
            z-index: 10; /* Di atas animasi kapal */
        }

        /* Hero Section */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 100px 20px 0;
            background-image: linear-gradient(rgba(15, 23, 42, 0.65), rgba(15, 23, 42, 0.75)), url('{{ asset("bg-tengganglopi.jpg") }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .hero-content {
            max-width: 900px;
            z-index: 10;
            position: relative;
        }
        
        #particles-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1; /* Di atas background, di bawah teks */
            pointer-events: none; /* Agar klik tembus ke konten (tombol dsb) */
        }
        
        /* Hero Logo Style */
        .hero-logo {
            max-width: 100%;
            height: auto;
            max-height: 380px;
            margin-bottom: 20px;
            filter: drop-shadow(0px 10px 15px rgba(0,0,0,0.4));
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
        
        .hero h2 {
            font-size: 34px;
            font-weight: 800;
            color: #ffffff;
            margin-top: 20px;
            letter-spacing: 1.5px;
            line-height: 1.4;
            text-transform: uppercase;
        }
        .hero h2 span { color: #38bdf8; }
        
        .hero-btn {
            margin-top: 40px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background-color: var(--primary);
            color: white;
            font-size: 16px;
            font-weight: 700;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.4);
            transition: all 0.3s;
        }
        .hero-btn:hover { background-color: var(--primary-hover); transform: scale(1.05); }

        /* Features Section */
        .features-section {
            padding: 100px 5%;
            background-color: #ffffff;
        }
        .section-title {
            text-align: center;
            font-size: 36px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 60px;
        }
        .section-title span { color: var(--primary); }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .feature-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 40px 30px;
            transition: all 0.3s;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: #bae6fd;
        }
        .feature-icon {
            width: 70px; height: 70px;
            background: #eff6ff;
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 25px auto;
            color: var(--primary);
        }
        .feature-icon svg { width: 32px; height: 32px; stroke-width: 2; }
        .feature-title { font-size: 20px; font-weight: 700; margin-bottom: 12px; color: var(--text-dark); }
        .feature-desc { font-size: 15px; color: var(--text-muted); line-height: 1.6; }

        /* Info Section */
        .about-section {
            padding: 100px 5%;
            background-color: var(--bg-light);
            display: flex;
            justify-content: center;
        }
        .about-container {
            max-width: 1000px;
            background: #ffffff;
            border-radius: 24px;
            padding: 60px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
        }
        .about-container h3 { font-size: 32px; font-weight: 800; margin-bottom: 25px; color: var(--text-dark); }
        .about-container p { font-size: 17px; color: var(--text-muted); line-height: 1.8; margin-bottom: 25px; }
        
        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 40px;
        }
        
        .about-list { list-style: none; }
        .about-list li {
            position: relative;
            padding-left: 35px;
            margin-bottom: 20px;
            font-size: 16px;
            color: var(--text-dark);
            line-height: 1.6;
        }
        .about-list li::before {
            content: "✓";
            position: absolute; left: 0; top: 0;
            background: #10b981; color: white;
            width: 24px; height: 24px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: bold;
        }
        
        
        /* Footer */
        footer {
            background: #ffffff;
            padding: 40px 5%;
            text-align: center;
            color: var(--text-muted);
            border-top: 1px solid #e2e8f0;
            font-weight: 500;
        }

        @media (max-width: 992px) {
            .hero h1 { font-size: 60px; }
            .features-grid { grid-template-columns: repeat(2, 1fr); }
            .nav-links { display: none; }
            .about-grid { grid-template-columns: 1fr; gap: 20px; }
        }
        @media (max-width: 768px) {
            .hero h1 { font-size: 42px; }
            .hero h2 { font-size: 20px; }
            .features-grid { grid-template-columns: 1fr; }
            .about-container { padding: 40px 20px; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <!-- Simulasi Keselamatan Laka Laut -->
        <div class="nav-simulation">
            <!-- Perahu Nelayan (Korban) -->
            <svg class="sim-boat" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="1.5">
                <path d="M2 14l2 4h16l2-4" stroke-linejoin="round"/>
                <path d="M12 14V3L20 14" fill="#bfdbfe" />
                <path d="M12 14V6L6 14" fill="#dbeafe" />
            </svg>
            
            <!-- Sinyal Darurat SOS -->
            <svg class="sim-sos" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
            </svg>

            <!-- Daratan Base Station -->
            <svg class="sim-land" viewBox="0 0 100 30" preserveAspectRatio="none">
                <path d="M0,30 L100,30 L100,10 C80,10 60,0 40,5 C20,10 10,25 0,30 Z" fill="#94a3b8" />
            </svg>

            <!-- Rumah / Stasiun Pemantau -->
            <svg class="sim-house" viewBox="0 0 24 24" fill="#334155" stroke="#cbd5e1" stroke-width="1.2" stroke-linejoin="round">
                <path d="M3 11L12 4l9 7v10H3z" />
                <rect x="10" y="14" width="4" height="7" fill="#64748b"/>
                <rect x="5" y="12" width="4" height="4" fill="#bae6fd" opacity="0.9"/> <!-- Jendela bercahaya biru -->
                <rect x="15" y="12" width="4" height="4" fill="#bae6fd" opacity="0.9"/>
                <path d="M12 4v-2" stroke="#64748b"/> <!-- Antena kecil rumah -->
            </svg>

            <!-- Antena Tower (Darat) -->
            <svg class="sim-gateway" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 22h16M12 2v20M8 22V10M16 22V14M12 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/>
            </svg>

            <!-- Pancaran Sinyal Radio (Menggantikan Lonceng) -->
            <svg class="sim-gateway-signal" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round">
                <path d="M15.5 5.5 a 5 5 0 0 0 -7 0" />
                <path d="M18.5 2.5 a 9 9 0 0 0 -13 0" />
            </svg>
            <div class="sim-gateway-alert">SOS LAKA LAUT!</div>

            <!-- Kapal Tim SAR / Patroli -->
            <svg class="sim-sar" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="1.5">
                <path d="M2 15l2 3h16l2-3" stroke-linejoin="round"/>
                <path d="M6 15v-4h12v4" fill="#fdba74"/>
                <path d="M12 11V7M10 9h4" stroke="#ef4444" stroke-width="2"/> <!-- Palang Merah -->
                <circle cx="16" cy="11" r="1" fill="#ef4444"/> <!-- Sirine -->
            </svg>

            <!-- Efek Gelombang Laut -->
            <div class="sea-waves-container">
                <div class="wave-layer">
                    <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
                        <path d="M0,46.29 C150,46.29 150,110 300,110 C450,110 450,46.29 600,46.29 C750,46.29 750,110 900,110 C1050,110 1050,46.29 1200,46.29 V120 H0 V46.29 Z" fill="#38bdf8" fill-opacity="0.35"/>
                    </svg>
                    <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
                        <path d="M0,46.29 C150,46.29 150,110 300,110 C450,110 450,46.29 600,46.29 C750,46.29 750,110 900,110 C1050,110 1050,46.29 1200,46.29 V120 H0 V46.29 Z" fill="#38bdf8" fill-opacity="0.35"/>
                    </svg>
                </div>
                <div class="wave-layer reverse">
                    <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
                        <path d="M0,80 C150,80 150,20 300,20 C450,20 450,80 600,80 C750,80 750,20 900,20 C1050,20 1050,80 1200,80 V120 H0 V80 Z" fill="#2563eb" fill-opacity="0.4"/>
                    </svg>
                    <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
                        <path d="M0,80 C150,80 150,20 300,20 C450,20 450,80 600,80 C750,80 750,20 900,20 C1050,20 1050,80 1200,80 V120 H0 V80 Z" fill="#2563eb" fill-opacity="0.4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="nav-content-wrapper">
            <a href="/" class="nav-logo">
                <img src="{{ asset('hero.png') }}" alt="Logo TENGGANGLOPI">
            </a>
            <div class="nav-links">
                <a href="#beranda">Beranda</a>
                <a href="#fitur">Fitur Utama</a>
                <a href="#pemantauan">Live Peta</a>
                <a href="#tentang">Tentang Sistem</a>
            </div>
            <a href="{{ route('login') }}" class="btn-login">Login Akses</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="hero">
        <canvas id="particles-canvas"></canvas>
        <div class="hero-content">
            <img src="{{ asset('logo.png') }}" alt="TENGGANG LOPI" class="hero-logo">
            <h2>INOVASI INFRASTRUKTUR MARITIM CERDAS<br>BERBASIS <span>EDGE-AI</span> MICRO-CLIMATE DETECTION DAN <span>LORA</span> UNTUK MITIGASI LAKA LAUT</h2>
            
            <a href="{{ route('login') }}" class="hero-btn">
                Masuk ke Base Station
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 5px;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
        </div>
    </section>

    <!-- Features Section -->
    <section id="fitur" class="features-section">
        <h2 class="section-title">Fitur Keselamatan <span>Maritim</span></h2>
        
        <div class="features-grid">
            <!-- Iklim Mikro / Cuaca -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17.5 19C19.9853 19 22 16.9853 22 14.5C22 12.1325 20.177 10.2016 17.854 10.015C17.3828 6.60275 14.4697 4 11 4C7.13401 4 4 7.13401 4 11C4 11.2335 4.0114 11.4645 4.03362 11.6917C2.29656 12.3082 1 13.9856 1 16C1 18.2091 2.79086 20 5 20H17.5V19Z"></path></svg>
                </div>
                <div class="feature-title">Iklim Mikro & Edge-AI</div>
                <div class="feature-desc">Klasifikasi anomali cuaca mikro (suhu, kelembapan, tekanan) secara offline dengan algoritma TinyML langsung di atas perahu nelayan.</div>
            </div>

            <!-- GPS Tracking -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 10C21 17 12 23 12 23C12 23 3 17 3 10C3 5.02944 7.02944 1 12 1C16.9706 1 21 5.02944 21 10Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                </div>
                <div class="feature-title">Live Tracking GPS</div>
                <div class="feature-desc">Pemantauan titik koordinat (Latitude & Longitude) geolokasi nelayan secara real-time untuk memandu pergerakan Tim SAR.</div>
            </div>

            <!-- Deteksi Kapal Terbalik -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M2 12h20M2 16h20M2 20h20"></path><path d="M4 12V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v6"></path></svg>
                </div>
                <div class="feature-title">Deteksi Kapal Terbalik</div>
                <div class="feature-desc">Sensor Gyroscope & Accelerometer (MPU6050) akan otomatis membaca batas kritis kemiringan stabilitas lambung kapal laut.</div>
            </div>

            <!-- Sensor Kebocoran -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path></svg>
                </div>
                <div class="feature-title">Peringatan Kebocoran</div>
                <div class="feature-desc">Sensor ultrasonik mendeteksi secara dini apabila genangan volume air laut mulai memasuki area dalam lambung/palka.</div>
            </div>

            <!-- Jaringan LoRaWAN -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="2"></circle><path d="M16.24 7.76a6 6 0 0 1 0 8.49m-8.48-.01a6 6 0 0 1 0-8.49m11.31-2.82a10 10 0 0 1 0 14.14m-14.14 0a10 10 0 0 1 0-14.14"></path></svg>
                </div>
                <div class="feature-title">Transmisi LoRaWAN</div>
                <div class="feature-desc">Koneksi radio Low Power Wide Area Network (LoRaWAN) independen yang mampu menembus area blank-spot tanpa sinyal seluler.</div>
            </div>

            <!-- Dashboard Command Center -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                </div>
                <div class="feature-title">Dashboard Base Station</div>
                <div class="feature-desc">Antarmuka pusat untuk otoritas keselamatan memantau data sensor, cuaca, dan peringatan dini (Automated SOS).</div>
            </div>
        </div>
    </section>

    <!-- Pemantauan Section -->
    <section id="pemantauan" class="features-section" style="background-color: #f1f5f9;">
        <h2 class="section-title">Live <span>Pemantauan</span> Publik</h2>
        <div style="max-width: 1200px; margin: 0 auto; background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
            <div style="padding: 15px 20px; background: #fff; border-bottom: 1px solid #e2e8f0; display: flex; gap: 10px; align-items: center;">
                <input type="text" id="searchInput" onkeypress="if(event.key === 'Enter') searchNode()" placeholder="Cari kode node (misal: NODE_01)..." style="flex: 1; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; font-size: 14px; font-family: inherit;">
                <button onclick="searchNode()" style="background: var(--primary); color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: inherit; transition: all 0.2s;" onmouseover="this.style.backgroundColor='var(--primary-hover)'" onmouseout="this.style.backgroundColor='var(--primary)'">Cari Kapal</button>
            </div>
            <div id="public-map" style="height: 500px; width: 100%; z-index: 1;"></div>
            <div style="padding: 20px; display: flex; justify-content: space-between; align-items: center; background: #fff; border-top: 1px solid #e2e8f0;">
                <div>
                    <h4 style="font-size: 18px; color: var(--text-dark); margin-bottom: 5px;">Peta Posisi Perahu Nelayan</h4>
                    <p style="font-size: 14px; color: var(--text-muted);">Data koordinat ini diperbarui secara real-time via LoRaWAN.</p>
                </div>
                <div style="display: flex; gap: 15px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="width: 12px; height: 12px; border-radius: 50%; background: #10b981;"></span>
                        <span style="font-size: 13px; font-weight: 600; color: var(--text-dark);">Terhubung</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="width: 12px; height: 12px; border-radius: 50%; background: #ef4444;"></span>
                        <span style="font-size: 13px; font-weight: 600; color: var(--text-dark);">Terputus</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Info Section -->
    <section id="tentang" class="about-section">
        <div class="about-container">
            <h3>Solusi Mitigasi Laka Laut yang Mandiri & Presisi</h3>
            <p><strong>TENGGANG LOPI</strong> dirancang untuk menyelesaikan celah kelemahan alat peringatan dini cuaca (EWS) yang selama ini hanya mengandalkan prediksi berskala makro dan lumpuh saat berada di area tanpa koneksi internet (<em>blank-spot</em>). Sistem komprehensif ini bekerja secara otonom tepat di atas kapal nelayan pesisir Majene, memastikan setiap anomali ekstrem terdeteksi bahkan sebelum bahaya tiba.</p>
            
            <div class="about-grid">
                <div>
                    <ul class="about-list">
                        <li><strong>Fase Pra-Bencana:</strong> Membawa kecerdasan buatan (TinyML) ke mikrokontroler (Edge Computing) untuk menganalisa suhu, kelembapan, dan tekanan tanpa perlu server eksternal.</li>
                        <li><strong>Fase Pasca-Bencana:</strong> Mencegah hilangnya nyawa di <em>Golden Time</em> saat kapal terbalik dengan langsung melepaskan sinyal darurat (SOS) secara otomatis.</li>
                    </ul>
                </div>
                <div>
                    <ul class="about-list">
                        <li><strong>Transmisi Independen:</strong> Sinyal dipancarkan melalui modul LoRa yang terbukti secara teknis mampu menjangkau garis pantai hingga belasan Kilometer dengan bebas biaya.</li>
                        <li><strong>Antarmuka Real-Time:</strong> Pihak BPBD dan Tim SAR dapat segera melihat data lokasi secara langsung dari Base Station di darat.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; {{ date('Y') }} TENGGANG LOPI Research Project. All rights reserved.</p>
    </footer>

    <!-- Neural Network Particle Animation -->
    <script>
        const canvas = document.getElementById('particles-canvas');
        const ctx = canvas.getContext('2d');
        const hero = document.querySelector('.hero');
        
        let width, height;
        let particles = [];
        
        // Mouse interaction state
        const mouse = { x: null, y: null, radius: 200 };
        
        function resize() {
            width = canvas.width = hero.offsetWidth;
            height = canvas.height = hero.offsetHeight;
        }
        
        window.addEventListener('resize', resize);
        
        // Listen to mouse on the hero section
        hero.addEventListener('mousemove', (e) => {
            const rect = hero.getBoundingClientRect();
            mouse.x = e.clientX - rect.left;
            mouse.y = e.clientY - rect.top;
        });
        
        hero.addEventListener('mouseleave', () => {
            mouse.x = null;
            mouse.y = null;
        });
        
        class Particle {
            constructor() {
                this.x = Math.random() * width;
                this.y = Math.random() * height;
                this.size = Math.random() * 2 + 1;
                this.speedX = Math.random() * 1.5 - 0.75;
                this.speedY = Math.random() * 1.5 - 0.75;
                // Original position for spring effect
                this.baseX = this.x;
                this.baseY = this.y;
            }
            
            update() {
                this.x += this.speedX;
                this.y += this.speedY;
                
                // Wall bounce
                if (this.x < 0 || this.x > width) this.speedX *= -1;
                if (this.y < 0 || this.y > height) this.speedY *= -1;
                
                // Mouse interaction (network pulls together around cursor)
                if (mouse.x != null && mouse.y != null) {
                    let dx = mouse.x - this.x;
                    let dy = mouse.y - this.y;
                    let distance = Math.sqrt(dx * dx + dy * dy);
                    if (distance < mouse.radius) {
                        const force = (mouse.radius - distance) / mouse.radius;
                        this.x += dx * force * 0.06; 
                        this.y += dy * force * 0.06;
                    }
                }
            }
            
            draw() {
                ctx.fillStyle = 'rgba(255, 255, 255, 0.8)';
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }
        
        function init() {
            resize();
            particles = [];
            // Density based on screen size
            let numParticles = Math.floor((width * height) / 6000); 
            // Cap particles to prevent lag
            if (numParticles > 250) numParticles = 250; 
            
            for (let i = 0; i < numParticles; i++) {
                particles.push(new Particle());
            }
        }
        
        function animate() {
            ctx.clearRect(0, 0, width, height);
            
            for (let i = 0; i < particles.length; i++) {
                particles[i].update();
                particles[i].draw();
                
                // Connect particles with lines
                for (let j = i; j < particles.length; j++) {
                    let dx = particles[i].x - particles[j].x;
                    let dy = particles[i].y - particles[j].y;
                    let distance = Math.sqrt(dx * dx + dy * dy);
                    
                    if (distance < 160) {
                        ctx.beginPath();
                        ctx.strokeStyle = `rgba(255, 255, 255, ${0.4 - distance/400})`; // fade out by distance
                        ctx.lineWidth = 1;
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.stroke();
                    }
                }
            }
            requestAnimationFrame(animate);
        }
        
        setTimeout(() => {
            init();
            animate();
        }, 50);

        // ScrollSpy untuk Navbar
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('section');
            const navLinks = document.querySelectorAll('.nav-links a');
            
            function updateActiveLink() {
                let current = '';
                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    // Deteksi ketika bagian atas section mencapai sedikit di bawah navbar
                    if (window.pageYOffset >= (sectionTop - 120)) {
                        current = section.getAttribute('id');
                    }
                });

                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (current && link.getAttribute('href').includes(current)) {
                        link.classList.add('active');
                    }
                });
            }

            // Jalankan sekali saat load
            updateActiveLink();

            // Dengarkan event scroll
            window.addEventListener('scroll', updateActiveLink);
        });
    
        </script>

        <!-- Leaflet Map Script -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var publicMap = L.map('public-map').setView([-3.543, 118.974], 12);
                L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                    maxZoom: 19,
                    attribution: '&copy; Google Maps'
                }).addTo(publicMap);

                function createShipIcon(name, isOffline) {
                    let color = isOffline ? '#ef4444' : '#10b981';
                    return L.divIcon({
                        html: `<div style="display:flex; flex-direction:column; align-items:center;">
                                <div style="background:${color}; width:24px; height:24px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid #fff; box-shadow:0 0 10px rgba(0,0,0,0.5);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><circle cx="12" cy="5" r="3"></circle><line x1="12" y1="22" x2="12" y2="8"></line><path d="M5 12H2a10 10 0 0 0 20 0h-3"></path></svg>
                                </div>
                                <div style="margin-top:4px; background:rgba(255,255,255,0.95); padding:2px 8px; border-radius:4px; font-size:11px; font-weight:800; color:#1e293b; white-space:nowrap; box-shadow:0 2px 4px rgba(0,0,0,0.15); border: 1px solid #e2e8f0; font-family: inherit;">${name}</div>
                               </div>`,
                        className: 'custom-ship-icon',
                        iconSize: [40, 50],
                        iconAnchor: [20, 12]
                    });
                }

                let publicMarkers = {};

                window.searchNode = function() {
                    const query = document.getElementById('searchInput').value.trim().toUpperCase();
                    if(!query) return;
                    
                    if(publicMarkers[query]) {
                        const latLng = publicMarkers[query].getLatLng();
                        publicMap.flyTo(latLng, 16, { duration: 1.5 });
                        publicMarkers[query].openTooltip();
                    } else {
                        alert("Node kapal '" + query + "' tidak ditemukan di peta atau sedang offline.");
                    }
                };

                function updatePublicMap() {
                    fetch("/api/fleet/public")
                        .then(response => response.json())
                        .then(res => {
                            if(res.status === 'success') {
                                res.data.forEach(node => {
                                    let isOffline = false;
                                    if (node.heartbeat) {
                                        let nowUnix = Math.floor(Date.now() / 1000);
                                        if (nowUnix - node.heartbeat > 30) isOffline = true;
                                    }
                                    
                                    let statusText = isOffline ? 'Terputus 🔴' : 'Terhubung 🟢';
                                    let tooltipContent = `
                                        <div style="font-weight:700; color:#1e293b; font-size:12px; font-family: inherit;">${node.id}</div>
                                        <div style="color:#64748b; font-size:11px; font-weight:600; font-family: inherit;">Status: ${statusText}</div>
                                    `;

                                    if (node.coordinates && node.coordinates.lat !== 0 && node.coordinates.lng !== 0) {
                                        let newLatLng = new L.LatLng(node.coordinates.lat, node.coordinates.lng);
                                        let currentIcon = createShipIcon(node.vesselName, isOffline);

                                        if (!publicMarkers[node.id]) {
                                            publicMarkers[node.id] = L.marker(newLatLng, {icon: currentIcon}).addTo(publicMap)
                                                .bindTooltip(tooltipContent, {direction: 'top', offset: [0, -12]});
                                        } else {
                                            publicMarkers[node.id].setLatLng(newLatLng);
                                            publicMarkers[node.id].setIcon(currentIcon);
                                            publicMarkers[node.id].setTooltipContent(tooltipContent);
                                        }
                                    }
                                });
                            }
                        })
                        .catch(console.error);
                }
                
                // Initialize map if element is in view to prevent rendering bugs
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            publicMap.invalidateSize();
                            updatePublicMap();
                        }
                    });
                });
                observer.observe(document.getElementById('pemantauan'));
                
                setInterval(updatePublicMap, 5000);
            });
        </script>
    </body>
</html>
