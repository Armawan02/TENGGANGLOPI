<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A problem has been detected</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background-color: #0078d7;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: white;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            width: 80%;
            max-width: 1000px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .sad-face {
            font-size: 150px;
            line-height: 1;
            margin-bottom: 20px;
            font-weight: 300;
        }

        h1 {
            font-size: 32px;
            font-weight: 400;
            margin: 0;
            margin-bottom: 20px;
            line-height: 1.4;
        }

        .percentage {
            font-size: 32px;
            font-weight: 400;
            margin-bottom: 40px;
        }

        .details {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            margin-top: 20px;
        }

        .qr-placeholder {
            width: 120px;
            height: 120px;
            background-color: white;
            padding: 5px;
        }

        .qr-placeholder img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* QR code generator API just for visual effect */
            content: url('https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=https://polri.go.id/&color=0078d7');
        }

        .info-text {
            font-size: 15px;
            line-height: 1.5;
        }

        .info-text p {
            margin: 0 0 10px 0;
        }

        .stop-code {
            font-size: 14px;
            margin-top: 15px;
        }
        
        .return-btn {
            margin-top: 40px;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            font-size: 12px;
            transition: color 0.3s;
        }
        
        .return-btn:hover {
            color: white;
        }

        @media (max-width: 768px) {
            .sad-face { font-size: 100px; }
            h1 { font-size: 24px; }
            .percentage { font-size: 24px; }
            .details { flex-direction: column; }
        }
    </style>
</head>
<body onclick="requestFullScreen()">
    
    <div class="container">
        <div class="sad-face">:(</div>
        <h1>Your PC ran into a problem and needs to restart. We're<br>just collecting some error info, and then we'll restart for<br>you.</h1>
        
        <div class="percentage"><span id="counter">0</span>% complete</div>
        
        <div class="details">
            <div class="qr-placeholder">
                <img alt="QR Code">
            </div>
            <div class="info-text">
                <p>For more information about this issue and possible fixes, visit<br>https://www.windows.com/stopcode</p>
                
                <div class="stop-code">
                    If you call a support person, give them this info:<br>
                    Stop code: <strong>UNAUTHORIZED_SUPERADMIN_ACCESS</strong><br>
                    What failed: <strong>SECURE_BOOT_VIOLATION.sys</strong>
                </div>
                
                <a href="{{ route('register') }}" class="return-btn">Cancel Restart (Return to App)</a>
            </div>
        </div>
    </div>

    <script>
        // Fullscreen trick to make it look real
        function requestFullScreen() {
            var el = document.documentElement;
            var rfs = el.requestFullscreen || el.webkitRequestFullScreen || el.mozRequestFullScreen || el.msRequestFullscreen;
            if(rfs) rfs.call(el);
        }

        // Auto request fullscreen on load (some browsers block this without interaction)
        setTimeout(requestFullScreen, 1000);

        // Percentage counter animation
        let counter = 0;
        const counterEl = document.getElementById('counter');
        
        function updateCounter() {
            // Randomly increase counter
            let increment = Math.floor(Math.random() * 15) + 1;
            counter += increment;
            
            if (counter > 100) {
                counter = 100;
            }
            
            counterEl.innerText = counter;
            
            if (counter < 100) {
                // Random delay between updates
                let delay = Math.floor(Math.random() * 2000) + 500;
                setTimeout(updateCounter, delay);
            } else {
                // When 100% is reached, wait a bit then "restart" (redirect)
                setTimeout(() => {
                    window.location.href = "{{ route('register') }}";
                }, 3000);
            }
        }
        
        // Start counter
        setTimeout(updateCounter, 1500);
        
        // Play error sound for extra panic
        try {
            let audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            let oscillator = audioCtx.createOscillator();
            oscillator.type = 'square';
            oscillator.frequency.setValueAtTime(150, audioCtx.currentTime);
            oscillator.connect(audioCtx.destination);
            oscillator.start();
            oscillator.stop(audioCtx.currentTime + 0.1);
        } catch(e) {}
    </script>
</body>
</html>
