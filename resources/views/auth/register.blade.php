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
      user-select: none;
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
      0% { transform: translate(0, 0) scale(1); }
      40% { transform: translate(5%, 8%) scale(1.08); }
      70% { transform: translate(-4%, 3%) scale(0.95); }
      100% { transform: translate(7%, -6%) scale(1.12); }
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
      animation: subtleGlow 3s infinite alternate ease-in-out;
    }

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

    .glass-card:hover::before { left: 125%; }

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

    .full-width-header:hover::before { left: 125%; }

    .header-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
      height: 64px;
      padding: 0 1rem;
      max-width: 100%;
    }

    @media (min-width: 640px) { .header-content { padding: 0 1.5rem; } }
    @media (min-width: 1024px) { .header-content { padding: 0 2rem; } }

    .header-left { display: flex; align-items: center; }
    .logo-container { display: flex; align-items: center; justify-content: flex-start; }

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

    .header-logo-text { color: white; font-weight: bold; font-size: 24px; text-shadow: 0 2px 8px rgba(0, 0, 0, 0.4); }

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

    .brand-subtitle { font-size: 12px; color: #ffffff; margin: 0; font-weight: 500; letter-spacing: 0.3px; }

    .header-right { display: flex; align-items: center; }

    .login-btn {
      display: flex; align-items: center; padding: 8px 16px; background: #2563eb; color: white; border-radius: 8px; text-decoration: none; transition: background-color 0.2s;
    }

    .login-btn:hover { background: #1d4ed8; }

    .register-card-header { text-align: center; margin-bottom: 2rem; position: relative; }

    .register-header-icon {
      width: 60px; height: 60px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }

    .register-header-icon i { font-size: 28px; color: white; }

    .register-title {
      font-size: 28px; font-weight: 700; background: linear-gradient(125deg, #667eea, #764ba2); -webkit-background-clip: text; background-clip: text; color: transparent; margin: 0 0 0.5rem 0; letter-spacing: -0.5px;
    }

    .register-subtitle { font-size: 14px; color: #8b9dc3; margin: 0; font-weight: 500; letter-spacing: 0.3px; }

    .input-group { margin-bottom: 1.5rem; position: relative; }

    .input-icon {
      position: absolute; left: 1.2rem; top: 50%; transform: translateY(-50%); color: rgba(255, 255, 255, 0.7); font-size: 1.1rem; transition: color 0.2s; pointer-events: none;
    }

    .input-field {
      width: 100%; background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25); border-radius: 2.2rem; padding: 0.9rem 1rem 0.9rem 2.8rem; font-size: 1rem; font-weight: 500; font-family: 'Nunito Sans', sans-serif; color: #f0f3ff; backdrop-filter: blur(4px); outline: none; transition: all 0.25s ease;
    }

    .input-field::placeholder { color: rgba(210, 225, 255, 0.6); font-weight: 400; font-size: 0.9rem; }

    .input-field:focus { background: rgba(255, 255, 255, 0.22); border-color: rgba(255, 255, 255, 0.7); box-shadow: 0 0 12px rgba(120, 160, 255, 0.4); outline: none; }

    /* Custom Dropdown Styling */
    .custom-dropdown { position: relative; width: 100%; }
    .dropdown-selected {
      width: 100%; background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25); border-radius: 2.2rem; padding: 0.9rem 1rem 0.9rem 2.8rem; font-size: 1rem; font-weight: 500; color: #f0f3ff; backdrop-filter: blur(4px); cursor: pointer; display: flex; align-items: center; justify-content: space-between;
    }
    .dropdown-selected:hover { background: rgba(255, 255, 255, 0.18); border-color: rgba(255, 255, 255, 0.4); }
    .dropdown-selected.active { border-color: rgba(255, 255, 255, 0.7); box-shadow: 0 0 12px rgba(120, 160, 255, 0.4); }
    .selected-text { flex: 1; text-align: left; }
    .dropdown-arrow { color: rgba(255, 255, 255, 0.7); font-size: 0.8rem; transition: transform 0.3s ease; }
    .dropdown-selected.active .dropdown-arrow { transform: rotate(180deg); }
    .dropdown-options {
      position: absolute; top: calc(100% + 0.5rem); left: 0; right: 0; background: rgba(26, 35, 50, 0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 1.2rem; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4); opacity: 0; visibility: hidden; transform: translateY(-10px) scale(0.95); transition: all 0.3s ease; z-index: 1000; overflow: hidden;
    }
    .dropdown-options.show { opacity: 1; visibility: visible; transform: translateY(0) scale(1); }
    .dropdown-option { display: flex; align-items: center; padding: 1rem 1.2rem; color: rgba(240, 243, 255, 0.9); cursor: pointer; transition: all 0.2s ease; }
    .dropdown-option:hover { background: rgba(102, 126, 234, 0.2); color: white; padding-left: 1.5rem; }
    .option-icon { margin-right: 0.8rem; font-size: 1.1rem; color: rgba(255, 255, 255, 0.8); }

    .role-selection { display: flex; gap: 1rem; margin-bottom: 1.5rem; }
    .role-option { flex: 1; position: relative; }
    .role-option input[type="radio"] { position: absolute; opacity: 0; cursor: pointer; }
    .role-label {
      display: flex; align-items: center; justify-content: center; padding: 0.8rem; background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25); border-radius: 1.2rem; cursor: pointer; transition: all 0.25s ease; color: rgba(255, 255, 255, 0.8); font-weight: 500; font-size: 0.9rem;
    }
    .role-option input[type="radio"]:checked + .role-label { background: linear-gradient(135deg, #667eea, #764ba2); border-color: rgba(255, 255, 255, 0.5); color: white; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); }
    .role-icon { margin-right: 0.5rem; font-size: 1.1rem; }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

    .register-btn {
      width: 100%; background: linear-gradient(95deg, #667eea, #764ba2); border: none; border-radius: 3rem; padding: 0.95rem; font-size: 1rem; font-weight: 600; color: white; cursor: pointer; transition: all 0.35s ease; box-shadow: 0 8px 18px rgba(102, 126, 234, 0.3); display: flex; align-items: center; justify-content: center; gap: 0.6rem;
    }
    .register-btn:hover { transform: translateY(-2px); box-shadow: 0 14px 28px rgba(102, 126, 234, 0.4); }

    .login-link { text-align: center; margin-top: 1.8rem; font-size: 0.85rem; color: rgba(235, 245, 255, 0.8); }
    .login-link a { color: #b8d0ff; text-decoration: none; font-weight: 600; margin-left: 0.3rem; transition: all 0.2s; }
    .login-link a:hover { color: white; }

    .error-message { background: rgba(255, 100, 100, 0.15); border: 1px solid rgba(255, 100, 100, 0.3); border-radius: 1rem; padding: 0.8rem; margin-bottom: 1rem; color: #ffcccc; font-size: 0.85rem; }
    .terms-group { display: flex; align-items: flex-start; margin: 1.2rem 0; font-size: 0.85rem; color: rgba(240, 245, 255, 0.85); }
    .terms-group input[type="checkbox"] { accent-color: #667eea; width: 1rem; height: 1rem; margin-right: 0.5rem; margin-top: 0.1rem; cursor: pointer; }
    .terms-group label { cursor: pointer; line-height: 1.4; }

    .hidden { display: none !important; }

    @keyframes subtleGlow {
      0% { box-shadow: 0 25px 40px rgba(0, 0, 0, 0.2), inset 0 1px 1px rgba(255, 255, 255, 0.2); }
      100% { box-shadow: 0 30px 50px rgba(0, 0, 0, 0.35), inset 0 1px 2px rgba(255, 255, 255, 0.3); }
    }

    @media (max-width: 550px) {
      .form-row { grid-template-columns: 1fr; }
      .role-selection { flex-direction: column; }
    }
  </style>
</head>
<body>

  <div class="blob-container">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
    <div class="blob blob-4"></div>
  </div>

  <header class="full-width-header">
    <div class="header-content">
      <div class="header-left">
        <div class="logo-container">
          <div class="header-logo-icon"><span class="header-logo-text">G</span></div>
          <div class="brand-info">
            <h1 class="brand-title">GPTFMS</h1>
            <p class="brand-subtitle">Group project team formation Management System</p>
          </div>
        </div>
      </div>
      <div class="header-right">
        <a href="{{ route('login') }}" class="login-btn">
          <i class="uil uil-sign-in-alt me-2"></i><span>Sign In</span>
        </a>
      </div>
    </div>
  </header>

  <div class="glass-card">
    <div class="card-content">
      <div class="register-card-header">
        <div class="register-header-icon"><i class="uil uil-user-plus"></i></div>
        <h2 class="register-title">Create Account</h2>
        <p class="register-subtitle">Join the GPTFMS community</p>
      </div>

      <form id="registerForm" action="{{ route('register') }}" method="POST">
        @csrf
        
        @if ($errors->any())
            <div class="error-message">
                @foreach ($errors->all() as $error)
                    <p><i class="uil uil-exclamation-circle me-1"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="role-selection">
          <div class="role-option">
            <input type="radio" id="student" name="role" value="student" {{ old('role', 'student') == 'student' ? 'checked' : '' }}>
            <label for="student" class="role-label"><i class="fas fa-graduation-cap role-icon"></i> Student</label>
          </div>
          <div class="role-option">
            <input type="radio" id="supervisor" name="role" value="supervisor" {{ old('role') == 'supervisor' ? 'checked' : '' }}>
            <label for="supervisor" class="role-label"><i class="fas fa-chalkboard-teacher role-icon"></i> Supervisor</label>
          </div>
        </div>

        <div class="form-row">
          <div class="input-group">
            <i class="fas fa-user input-icon"></i>
            <input type="text" class="input-field" name="first_name" placeholder="First Name" required value="{{ old('first_name') }}">
          </div>
          <div class="input-group">
            <i class="fas fa-user input-icon"></i>
            <input type="text" class="input-field" name="last_name" placeholder="Last Name" required value="{{ old('last_name') }}">
          </div>
        </div>

        <div class="input-group">
          <i class="fas fa-envelope input-icon"></i>
          <input type="email" class="input-field" name="email" placeholder="Email address" required value="{{ old('email') }}">
        </div>

        <div class="input-group">
          <i class="fas fa-phone input-icon"></i>
          <input type="tel" class="input-field" name="phone" placeholder="Phone number" required value="{{ old('phone') }}">
        </div>

        <div class="input-group">
          <i class="fas fa-venus-mars input-icon"></i>
          <div class="custom-dropdown" id="genderDropdown">
            <div class="dropdown-selected" id="genderSelected">
              <span class="selected-text">{{ old('gender') ? ucfirst(old('gender')) : 'Select gender' }}</span>
              <i class="fas fa-chevron-down dropdown-arrow"></i>
            </div>
            <div class="dropdown-options" id="genderOptions">
              <div class="dropdown-option" data-value="male"><i class="fas fa-mars option-icon"></i> <span>Male</span></div>
              <div class="dropdown-option" data-value="female"><i class="fas fa-venus option-icon"></i> <span>Female</span></div>
              <div class="dropdown-option" data-value="other"><i class="fas fa-genderless option-icon"></i> <span>Other</span></div>
            </div>
          </div>
          <input type="hidden" id="gender" name="gender" required value="{{ old('gender') }}">
        </div>

        <div id="reg-num-group" class="input-group {{ old('role', 'student') == 'student' ? '' : 'hidden' }}">
          <i class="fas fa-id-card input-icon"></i>
          <input type="text" class="input-field" name="registration_number" placeholder="Registration Number" value="{{ old('registration_number') }}">
        </div>

        <div class="form-row">
          <div class="input-group">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" class="input-field" name="password" placeholder="Password" required>
          </div>
          <div class="input-group">
            <i class="fas fa-shield-check input-icon"></i>
            <input type="password" class="input-field" name="password_confirmation" placeholder="Confirm Password" required>
          </div>
        </div>

        <div class="terms-group">
          <input type="checkbox" name="terms" id="terms" required {{ old('terms') ? 'checked' : '' }}>
          <label for="terms">I agree to the Terms of Service and Privacy Policy</label>
        </div>

        <button type="submit" class="register-btn">
          <span>Create Account</span><i class="uil uil-arrow-right"></i>
        </button>

        <div class="login-link">
          Already have an account? <a href="{{ route('login') }}">Sign In</a>
        </div>
      </form>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Gender Dropdown Logic
      const genderDropdown = document.getElementById('genderDropdown');
      const genderSelected = document.getElementById('genderSelected');
      const genderOptions = document.getElementById('genderOptions');
      const genderInput = document.getElementById('gender');
      const options = genderOptions.querySelectorAll('.dropdown-option');

      genderSelected.addEventListener('click', () => {
        genderOptions.classList.toggle('show');
        genderSelected.classList.toggle('active');
      });

      options.forEach(option => {
        option.addEventListener('click', () => {
          const val = option.dataset.value;
          const text = option.querySelector('span').innerText;
          genderInput.value = val;
          genderSelected.querySelector('.selected-text').innerText = text;
          genderOptions.classList.remove('show');
          genderSelected.classList.remove('active');
        });
      });

      document.addEventListener('click', (e) => {
        if (!genderDropdown.contains(e.target)) {
          genderOptions.classList.remove('show');
          genderSelected.classList.remove('active');
        }
      });

      // Role Logic (Show/Hide Reg Number)
      const roleRadios = document.querySelectorAll('input[name="role"]');
      const regNumGroup = document.getElementById('reg-num-group');
      const regNumInput = regNumGroup.querySelector('input');

      roleRadios.forEach(radio => {
        radio.addEventListener('change', () => {
          if (radio.value === 'student') {
            regNumGroup.classList.remove('hidden');
            regNumInput.required = true;
          } else {
            regNumGroup.classList.add('hidden');
            regNumInput.required = false;
          }
        });
      });
    });
  </script>
</body>
</html>
