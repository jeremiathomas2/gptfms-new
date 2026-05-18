<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>Welcome | GPTFMS Login</title>
  <!-- Phoenix Template Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
  <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <!-- Phoenix Template Icon Fonts -->
  <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      /* user-select: none; */ /* Removed for accessibility and testing */
    }

    body {
      min-height: 100vh;
      background: radial-gradient(circle at 30% 10%, #0a0c1a, #03050b);
      font-family: 'Nunito Sans', sans-serif;
      display: flex;
      align-items: flex-start;
      justify-content: center;
      position: relative;
      overflow-x: hidden;
      padding: 1.5rem;
      padding-top: 180px;
      margin: 0;
      box-sizing: border-box;
      color: #f0f3ff;
    }

    /* ========= LIQUID BLOBS (ANIMATED BACKGROUND) ========= */
    .blob-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 0;
      overflow: hidden;
      pointer-events: none;
    }

    .blob {
      position: absolute;
      border-radius: 50%;
      filter: blur(70px);
      opacity: 0.65;
      animation: floatBlob 22s infinite alternate ease-in-out;
    }

    .blob-1 {
      width: 55vw;
      height: 55vw;
      background: radial-gradient(circle at 30% 30%, rgba(88, 130, 255, 0.7), rgba(30, 80, 220, 0.3));
      top: -20vh;
      left: -15vw;
      animation-duration: 24s;
      animation-delay: 0s;
    }

    .blob-2 {
      width: 65vw;
      height: 65vw;
      background: radial-gradient(circle at 70% 60%, rgba(255, 90, 180, 0.6), rgba(160, 50, 220, 0.4));
      bottom: -35vh;
      right: -20vw;
      animation-duration: 28s;
      animation-delay: 2s;
    }

    .blob-3 {
      width: 45vw;
      height: 45vw;
      background: radial-gradient(circle at 40% 50%, rgba(0, 210, 200, 0.6), rgba(40, 170, 220, 0.5));
      top: 40vh;
      left: 55vw;
      animation-duration: 20s;
      animation-delay: 5s;
      filter: blur(80px);
    }

    .blob-4 {
      width: 40vw;
      height: 40vw;
      background: radial-gradient(circle at 80% 20%, rgba(255, 180, 70, 0.55), rgba(255, 80, 120, 0.4));
      bottom: 10vh;
      left: -10vw;
      animation-duration: 26s;
      animation-delay: 1s;
    }

    @keyframes floatBlob {
      0% {
        transform: translate(0, 0) scale(1);
      }
      40% {
        transform: translate(5%, 8%) scale(1.08);
      }
      70% {
        transform: translate(-4%, 3%) scale(0.95);
      }
      100% {
        transform: translate(7%, -6%) scale(1.12);
      }
    }

    /* ========= MAIN CARD – LIQUID GLASS EFFECT ========= */
    .glass-card {
      position: relative;
      z-index: 20;
      width: 100%;
      max-width: 480px;
      margin-top: 0;
      background: rgba(20, 30, 55, 0.28);
      backdrop-filter: blur(16px) saturate(180%);
      -webkit-backdrop-filter: blur(16px) saturate(180%);
      border-radius: 2rem;
      border: 1px solid rgba(255, 255, 255, 0.35);
      box-shadow: 0 30px 50px rgba(0, 0, 0, 0.3), inset 0 1px 1px rgba(255, 255, 255, 0.2);
      transition: transform 0.3s ease, box-shadow 0.4s ease;
      overflow: hidden;
    }

    /* Liquid glass shine overlay (moving reflection) */
    .glass-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: -75%;
      width: 50%;
      height: 100%;
      background: linear-gradient(115deg, transparent, rgba(255, 255, 255, 0.25), transparent);
      transform: skewX(-15deg);
      transition: left 0.6s cubic-bezier(0.23, 1, 0.32, 1);
      pointer-events: none;
    }

    .glass-card:hover::before {
      left: 125%;
    }

    /* inner content */
    .card-content {
      padding: 2.2rem 2rem 2.5rem;
      position: relative;
      z-index: 2;
    }

    /* Full width header container */
    .full-width-header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      width: 100%;
      min-width: 100vw;
      z-index: 25;
      background: rgba(20, 30, 55, 0.28);
      backdrop-filter: blur(16px) saturate(180%);
      -webkit-backdrop-filter: blur(16px) saturate(180%);
      border-radius: 0 0 2rem 2rem;
      border: 1px solid rgba(255, 255, 255, 0.35);
      border-top: none;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), inset 0 1px 1px rgba(255, 255, 255, 0.2);
      transition: transform 0.3s ease, box-shadow 0.4s ease;
      overflow: hidden;
      box-sizing: border-box;
    }

    /* Liquid glass shine overlay for header */
    .full-width-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: -75%;
      width: 50%;
      height: 100%;
      background: linear-gradient(115deg, transparent, rgba(255, 255, 255, 0.25), transparent);
      transform: skewX(-15deg);
      transition: left 0.6s cubic-bezier(0.23, 1, 0.32, 1);
      pointer-events: none;
    }

    .full-width-header:hover::before {
      left: 125%;
    }

    /* Header content layout */
    .header-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
      height: 64px;
      padding: 0 1rem;
      max-width: 100%;
    }

    @media (min-width: 640px) {
      .header-content {
        padding: 0 1.5rem;
      }
    }

    @media (min-width: 1024px) {
      .header-content {
        padding: 0 2rem;
      }
    }

    /* Header left side */
    .header-left {
      display: flex;
      align-items: center;
    }

    .logo-container {
      display: flex;
      align-items: center;
      justify-content: flex-start;
    }

    .header-logo-icon {
      width: 48px;
      height: 48px;
      background: linear-gradient(135deg, #031969, #4285f4);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 16px;
      box-shadow: 0 6px 20px rgba(3, 25, 105, 0.3);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .header-logo-icon:hover {
      transform: scale(1.05);
      box-shadow: 0 8px 25px rgba(3, 25, 105, 0.4);
    }

    .header-logo-text {
      color: white;
      font-weight: bold;
      font-size: 24px;
      text-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
    }

    .brand-info h1 {
      font-size: 20px;
      font-weight: bold;
      background: linear-gradient(125deg, #4285f4, #71d5f6);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      margin: 0;
      letter-spacing: -0.5px;
    }

    .brand-subtitle {
      font-size: 12px;
      color: #ffffff;
      margin: 0;
      font-weight: 500;
      letter-spacing: 0.3px;
    }

    /* Header right side */
    .header-right {
      display: flex;
      align-items: center;
    }

    .register-btn {
      display: flex;
      align-items: center;
      padding: 8px 16px;
      background: #2563eb;
      color: white;
      border-radius: 8px;
      text-decoration: none;
      transition: background-color 0.2s;
    }

    .register-btn:hover {
      background: #1d4ed8;
    }

    .btn-icon {
      width: 16px;
      height: 16px;
      margin-right: 8px;
    }

    /* Login card header styling */
    .login-card-header {
      text-align: center;
      margin-bottom: 2rem;
      position: relative;
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.5s ease-out;
    }

    .login-card-header.fade-in {
      opacity: 1;
      transform: translateY(0);
    }

    .login-header-icon {
      width: 60px;
      height: 60px;
      background: linear-gradient(135deg, #031969, #4285f4);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1rem;
      box-shadow: 0 8px 25px rgba(3, 25, 105, 0.3);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .login-header-icon:hover {
      transform: scale(1.05);
      box-shadow: 0 12px 35px rgba(3, 25, 105, 0.4);
    }

    .login-header-icon i {
      font-size: 28px;
      color: white;
    }

    .login-title {
      font-size: 28px;
      font-weight: 700;
      background: linear-gradient(125deg, #031969, #4285f4);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      margin: 0 0 0.5rem 0;
      letter-spacing: -0.5px;
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.5s ease-out;
    }

    .login-title.fade-in {
      opacity: 1;
      transform: translateY(0);
    }

    .login-subtitle {
      font-size: 14px;
      color: #8b9dc3;
      margin: 0;
      font-weight: 500;
      letter-spacing: 0.3px;
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.5s ease-out;
    }

    .login-subtitle.fade-in {
      opacity: 1;
      transform: translateY(0);
    }

    /* Registration section styling */
    #register-header {
      text-align: center;
      margin-bottom: 2rem;
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.5s ease-out;
    }

    #register-header.show {
      opacity: 1;
      transform: translateY(0);
    }

    .register-g-icon {
      width: 48px;
      height: 48px;
      background: linear-gradient(135deg, #667eea, #764ba2);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 1rem;
      box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }

    .register-g-text {
      color: white;
      font-weight: bold;
      font-size: 24px;
      text-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
    }

    .register-title {
      font-size: 32px;
      font-weight: bold;
      background: linear-gradient(125deg, #ffffff, #f0f3ff);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      margin: 0 0 1rem 0;
      letter-spacing: -0.5px;
    }

    .register-subtitle {
      font-size: 14px;
      color: #e0e7ff;
      margin: 0 0 1.5rem 0;
      font-weight: 500;
    }

    .register-card {
      background: rgba(20, 30, 55, 0.28);
      backdrop-filter: blur(16px) saturate(180%);
      -webkit-backdrop-filter: blur(16px) saturate(180%);
      border-radius: 2rem;
      border: 1px solid rgba(255, 255, 255, 0.35);
      box-shadow: 0 30px 50px rgba(0, 0, 0, 0.3), inset 0 1px 1px rgba(255, 255, 255, 0.2);
      transition: transform 0.3s ease, box-shadow 0.4s ease;
      overflow: hidden;
      padding: 2rem;
      max-width: 400px;
      margin: 0 auto;
    }

    .register-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: -75%;
      width: 50%;
      height: 100%;
      background: linear-gradient(115deg, transparent, rgba(255, 255, 255, 0.25), transparent);
      transform: skewX(-15deg);
      transition: left 0.6s cubic-bezier(0.23, 1, 0.32, 1);
      pointer-events: none;
    }

    .register-card:hover::before {
      left: 125%;
    }

    .register-link {
      color: #b8d0ff;
      text-decoration: none;
      font-weight: 600;
      margin-left: 0.3rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.3);
      transition: all 0.2s;
    }

    .register-link:hover {
      color: white;
      border-bottom-color: white;
      transform: scale(1.05);
    }

    /* brand / logo area */
    .login-header {
      text-align: center;
      margin-bottom: 0;
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      max-width: 1200px;
      margin-left: auto;
      margin-right: auto;
    }

    .logo-icon {
      font-size: 2.8rem;
      background: linear-gradient(135deg, #fff, #b3cdff);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      margin-bottom: 0.5rem;
      display: block;
      width: 100%;
      text-align: center;
    }

    /* form group styling */
    .input-group {
      margin-bottom: 1.5rem;
      position: relative;
    }

    .input-icon {
      position: absolute;
      left: 1.2rem;
      top: 50%;
      transform: translateY(-50%);
      color: rgba(255, 255, 255, 0.7);
      font-size: 1.1rem;
      transition: color 0.2s;
      pointer-events: none;
    }

    .input-field {
      width: 100%;
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 255, 255, 0.25);
      border-radius: 2.2rem;
      padding: 0.9rem 1rem 0.9rem 2.8rem;
      font-size: 1rem;
      font-weight: 500;
      font-family: 'Inter', sans-serif;
      color: #f0f3ff;
      backdrop-filter: blur(4px);
      outline: none;
      transition: all 0.25s ease;
    }

    /* specific for password to have right padding for eye */
    .password-wrapper .input-field {
      padding-right: 3rem;
    }

    .input-field::placeholder {
      color: rgba(210, 225, 255, 0.6);
      font-weight: 400;
      font-size: 0.9rem;
    }

    .input-field:focus {
      background: rgba(255, 255, 255, 0.22);
      border-color: rgba(255, 255, 255, 0.7);
      box-shadow: 0 0 12px rgba(120, 160, 255, 0.4);
      outline: none;
    }

    /* password eye toggle */
    .toggle-password {
      position: absolute;
      right: 1.2rem;
      top: 50%;
      transform: translateY(-50%);
      color: rgba(255, 255, 255, 0.7);
      cursor: pointer;
      font-size: 1.1rem;
      transition: color 0.2s;
      z-index: 5;
    }

    .toggle-password:hover {
      color: white;
    }

    /* remember & forgot row */
    .options-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 1.2rem 0 1.8rem;
      font-size: 0.85rem;
    }

    .checkbox {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: rgba(240, 245, 255, 0.85);
      cursor: pointer;
    }

    .checkbox input {
      accent-color: #5d8eff;
      width: 1rem;
      height: 1rem;
      cursor: pointer;
    }

    .forgot-link {
      color: #b8d0ff;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.2s;
      border-bottom: 1px dotted rgba(255, 255, 255, 0.4);
    }

    .forgot-link:hover {
      color: white;
      border-bottom-color: white;
    }

    /* login button — Google colors & liquid hover */
    .login-btn {
      width: 100%;
      background: linear-gradient(95deg, #4285f4, #031969);
      border: none;
      border-radius: 3rem;
      padding: 0.95rem;
      font-size: 1rem;
      font-weight: 600;
      font-family: 'Inter', sans-serif;
      color: white;
      letter-spacing: 0.5px;
      cursor: pointer;
      transition: all 0.35s ease;
      box-shadow: 0 8px 18px rgba(52, 168, 83, 0.2);
      backdrop-filter: blur(2px);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.6rem;
    }

    .login-btn i {
      font-size: 1.1rem;
      transition: transform 0.2s;
    }

    .login-btn:hover {
      background: linear-gradient(95deg, #031969, #4285f4);
      transform: translateY(-2px);
      box-shadow: 0 14px 28px rgba(15, 157, 236, 0.3);
    }

    .login-btn:hover i {
      transform: translateX(4px);
    }

    .login-btn:active {
      transform: translateY(2px);
    }

    /* divider */
    .divider {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin: 1.8rem 0 1.2rem;
      color: rgba(255, 255, 255, 0.5);
      font-size: 0.75rem;
    }

    .divider-line {
      flex: 1;
      height: 1px;
      background: rgba(255, 255, 255, 0.25);
    }

    /* social icons */
    .social-icons {
      display: flex;
      justify-content: center;
      gap: 1.5rem;
      margin-top: 0.8rem;
    }

    .social-icon {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(5px);
      width: 44px;
      height: 44px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
      color: #eef3ff;
      transition: all 0.25s;
      border: 1px solid rgba(255, 255, 255, 0.2);
      cursor: pointer;
      text-decoration: none;
    }

    .social-icon:hover {
      background: rgba(255, 255, 255, 0.25);
      transform: translateY(-3px);
      border-color: rgba(255, 255, 255, 0.6);
      color: white;
    }

    /* login footer / signup link */
    .login-footer {
      text-align: center;
      margin-top: 1.8rem;
      font-size: 0.85rem;
      color: rgba(235, 245, 255, 0.85);
    }

    .login-footer .register-link {
      color: #cae0ff;
      text-decoration: none;
      font-weight: 600;
      margin-left: 0.3rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.3);
      transition: all 0.2s;
    }

    .login-footer .register-link:hover {
      color: white;
      border-bottom-color: white;
      transform: scale(1.05);
    }

    /* extra micro-interactions + responsive */
    @media (max-width: 550px) {
      .card-content {
        padding: 1.8rem 1.4rem 2rem;
      }
      h1 {
        font-size: 1.8rem;
      }
      .input-field {
        padding: 0.8rem 1rem 0.8rem 2.5rem;
      }
      .glass-card {
        max-width: 95%;
      }
    }

    /* subtle pulse on card for extra liquid shine */
    @keyframes subtleGlow {
      0% {
        box-shadow: 0 25px 40px rgba(0, 0, 0, 0.2), inset 0 1px 1px rgba(255, 255, 255, 0.2);
      }
      100% {
        box-shadow: 0 30px 50px rgba(0, 0, 0, 0.35), inset 0 1px 2px rgba(255, 255, 255, 0.3);
      }
    }
    .glass-card {
      animation: subtleGlow 3s infinite alternate ease-in-out;
    }

    /* no text selection on icons, but okay */
    .fa-eye, .fa-eye-slash {
      pointer-events: auto;
    }

    /* Error message styling for liquid glass theme */
    .error-message {
      background: rgba(255, 100, 100, 0.15);
      backdrop-filter: blur(4px);
      border: 1px solid rgba(255, 100, 100, 0.3);
      border-radius: 1rem;
      padding: 0.8rem;
      margin-bottom: 1rem;
      color: #ffcccc;
      font-size: 0.85rem;
      animation: fadeInUp 0.3s ease-out;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>

  <!-- Liquid animated background blobs -->
  <div class="blob-container">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
    <div class="blob blob-4"></div>
  </div>

  <!-- Full Width Header -->
  <header class="full-width-header">
    <div class="header-content">
      <!-- System Name on Left -->
      <div class="header-left">
        <div class="logo-container">
          <div class="header-logo-icon"><span class="header-logo-text">G</span></div>
          <div class="brand-info">
            <h1 class="brand-title">GPTFMS</h1>
            <p class="brand-subtitle">Group project team formation Management System</p>
          </div>
        </div>
      </div>

      <!-- Register Button on Right -->
      <div class="header-right">
        <a href="{{ route('register') }}" class="register-btn">
          <i class="uil uil-user-plus me-2"></i>
          <span>Register</span>
        </a>
      </div>
    </div>
  </header>

  <!-- Main Login Card with Liquid Glass Effect -->
  <div class="glass-card">
    <div class="card-content">
      <!-- Login Card Header -->
      <div class="login-card-header">
        <div class="login-header-icon">
          <i class="uil uil-user-circle"></i>
        </div>
        <h2 class="login-title">Welcome Back</h2>
        <p class="login-subtitle">Sign in to your GPTFMS account</p>
      </div>

      <!-- login form -->
      <form class="login-form" action="{{ route('login') }}" method="POST" id="loginForm">
        @csrf
        <input type="hidden" name="form_type" value="login">
        
        @if ($errors->any())
            <div class="error-message" style="background: rgba(255, 100, 100, 0.1); border: 1px solid rgba(255, 100, 100, 0.3); border-radius: 8px; padding: 10px; margin-bottom: 20px; color: #ff6464; font-size: 13px;">
                <ul style="list-style: none;">
                    @foreach ($errors->all() as $error)
                        <li><i class="uil uil-exclamation-circle me-1"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if(session('error'))
            <div class="error-message">
                <div class="text-sm">
                    <p>{{ session('error') }}</p>
                </div>
            </div>
        @endif
        

        <!-- Client-side validation message -->
        <div id="validation-message" class="error-message hidden" style="display: none;">
            <div class="text-sm">
                <p id="validation-text"></p>
            </div>
        </div>

        <!-- email / username -->
        <div class="input-group">
          <input type="email" class="input-field" id="email" name="email" placeholder="Email Address" required autocomplete="email" value="{{ old('email') }}">
          <i class="uil uil-envelope input-icon"></i>
        </div>

        <!-- password field + eye toggle -->
        <div class="input-group password-wrapper">
          <input type="password" class="input-field" id="password" name="password" placeholder="Password" required autocomplete="current-password">
          <i class="uil uil-lock input-icon"></i>
          <i class="uil uil-eye-slash toggle-password" id="togglePasswordIcon"></i>
        </div>

        <!-- options row -->
        <div class="options-row">
          <label class="checkbox">
            <input type="checkbox" id="remember-me" name="remember" {{ old('remember') ? 'checked' : '' }}> <span>Remember me</span>
          </label>
          <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
        </div>

        <!-- login button -->
        <button type="submit" class="login-btn" id="loginBtn">
          <span class="btn-text">Sign In</span>
          <i class="uil uil-arrow-right"></i>
        </button>

        <!-- footer / link to register -->
        <div class="login-footer">
          Don't have an account? <a href="{{ route('register') }}" class="register-link">Create Account</a>
        </div>
      </form>
    </div>
  </div>

<script>
    (function() {
      // ---------- PASSWORD TOGGLE (Liquid glass usability) ----------
      const passwordInput = document.getElementById('password');
      const toggleIcon = document.getElementById('togglePasswordIcon');

      if (toggleIcon && passwordInput) {
        toggleIcon.addEventListener('click', function(e) {
          e.preventDefault();
          const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
          passwordInput.setAttribute('type', type);
          // toggle eye / eye-slash icon
          if (type === 'text') {
            toggleIcon.classList.remove('uil-eye-slash');
            toggleIcon.classList.add('uil-eye');
          } else {
            toggleIcon.classList.remove('uil-eye');
            toggleIcon.classList.add('uil-eye-slash');
          }
        });
      }

      // ---------- FORM SUBMISSION (Preserve existing functionality) ----------
      const form = document.getElementById('loginForm');
      if (form) {
        form.addEventListener('submit', function(event) {
          const emailField = document.getElementById('email');
          const passwordField = document.getElementById('password');
          const email = emailField ? emailField.value.trim() : '';
          const pwd = passwordField ? passwordField.value : '';

          // Clear all existing error messages
          const validationMessage = document.getElementById('validation-message');
          const validationText = document.getElementById('validation-text');
          if (validationMessage && validationText) {
            validationMessage.classList.add('hidden');
            validationText.textContent = '';
          }
          
          // Clear any server error messages
          const serverErrors = document.querySelectorAll('.error-message');
          serverErrors.forEach(error => {
            if (!error.id || error.id !== 'validation-message') {
              error.remove();
            }
          });

          // Validate required fields before submission
          if (email === '' || pwd === '') {
            event.preventDefault();
            if (validationMessage && validationText) {
              validationMessage.classList.remove('hidden');
              validationMessage.style.display = 'block';
              validationText.textContent = 'Please fill in all required fields.';
            }
            // highlight empty fields
            if (email === '') emailField.style.borderColor = 'rgba(255, 100, 100, 0.8)';
            if (pwd === '') passwordField.style.borderColor = 'rgba(255, 100, 100, 0.8)';
            setTimeout(() => {
              if (emailField) emailField.style.borderColor = '';
              if (passwordField) passwordField.style.borderColor = '';
            }, 1500);
            return;
          }

          // Show loading state
          const btn = document.querySelector('.login-btn');
          const originalText = btn.innerHTML;
          btn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Authenticating...';
          btn.disabled = true;

          // Allow form to submit normally after validation
          setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
          }, 3000);
        });
      }

      // Forgot password link demo
      const forgotLink = document.querySelector('.forgot-link');
      if (forgotLink) {
        // forgotLink.addEventListener('click', (e) => {
        //   e.preventDefault();
        //   // You can implement actual forgot password functionality here
        //   alert('This feature coming soon ..');
        // });
      }

      // Additional dynamic liquid glass: micro movement on card (parallax light)
      const card = document.querySelector('.glass-card');
      if (card) {
        document.addEventListener('mousemove', function(e) {
          // subtle rotation / tilt effect to enhance glass depth
          const mouseX = e.clientX / window.innerWidth;
          const mouseY = e.clientY / window.innerHeight;
          const rotateY = (mouseX - 0.5) * 3;
          const rotateX = (mouseY - 0.5) * -2;
          card.style.transform = `perspective(1200px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-2px)`;
          card.style.transition = 'transform 0.2s cubic-bezier(0.2, 0.9, 0.4, 1.1)';
        });

        document.addEventListener('mouseleave', () => {
          card.style.transform = 'perspective(1200px) rotateX(0deg) rotateY(0deg) translateY(0px)';
        });
      }

      // Preserve existing form submission functionality
      const loginFormElement = document.querySelector('form[method="POST"]');
      if (loginFormElement) {
        loginFormElement.addEventListener('submit', function(e) {
          // Allow normal form submission to Laravel backend
          // The validation above will prevent submission if needed
        });
      }
    })();

    // Preserve existing JavaScript functions
    function togglePassword(fieldId) {
      const field = document.getElementById(fieldId);
      const eye = document.getElementById('togglePasswordIcon');
      
      if (field.type === 'password') {
        field.type = 'text';
        eye.classList.remove('fa-eye');
        eye.classList.add('fa-eye-slash');
      } else {
        field.type = 'password';
        eye.classList.remove('fa-eye-slash');
        eye.classList.add('fa-eye');
      }
    }

    function submitLoginForm() {
      // This function is preserved for compatibility but the main form handles submission
      const form = document.getElementById('loginForm');
      if (form) {
        form.submit();
      }
    }

    // Toggle registration section visibility
    function toggleRegister() {
      const loginCard = document.querySelector('.glass-card');
      const registerSection = document.getElementById('register-section');
      
      if (registerSection) {
        if (registerSection.style.display === 'none' || registerSection.style.display === '') {
          // Show registration section
          registerSection.style.display = 'block';
          registerSection.classList.remove('hidden');
          
          // Hide login card
          loginCard.style.display = 'none';
          
          // Show registration header animation
          const registerHeader = document.getElementById('register-header');
          if (registerHeader) {
            registerHeader.classList.add('show');
          }
        } else {
          // Hide registration section
          registerSection.style.display = 'none';
          registerSection.classList.add('hidden');
          
          // Show login card
          loginCard.style.display = 'block';
          
          // Hide registration header animation
          const registerHeader = document.getElementById('register-header');
          if (registerHeader) {
            registerHeader.classList.remove('show');
          }
        }
      }
    }

    // Fade in login card header elements
    function animateLoginHeader() {
      setTimeout(() => {
        const loginCardHeader = document.querySelector('.login-card-header');
        const loginTitle = document.querySelector('.login-title');
        const loginSubtitle = document.querySelector('.login-subtitle');
        
        if (loginCardHeader) {
          loginCardHeader.classList.add('fade-in');
        }
        if (loginTitle) {
          loginTitle.classList.add('fade-in');
        }
        if (loginSubtitle) {
          loginSubtitle.classList.add('fade-in');
        }
      }, 300);
    }

    // Initialize animations on page load
    document.addEventListener('DOMContentLoaded', function() {
      animateLoginHeader();
    });
  </script>
</body>
</html>
