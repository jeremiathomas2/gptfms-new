<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>Register | GPTFMS</title>
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
      /* user-select: none; */
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
      max-width: 520px;
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

    .login-btn {
      display: flex;
      align-items: center;
      padding: 8px 16px;
      background: #2563eb;
      color: white;
      border-radius: 8px;
      text-decoration: none;
      transition: background-color 0.2s;
    }

    .login-btn:hover {
      background: #1d4ed8;
    }

    .btn-icon {
      width: 16px;
      height: 16px;
      margin-right: 8px;
    }

    /* Register card header styling */
    .register-card-header {
      text-align: center;
      margin-bottom: 2rem;
      position: relative;
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.5s ease-out;
    }

    .register-card-header.fade-in {
      opacity: 1;
      transform: translateY(0);
    }

    .register-header-icon {
      width: 60px;
      height: 60px;
      background: linear-gradient(135deg, #667eea, #764ba2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1rem;
      box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .register-header-icon:hover {
      transform: scale(1.05);
      box-shadow: 0 12px 35px rgba(102, 126, 234, 0.5);
    }

    .register-header-icon i {
      font-size: 28px;
      color: white;
    }

    .register-title {
      font-size: 28px;
      font-weight: 700;
      background: linear-gradient(125deg, #667eea, #764ba2);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      margin: 0 0 0.5rem 0;
      letter-spacing: -0.5px;
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.5s ease-out;
    }

    .register-title.fade-in {
      opacity: 1;
      transform: translateY(0);
    }

    .register-subtitle {
      font-size: 14px;
      color: #8b9dc3;
      margin: 0;
      font-weight: 500;
      letter-spacing: 0.3px;
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.5s ease-out;
    }

    .register-subtitle.fade-in {
      opacity: 1;
      transform: translateY(0);
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

    /* Gender dropdown specific styling */
    select.input-field {
      color: #f0f3ff;
      background: rgba(255, 255, 255, 0.12);
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      cursor: pointer;
    }

    select.input-field option {
      background: #1a2332;
      color: #f0f3ff;
      padding: 0.8rem;
      border: none;
    }

    select.input-field option:hover,
    select.input-field option:checked {
      background: #667eea;
      color: white;
    }

    /* Advanced Custom Dropdown Styling */
    .custom-dropdown {
      position: relative;
      width: 100%;
    }

    .dropdown-selected {
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
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .dropdown-selected:hover {
      background: rgba(255, 255, 255, 0.18);
      border-color: rgba(255, 255, 255, 0.4);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(120, 160, 255, 0.2);
    }

    .dropdown-selected:focus,
    .dropdown-selected.active {
      background: rgba(255, 255, 255, 0.22);
      border-color: rgba(255, 255, 255, 0.7);
      box-shadow: 0 0 12px rgba(120, 160, 255, 0.4);
      outline: none;
    }

    .selected-text {
      flex: 1;
      text-align: left;
    }

    .dropdown-arrow {
      color: rgba(255, 255, 255, 0.7);
      font-size: 0.8rem;
      transition: transform 0.3s ease, color 0.2s ease;
    }

    .dropdown-selected.active .dropdown-arrow {
      transform: rotate(180deg);
      color: white;
    }

    .dropdown-options {
      position: absolute;
      top: calc(100% + 0.5rem);
      left: 0;
      right: 0;
      background: rgba(26, 35, 50, 0.95);
      backdrop-filter: blur(20px) saturate(180%);
      -webkit-backdrop-filter: blur(20px) saturate(180%);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 1.2rem;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1);
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px) scale(0.95);
      transition: all 0.3s cubic-bezier(0.2, 0, 0.2, 1);
      z-index: 1000;
      overflow: hidden;
    }

    .dropdown-options.show {
      opacity: 1;
      visibility: visible;
      transform: translateY(0) scale(1);
    }

    .dropdown-option {
      display: flex;
      align-items: center;
      padding: 1rem 1.2rem;
      color: rgba(240, 243, 255, 0.9);
      cursor: pointer;
      transition: all 0.2s ease;
      position: relative;
      overflow: hidden;
    }

    .dropdown-option::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.3), transparent);
      transition: left 0.4s ease;
    }

    .dropdown-option:hover::before {
      left: 100%;
    }

    .dropdown-option:hover {
      background: rgba(102, 126, 234, 0.2);
      color: white;
      padding-left: 1.5rem;
      transform: translateX(4px);
    }

    .dropdown-option.selected {
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: white;
      font-weight: 600;
    }

    .dropdown-option.selected::after {
      content: '✓';
      position: absolute;
      right: 1.2rem;
      color: white;
      font-weight: bold;
      font-size: 1.1rem;
    }

    .option-icon {
      margin-right: 0.8rem;
      font-size: 1.1rem;
      width: 1.2rem;
      text-align: center;
      color: rgba(255, 255, 255, 0.8);
      transition: color 0.2s ease;
    }

    .dropdown-option:hover .option-icon {
      color: white;
      transform: scale(1.1);
    }

    .dropdown-option.selected .option-icon {
      color: white;
    }

    .dropdown-option:first-child {
      border-radius: 1.2rem 1.2rem 0 0;
    }

    .dropdown-option:last-child {
      border-radius: 0 0 1.2rem 1.2rem;
    }

    /* Glass shine effect for dropdown */
    .dropdown-options::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 50%;
      height: 100%;
      background: linear-gradient(115deg, transparent, rgba(255, 255, 255, 0.1), transparent);
      transform: skewX(-15deg);
      transition: left 0.6s cubic-bezier(0.23, 1, 0.32, 1);
      pointer-events: none;
    }

    .dropdown-options.show::before {
      left: 125%;
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

    /* Role selection styling */
    .role-selection {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .role-option {
      flex: 1;
      position: relative;
    }

    .role-option input[type="radio"] {
      position: absolute;
      opacity: 0;
      cursor: pointer;
    }

    .role-label {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0.8rem;
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 255, 255, 0.25);
      border-radius: 1.2rem;
      cursor: pointer;
      transition: all 0.25s ease;
      color: rgba(255, 255, 255, 0.8);
      font-weight: 500;
      font-size: 0.9rem;
    }

    .role-option input[type="radio"]:checked + .role-label {
      background: linear-gradient(135deg, #667eea, #764ba2);
      border-color: rgba(255, 255, 255, 0.5);
      color: white;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .role-label:hover {
      background: rgba(255, 255, 255, 0.18);
      border-color: rgba(255, 255, 255, 0.4);
    }

    .role-option input[type="radio"]:checked + .role-label:hover {
      background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .role-icon {
      margin-right: 0.5rem;
      font-size: 1.1rem;
    }

    /* Two-column form fields */
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }

    /* register button */
    .register-btn {
      width: 100%;
      background: linear-gradient(95deg, #667eea, #764ba2);
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
      box-shadow: 0 8px 18px rgba(102, 126, 234, 0.3);
      backdrop-filter: blur(2px);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.6rem;
    }

    .register-btn i {
      font-size: 1.1rem;
      transition: transform 0.2s;
    }

    .register-btn:hover {
      background: linear-gradient(95deg, #764ba2, #667eea);
      transform: translateY(-2px);
      box-shadow: 0 14px 28px rgba(102, 126, 234, 0.4);
    }

    .register-btn:hover i {
      transform: translateX(4px);
    }

    .register-btn:active {
      transform: translateY(2px);
    }

    /* login link */
    .login-link {
      text-align: center;
      margin-top: 1.8rem;
      font-size: 0.85rem;
      color: rgba(235, 245, 255, 0.8);
    }

    .login-link a {
      color: #b8d0ff;
      text-decoration: none;
      font-weight: 600;
      margin-left: 0.3rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.3);
      transition: all 0.2s;
    }

    .login-link a:hover {
      color: white;
      border-bottom-color: white;
      transform: scale(1.05);
    }

    /* extra micro-interactions + responsive */
    @media (max-width: 550px) {
      .card-content {
        padding: 1.8rem 1.4rem 2rem;
      }
      .form-row {
        grid-template-columns: 1fr;
      }
      .glass-card {
        max-width: 95%;
      }
      .role-selection {
        flex-direction: column;
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

    /* Terms checkbox styling */
    .terms-group {
      display: flex;
      align-items: flex-start;
      margin: 1.2rem 0;
      font-size: 0.85rem;
      color: rgba(240, 245, 255, 0.85);
    }

    .terms-group input[type="checkbox"] {
      accent-color: #667eea;
      width: 1rem;
      height: 1rem;
      margin-right: 0.5rem;
      margin-top: 0.1rem;
      cursor: pointer;
    }

    .terms-group label {
      cursor: pointer;
      line-height: 1.4;
    }

    .terms-group a {
      color: #b8d0ff;
      text-decoration: none;
      border-bottom: 1px dotted rgba(255, 255, 255, 0.4);
      transition: all 0.2s;
    }

    .terms-group a:hover {
      color: white;
      border-bottom-color: white;
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
          <div class="header-logo-icon">
            <span class="header-logo-text">G</span>
          </div>
          <div class="brand-info">
            <h1 class="brand-title">GPTFMS</h1>
            <p class="brand-subtitle">Group project team formation Management System</p>
          </div>
        </div>
      </div>

      <!-- Login Button on Right -->
      <div class="header-right">
        <a href="{{ route('login') }}" class="login-btn">
          <i class="uil uil-sign-in-alt me-2"></i>
          <span>Sign In</span>
        </a>
      </div>
    </div>
  </header>

  <!-- Main Register Card with Liquid Glass Effect -->
  <div class="glass-card">
    <div class="card-content">
      <!-- Register Card Header -->
      <div class="register-card-header">
        <div class="register-header-icon">
          <i class="uil uil-user-plus"></i>
        </div>
        <h2 class="register-title">Create Account</h2>
        <p class="register-subtitle">Join the GPTFMS community</p>
      </div>

      <form class="register-form" action="{{ route('register') }}" method="POST" id="registerForm">
        @csrf
        
        @if ($errors->any())
            <div class="error-message" style="background: rgba(255, 100, 100, 0.1); border: 1px solid rgba(255, 100, 100, 0.3); border-radius: 8px; padding: 10px; margin-bottom: 20px; color: #ff6464; font-size: 13px;">
                <ul style="list-style: none;">
                    @foreach ($errors->all() as $error)
                        <li><i class="uil uil-exclamation-circle me-1"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="input-row">
          <div class="input-group">
            <input type="text" name="first_name" id="first_name" placeholder="First Name" required value="{{ old('first_name') }}">
            <i class="uil uil-user input-icon"></i>
          </div>
          <div class="input-group">
            <input type="text" name="last_name" id="last_name" placeholder="Last Name" required value="{{ old('last_name') }}">
            <i class="uil uil-user input-icon"></i>
          </div>
        </div>

        <div class="input-group">
          <input type="email" name="email" id="email" placeholder="Email Address" required value="{{ old('email') }}">
          <i class="uil uil-envelope input-icon"></i>
        </div>

        <div class="input-group">
          <input type="password" name="password" id="password" placeholder="Password" required>
          <i class="uil uil-lock input-icon"></i>
        </div>

        <div class="input-group">
            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" required>
            <i class="uil uil-shield-check input-icon"></i>
          </div>

        <button type="submit" class="submit-btn" id="registerBtn">
          <span class="btn-text">Create Account</span>
          <i class="uil uil-arrow-right"></i>
        </button>

        <div class="login-link">
          Already have an account? <a href="{{ route('login') }}">Sign In</a>
        </div>
      </form>
    </div>
  </div>

<script>
    (function() {
      // ---------- PASSWORD TOGGLE (Liquid glass usability) ----------
      const passwordInput = document.getElementById('password');
      const toggleIcon = document.getElementById('togglePasswordIcon');
      const confirmInput = document.getElementById('password_confirmation');
      const toggleIconConfirm = document.getElementById('togglePasswordIconConfirm');

      function setupPasswordToggle(input, icon) {
        if (icon && input) {
          icon.addEventListener('click', function(e) {
            e.preventDefault();
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            // toggle eye / eye-slash icon
            if (type === 'text') {
              icon.classList.remove('fa-eye');
              icon.classList.add('fa-eye-slash');
            } else {
              icon.classList.remove('fa-eye-slash');
              icon.classList.add('fa-eye');
            }
          });
        }
      }

      setupPasswordToggle(passwordInput, toggleIcon);
      setupPasswordToggle(confirmInput, toggleIconConfirm);

      // ---------- ADVANCED GENDER DROPDOWN ----------
      const genderDropdown = document.getElementById('genderDropdown');
      const genderSelected = document.getElementById('genderSelected');
      const genderOptions = document.getElementById('genderOptions');
      const genderInput = document.getElementById('gender');
      const genderOptionsList = genderOptions ? genderOptions.querySelectorAll('.dropdown-option') : [];

      function toggleGenderDropdown() {
        const isOpen = genderOptions.classList.contains('show');
        
        if (isOpen) {
          genderOptions.classList.remove('show');
          genderSelected.classList.remove('active');
        } else {
          genderOptions.classList.add('show');
          genderSelected.classList.add('active');
        }
      }

      function selectGenderOption(option) {
        const value = option.dataset.value;
        const text = option.querySelector('span').textContent;
        const icon = option.querySelector('i').className;
        
        // Update selected display
        genderSelected.querySelector('.selected-text').textContent = text;
        genderSelected.querySelector('.dropdown-arrow').className = 'fas fa-chevron-down dropdown-arrow';
        
        // Update hidden input
        genderInput.value = value;
        
        // Update selected state
        genderOptionsList.forEach(opt => opt.classList.remove('selected'));
        option.classList.add('selected');
        
        // Close dropdown
        genderOptions.classList.remove('show');
        genderSelected.classList.remove('active');
      }

      // Initialize dropdown
      if (genderDropdown && genderSelected && genderOptions && genderInput) {
        // Toggle dropdown on click
        genderSelected.addEventListener('click', function(e) {
          e.stopPropagation();
          toggleGenderDropdown();
        });

        // Handle option selection
        genderOptionsList.forEach(option => {
          option.addEventListener('click', function(e) {
            e.stopPropagation();
            selectGenderOption(this);
          });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
          if (!genderDropdown.contains(e.target)) {
            genderOptions.classList.remove('show');
            genderSelected.classList.remove('active');
          }
        });

        // Keyboard navigation
        genderSelected.addEventListener('keydown', function(e) {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            toggleGenderDropdown();
          } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (!genderOptions.classList.contains('show')) {
              toggleGenderDropdown();
            } else {
              // Focus next option
              const currentIndex = Array.from(genderOptionsList).findIndex(opt => opt.classList.contains('selected'));
              const nextIndex = (currentIndex + 1) % genderOptionsList.length;
              selectGenderOption(genderOptionsList[nextIndex]);
            }
          } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (!genderOptions.classList.contains('show')) {
              toggleGenderDropdown();
            } else {
              // Focus previous option
              const currentIndex = Array.from(genderOptionsList).findIndex(opt => opt.classList.contains('selected'));
              const prevIndex = currentIndex === 0 ? genderOptionsList.length - 1 : currentIndex - 1;
              selectGenderOption(genderOptionsList[prevIndex]);
            }
          } else if (e.key === 'Escape') {
            genderOptions.classList.remove('show');
            genderSelected.classList.remove('active');
          }
        });

        // Add hover effects
        genderOptionsList.forEach(option => {
          option.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(8px) scale(1.02)';
          });
          
          option.addEventListener('mouseleave', function() {
            if (!this.classList.contains('selected')) {
              this.style.transform = 'translateX(0) scale(1)';
            }
          });
        });
      }

      // ---------- ROLE SELECTION ----------
      const studentRadio = document.getElementById('student');
      const supervisorRadio = document.getElementById('supervisor');
      const registrationGroup = document.getElementById('registration-number-group');

      function updateRegistrationField() {
        if (studentRadio.checked) {
          registrationGroup.style.display = 'block';
          document.getElementById('registration_number').required = true;
        } else {
          registrationGroup.style.display = 'none';
          document.getElementById('registration_number').required = false;
        }
      }

      if (studentRadio && supervisorRadio && registrationGroup) {
        studentRadio.addEventListener('change', updateRegistrationField);
        supervisorRadio.addEventListener('change', updateRegistrationField);
        updateRegistrationField(); // Initialize on page load
      }

      // ---------- FORM SUBMISSION ----------
      const form = document.getElementById('registerForm');
      if (form) {
        form.addEventListener('submit', function(event) {
          const firstName = document.getElementById('first_name');
          const lastName = document.getElementById('last_name');
          const emailField = document.getElementById('email');
          const phoneField = document.getElementById('phone');
          const passwordField = document.getElementById('password');
          const confirmField = document.getElementById('password_confirmation');
          const termsField = document.getElementById('terms');
          
          const firstNameVal = firstName ? firstName.value.trim() : '';
          const lastNameVal = lastName ? lastName.value.trim() : '';
          const email = emailField ? emailField.value.trim() : '';
          const phone = phoneField ? phoneField.value.trim() : '';
          const password = passwordField ? passwordField.value : '';
          const confirm = confirmField ? confirmField.value : '';
          const terms = termsField ? termsField.checked : false;

          // Clear all existing error messages
          const validationMessage = document.getElementById('validation-message');
          const validationText = document.getElementById('validation-text');
          if (validationMessage && validationText) {
            validationMessage.classList.add('hidden');
            validationMessage.style.display = 'none';
            validationText.textContent = '';
          }
          
          // Clear any server error messages
          const serverErrors = document.querySelectorAll('.error-message');
          serverErrors.forEach(error => {
            if (!error.id || error.id !== 'validation-message') {
              error.remove();
            }
          });

          // Validate required fields
          if (firstNameVal === '' || lastNameVal === '' || email === '' || phone === '' || password === '' || confirm === '') {
            event.preventDefault();
            if (validationMessage && validationText) {
              validationMessage.classList.remove('hidden');
              validationMessage.style.display = 'block';
              validationText.textContent = 'Please fill in all required fields.';
            }
            // highlight empty fields
            if (firstNameVal === '') firstName.style.borderColor = 'rgba(255, 100, 100, 0.8)';
            if (lastNameVal === '') lastName.style.borderColor = 'rgba(255, 100, 100, 0.8)';
            if (email === '') emailField.style.borderColor = 'rgba(255, 100, 100, 0.8)';
            if (phone === '') phoneField.style.borderColor = 'rgba(255, 100, 100, 0.8)';
            if (password === '') passwordField.style.borderColor = 'rgba(255, 100, 100, 0.8)';
            if (confirm === '') confirmField.style.borderColor = 'rgba(255, 100, 100, 0.8)';
            setTimeout(() => {
              if (firstName) firstName.style.borderColor = '';
              if (lastName) lastName.style.borderColor = '';
              if (emailField) emailField.style.borderColor = '';
              if (phoneField) phoneField.style.borderColor = '';
              if (passwordField) passwordField.style.borderColor = '';
              if (confirmField) confirmField.style.borderColor = '';
            }, 1500);
            return;
          }

          // Validate email format
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailRegex.test(email)) {
            event.preventDefault();
            if (validationMessage && validationText) {
              validationMessage.classList.remove('hidden');
              validationMessage.style.display = 'block';
              validationText.textContent = 'Please provide a valid email address.';
            }
            if (emailField) emailField.style.borderColor = 'rgba(255, 100, 100, 0.8)';
            setTimeout(() => {
              if (emailField) emailField.style.borderColor = '';
            }, 1500);
            return;
          }

          // Validate password match
          if (password !== confirm) {
            event.preventDefault();
            if (validationMessage && validationText) {
              validationMessage.classList.remove('hidden');
              validationMessage.style.display = 'block';
              validationText.textContent = 'Password confirmation does not match.';
            }
            if (passwordField) passwordField.style.borderColor = 'rgba(255, 100, 100, 0.8)';
            if (confirmField) confirmField.style.borderColor = 'rgba(255, 100, 100, 0.8)';
            setTimeout(() => {
              if (passwordField) passwordField.style.borderColor = '';
              if (confirmField) confirmField.style.borderColor = '';
            }, 1500);
            return;
          }

          // Validate password length
          if (password.length < 8) {
            event.preventDefault();
            if (validationMessage && validationText) {
              validationMessage.classList.remove('hidden');
              validationMessage.style.display = 'block';
              validationText.textContent = 'Password must be at least 8 characters long.';
            }
            if (passwordField) passwordField.style.borderColor = 'rgba(255, 100, 100, 0.8)';
            setTimeout(() => {
              if (passwordField) passwordField.style.borderColor = '';
            }, 1500);
            return;
          }

          // Validate terms
          if (!terms) {
            event.preventDefault();
            if (validationMessage && validationText) {
              validationMessage.classList.remove('hidden');
              validationMessage.style.display = 'block';
              validationText.textContent = 'You must agree to the terms and conditions.';
            }
            return;
          }

          // Show loading state
          const btn = document.querySelector('.register-btn');
          const originalText = btn.innerHTML;
          btn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Creating Account...';
          btn.disabled = true;

          // Allow form to submit normally after validation
          setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
          }, 3000);
        });
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

      // Fade in animations
      setTimeout(() => {
        const header = document.querySelector('.register-card-header');
        if (header) {
          header.classList.add('fade-in');
        }
      }, 100);

      setTimeout(() => {
        const title = document.querySelector('.register-title');
        if (title) {
          title.classList.add('fade-in');
        }
      }, 200);

      setTimeout(() => {
        const subtitle = document.querySelector('.register-subtitle');
        if (subtitle) {
          subtitle.classList.add('fade-in');
        }
      }, 300);
    })();
</script>

</body>
</html>
