<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="description"
        content="Pseudolearn adalah aplikasi pembelajaran dasar pemrograman berbasis pseudocode yang dirancang untuk membantu siswa memahami konsep dasar pemrograman dengan cara yang interaktif." />
    <meta name="keywords"
        content="Pseudolearn, pembelajaran pemrograman, dasar pemrograman, pseudocode, aplikasi edukasi, belajar pemrograman, interaktif, siswa, pendidikan, teknologi pendidikan" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title"
        content="Pseudolearn" />
    <meta property="og:url" content="https://pseudolearn.web.id" />
    <meta property="og:site_name" content="Pseudolearn" />
    <link rel="canonical" href="https://pseudolearn.web.id" />
    <link rel="shortcut icon" href="{!! asset('assets/media/logos/logo-polinema.ico') !!}" />
    <title>Halaman Pemeliharaan - Sistem Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            box-sizing: border-box;
            background: linear-gradient(135deg, #ff7f50 0%, #ff6b35 50%, #f7931e 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .maintenance-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .maintenance-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
        }
        
        .maintenance-header {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            color: white;
            padding: 25px 30px;
            text-align: center;
            position: relative;
        }
        
        @keyframes wave {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-10deg); }
            75% { transform: rotate(10deg); }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .maintenance-body {
            padding: 40px 30px;
            text-align: center;
        }
        
        .status-badge {
            background: linear-gradient(135deg, #ff8c42, #ff6b35);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 25px;
            font-size: 0.9rem;
            animation: glow 2s ease-in-out infinite alternate;
        }
        
        @keyframes glow {
            from { box-shadow: 0 0 10px rgba(255, 107, 53, 0.5); }
            to { box-shadow: 0 0 20px rgba(255, 107, 53, 0.8); }
        }

        .btn-refresh {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 20px;
        }
        
        .btn-refresh:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
            color: white;
        }
        
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }
        
        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        .shape.orange {
            background: rgba(255, 107, 53, 0.2);
        }
        
        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }
        
        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }
        
        .shape:nth-child(4) {
            width: 100px;
            height: 100px;
            top: 10%;
            right: 30%;
            animation-delay: 1s;
        }
        
        .shape:nth-child(5) {
            width: 70px;
            height: 70px;
            bottom: 30%;
            right: 20%;
            animation-delay: 3s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        @media (max-width: 768px) {
            .maintenance-header {
                padding: 30px 20px;
            }
            
            .maintenance-body {
                padding: 30px 20px;
            }
            
            .maintenance-icon {
                font-size: 3rem;
            }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape orange"></div>
        <div class="shape"></div>
        <div class="shape orange"></div>
        <div class="shape"></div>
        <div class="shape orange"></div>
    </div>
    
    <div class="maintenance-container">
        <div class="maintenance-card">
            <div class="maintenance-header">
                <div class="maintenance-icon mb-2">
                    <img src="{{ asset('assets/media/img/avatar_maintance.webp') }}" alt="Maintenance" class="img-fluid" style="max-width:180px;">
                </div>
                <h1 class="fs-3 mb-2">Sistem Sedang Dalam Pemeliharaan</h1>
                <p class="mb-0 opacity-90">Mohon maaf atas ketidaknyamanan ini</p>
            </div>
            
            <div class="maintenance-body">
                <div class="status-badge">
                    <i class="fas fa-cog fa-spin me-2"></i>
                    Sedang Berlangsung
                </div>
                
                <h3 class="h4 text-dark mb-3">Pemeliharaan oleh Dosen</h3>
                <p class="text-muted mb-4">
                    Sistem mahasiswa sedang dalam proses pemeliharaan dan pembaruan oleh tim dosen. 
                    Kami sedang melakukan perbaikan untuk meningkatkan kualitas layanan.
                </p>
                
                
                {{-- <div class="contact-info">
                    <h5 class="text-dark mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Informasi Kontak
                    </h5>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>admin@universitas.ac.id</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>(021) 1234-5678</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <span>Estimasi selesai: 2-3 jam</span>
                    </div>
                </div> --}}
                
                <button class="btn btn-refresh" onclick="refreshPage()">
                    <i class="fas fa-sync-alt me-2"></i>
                    Muat Ulang Halaman
                </button>
                
                <div class="mt-4">
                    <small class="text-muted">
                        Terakhir diperbarui: <span id="lastUpdate"></span>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var APP_URL = window.APP_URL || "/";
        // Update timestamp
        function updateTimestamp() {
            const now = new Date();
            const options = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric', 
                hour: '2-digit', 
                minute: '2-digit',
                timeZone: 'Asia/Jakarta'
            };
            document.getElementById('lastUpdate').textContent = now.toLocaleDateString('id-ID', options);
        }
        
        // Refresh page function
        function refreshPage() {
            const btn = document.querySelector('.btn-refresh');
            const icon = btn.querySelector('i');
            
            // Add spinning animation
            icon.classList.add('fa-spin');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-sync-alt fa-spin me-2"></i>Memuat Ulang...';
            
            // Simulate refresh delay
            setTimeout(() => {
                window.location.href = APP_URL;
            }, 1500);
        }
        
        // Initialize timestamp
        updateTimestamp();
        
        // Update timestamp every minute
        setInterval(updateTimestamp, 60000);
        
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Animate progress bar on load
            setTimeout(() => {
                document.querySelector('.progress-bar').style.width = '65%';
            }, 1000);
        });
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'98acfd62d2b25ffa',t:'MTc1OTgzNTgyMi4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
