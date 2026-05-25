<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>Reset Password | GPTFMS</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
  <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{min-height:100vh;background:radial-gradient(circle at 30% 10%,#0a0c1a,#03050b);font-family:'Nunito Sans',sans-serif;display:flex;align-items:flex-start;justify-content:center;overflow-x:hidden;padding:1.5rem;padding-top:160px;color:#f0f3ff}
    .blob-container{position:fixed;top:0;left:0;width:100%;height:100%;z-index:0;overflow:hidden;pointer-events:none}
    .blob{position:absolute;border-radius:50%;filter:blur(70px);opacity:.65;animation:floatBlob 22s infinite alternate ease-in-out}
    .blob-1{width:55vw;height:55vw;background:radial-gradient(circle at 30% 30%,rgba(88,130,255,.7),rgba(30,80,220,.3));top:-20vh;left:-15vw;animation-duration:24s}
    .blob-2{width:65vw;height:65vw;background:radial-gradient(circle at 70% 60%,rgba(255,90,180,.6),rgba(160,50,220,.4));bottom:-35vh;right:-20vw;animation-duration:28s;animation-delay:2s}
    .blob-3{width:45vw;height:45vw;background:radial-gradient(circle at 40% 50%,rgba(0,210,200,.6),rgba(40,170,220,.5));top:40vh;left:55vw;animation-duration:20s;animation-delay:5s;filter:blur(80px)}
    @keyframes floatBlob{0%{transform:translate(0,0) scale(1)}40%{transform:translate(5%,8%) scale(1.08)}70%{transform:translate(-4%,3%) scale(.95)}100%{transform:translate(7%,-6%) scale(1.12)}}
    .glass-card{position:relative;z-index:2;width:100%;max-width:520px;background:rgba(20,30,55,.28);backdrop-filter:blur(16px) saturate(180%);-webkit-backdrop-filter:blur(16px) saturate(180%);border-radius:2rem;border:1px solid rgba(255,255,255,.35);box-shadow:0 30px 50px rgba(0,0,0,.3),inset 0 1px 1px rgba(255,255,255,.2);overflow:hidden}
    .card-content{padding:2.2rem 2rem 2.5rem}
    .title{font-size:22px;font-weight:900;letter-spacing:-.4px;margin-bottom:6px}
    .sub{font-size:13px;color:rgba(240,243,255,.8);margin-bottom:18px;line-height:1.5}
    .grid{display:grid;gap:12px}
    .input-group{position:relative}
    .input-field{width:100%;padding:14px 48px 14px 16px;border-radius:14px;border:1px solid rgba(255,255,255,.25);background:rgba(10,14,26,.35);color:#f0f3ff;outline:none;font-size:14px}
    .input-field:focus{border-color:rgba(120,160,255,.8)}
    .input-icon{position:absolute;right:14px;top:50%;transform:translateY(-50%);color:rgba(240,243,255,.7);font-size:18px}
    .row{display:flex;gap:10px;align-items:center}
    .btn{padding:14px 16px;border:none;border-radius:14px;background:linear-gradient(135deg,#5b8cff,#8b5cf6);color:#fff;font-weight:800;font-size:14px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:10px;flex:1}
    .btn-outline{background:transparent;border:1px solid rgba(255,255,255,.25);color:#f0f3ff}
    .btn:disabled{opacity:.65;cursor:not-allowed}
    .alert{margin-bottom:14px;padding:12px 14px;border-radius:14px;font-size:13px;line-height:1.4;border:1px solid rgba(255,255,255,.18);background:rgba(0,0,0,.18)}
    .alert.error{border-color:rgba(255,80,120,.35);background:rgba(255,80,120,.12)}
    .alert.success{border-color:rgba(16,185,129,.35);background:rgba(16,185,129,.12)}
    .modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,.55);padding:18px;z-index:50}
    .modal.open{display:flex}
    .modal-card{width:100%;max-width:520px;background:rgba(20,30,55,.92);border:1px solid rgba(255,255,255,.18);border-radius:18px;box-shadow:0 30px 60px rgba(0,0,0,.4);overflow:hidden}
    .modal-head{padding:14px 16px;border-bottom:1px solid rgba(255,255,255,.12);display:flex;align-items:center;justify-content:space-between}
    .modal-title{font-weight:900;font-size:14px}
    .modal-close{cursor:pointer;color:rgba(240,243,255,.8);font-size:18px}
    .modal-body{padding:16px}
    .link-row{margin-top:16px;font-size:13px;color:rgba(240,243,255,.8);text-align:center}
    .link-row a{color:#9bb7ff;text-decoration:none;font-weight:700}
  </style>
</head>
<body>
  <div class="blob-container">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
  </div>

  @php
    $passwordResetEnabled = \App\Models\SystemSetting::getBool('auth.password_reset_enabled', true);
  @endphp

  <div class="glass-card">
    <div class="card-content">
      <div class="title">Reset Password</div>
      <div class="sub">Enter the OTP sent to your email. After verification, you can set a new password.</div>

      @if(session('status'))
        <div class="alert success">{{ session('status') }}</div>
      @endif

      <div id="reset-alert" class="alert" style="display:none"></div>

      @if($passwordResetEnabled)
        <div class="grid">
          <div class="input-group">
            <input type="email" id="email" class="input-field" placeholder="Email Address" value="{{ $email ?? '' }}" autocomplete="email" required>
            <i class="uil uil-envelope input-icon"></i>
          </div>
          <div class="input-group">
            <input type="text" id="otp" class="input-field" placeholder="Enter OTP (6 digits)" inputmode="numeric" maxlength="6" required>
            <i class="uil uil-key-skeleton input-icon"></i>
          </div>
          <div class="row">
            <button type="button" class="btn btn-outline" id="resendBtn" onclick="resendOtp()">
              <i class="uil uil-redo"></i> Resend OTP
            </button>
            <button type="button" class="btn" id="verifyBtn" onclick="verifyOtp()">
              <i class="uil uil-shield-check"></i> Verify OTP
            </button>
          </div>
        </div>
      @else
        <div class="alert error" style="display:block">Password reset is temporarily disabled by the administrator.</div>
      @endif

      <div class="link-row">
        Back to <a href="{{ route('login') }}">Login</a>
      </div>
    </div>
  </div>

  @if($passwordResetEnabled)
    <div class="modal" id="passwordModal">
      <div class="modal-card">
        <div class="modal-head">
          <div class="modal-title">Set New Password</div>
          <div class="modal-close" onclick="closeModal()"><i class="uil uil-multiply"></i></div>
        </div>
        <div class="modal-body">
          <div id="modal-alert" class="alert" style="display:none"></div>
          <div class="grid">
            <div class="input-group">
              <input type="password" id="new_password" class="input-field" placeholder="New Password" autocomplete="new-password">
              <i class="uil uil-lock input-icon"></i>
            </div>
            <div class="input-group">
              <input type="password" id="new_password_confirmation" class="input-field" placeholder="Confirm Password" autocomplete="new-password">
              <i class="uil uil-lock-access input-icon"></i>
            </div>
            <button type="button" class="btn" id="resetBtn" onclick="resetPassword()">
              <i class="uil uil-check"></i> Change Password
            </button>
          </div>
        </div>
      </div>
    </div>
  @endif

  @if($passwordResetEnabled)
    <script>
      let resetToken = null;

    function showAlert(el, msg, type) {
      el.style.display = 'block';
      el.className = 'alert ' + (type || '');
      el.textContent = msg;
    }
    function errorMessage(err, fallback) {
      if (!err) return fallback;
      if (typeof err === 'string') return err;
      if (err.message) return err.message;
      if (err.errors) {
        const firstKey = Object.keys(err.errors)[0];
        if (firstKey && err.errors[firstKey] && err.errors[firstKey][0]) return err.errors[firstKey][0];
      }
      return fallback;
    }

    function openModal() {
      document.getElementById('passwordModal').classList.add('open');
    }

    function closeModal() {
      document.getElementById('passwordModal').classList.remove('open');
    }

    function resendOtp() {
      const email = document.getElementById('email').value.trim();
      const alertEl = document.getElementById('reset-alert');
      if (!email) {
        showAlert(alertEl, 'Please enter your email.', 'error');
        return;
      }
      const btn = document.getElementById('resendBtn');
      btn.disabled = true;

      fetch("{{ route('password.otp.send') }}", {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ email })
      })
      .then(async (res) => {
        const data = await res.json().catch(() => ({}));
        if (!res.ok) throw data;
        showAlert(alertEl, data.message || 'If this email exists, a new OTP has been sent.', 'success');
      })
      .catch(() => {
        showAlert(alertEl, 'Failed to resend OTP.', 'error');
      })
      .finally(() => {
        setTimeout(() => { btn.disabled = false; }, 1500);
      });
    }

    function verifyOtp() {
      const email = document.getElementById('email').value.trim();
      const otp = document.getElementById('otp').value.trim();
      const alertEl = document.getElementById('reset-alert');
      if (!email || !otp) {
        showAlert(alertEl, 'Email and OTP are required.', 'error');
        return;
      }

      const btn = document.getElementById('verifyBtn');
      btn.disabled = true;

      fetch("{{ route('password.otp.verify') }}", {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ email, otp })
      })
      .then(async (res) => {
        const data = await res.json();
        if (!res.ok) throw data;
        resetToken = data.reset_token;
        showAlert(alertEl, 'OTP verified. Set your new password.', 'success');
        openModal();
      })
      .catch((err) => {
        showAlert(alertEl, errorMessage(err, 'OTP verification failed.'), 'error');
      })
      .finally(() => {
        btn.disabled = false;
      });
    }

    function resetPassword() {
      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('new_password').value;
      const password_confirmation = document.getElementById('new_password_confirmation').value;
      const alertEl = document.getElementById('modal-alert');

      if (!resetToken) {
        showAlert(alertEl, 'Please verify OTP first.', 'error');
        return;
      }

      const btn = document.getElementById('resetBtn');
      btn.disabled = true;

      fetch("{{ route('password.update') }}", {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ email, reset_token: resetToken, password, password_confirmation })
      })
      .then(async (res) => {
        const data = await res.json();
        if (!res.ok) throw data;
        showAlert(alertEl, data.message || 'Password updated successfully.', 'success');
        setTimeout(() => {
          window.location.href = "{{ route('login') }}";
        }, 1200);
      })
      .catch((err) => {
        showAlert(alertEl, errorMessage(err, 'Password reset failed.'), 'error');
      })
      .finally(() => {
        btn.disabled = false;
      });
    }
    </script>
  @endif
</body>
</html>
