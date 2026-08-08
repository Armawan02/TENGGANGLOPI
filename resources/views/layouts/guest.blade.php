<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TENGGANGLOPI') }}</title>
    
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --bg-light: #f8fafc;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --danger: #ef4444;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Outfit', sans-serif; }
        
        body {
            background-color: var(--bg-light);
            background-image: linear-gradient(rgba(15, 23, 42, 0.7), rgba(15, 23, 42, 0.8)), url('{{ asset("bg-tengganglopi.jpg") }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: var(--text-dark);
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }

        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-container img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .logo-container h2 {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 5px;
        }
        .logo-container p {
            font-size: 14.5px;
            color: var(--text-muted);
        }

        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 14.5px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            font-size: 15px;
            color: var(--text-dark);
            transition: all 0.3s;
            outline: none;
            background-color: #f8fafc;
        }
        .form-group input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            background-color: #ffffff;
        }
        
        .btn-primary {
            width: 100%;
            background-color: var(--primary);
            color: #fff;
            padding: 14px;
            border-radius: 12px;
            border: none;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
            margin-top: 10px;
        }
        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }

        .auth-links {
            text-align: center;
            margin-top: 25px;
            font-size: 14.5px;
            color: var(--text-muted);
        }
        .auth-links a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        .auth-links a:hover {
            text-decoration: underline;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
        }
        .checkbox-group input {
            width: 18px; height: 18px; cursor: pointer;
            accent-color: var(--primary);
        }
        .checkbox-group label { margin-bottom: 0; font-weight: 500; font-size: 14.5px; cursor: pointer; color: var(--text-dark); }
        
        /* Session Alert */
        .session-alert {
            background-color: #ecfdf5;
            color: #059669;
            border-left: 4px solid #10b981;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="logo-container">
            <a href="/">
                <img src="{{ asset('logo.png') }}" alt="Logo TENGGANGLOPI">
            </a>
            <h2>TENGGANGLOPI</h2>
            <p>{{ $title ?? 'Portal Akses Base Station' }}</p>
        </div>

        {{ $slot }}
    </div>
</body>
</html>
