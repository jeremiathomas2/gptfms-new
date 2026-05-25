<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>Forgot Password | GPTFMS</title>
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
    .glass-card{position:relative;z-index:2;width:100%;max-width:480px;background:rgba(20,30,55,.28);backdrop-filter:blur(16px) saturate(180%);-webkit-backdrop-filter:blur(16px) saturate(180%);border-radius:2rem;border:1px solid rgba(255,255,255,.35);box-shadow:0 30px 50px rgba(0,0,0,.3),inset 0 1px 1px rgba(255,255,255,.2);overflow:hidden}
    .card-content{padding:2.2rem 2rem 2.5rem}
    .title{font-size:22px;font-weight:900;letter-spacing:-.4px;margin-bottom:6px}
    .sub{font-size:13px;color:rgba(240,243,255,.8);margin-bottom:18px;line-height:1.5}
    .input-group{position:relative;margin-bottom:14px}
    .input-field{width:100%;padding:14px 48px 14px 16px;border-radius:14px;border:1px solid rgba(255,255,255,.25);background:rgba(10,14,26,.35);color:#f0f3ff;outline:none;font-size:14px}
    .input-field:focus{border-color:rgba(120,160,255,.8)}
    .input-icon{position:absolute;right:14px;top:50%;transform:translateY(-50%);color:rgba(240,243,255,.7);font-size:18px}
    .btn{width:100%;padding:14px 16px;border:none;border-radius:14px;background:linear-gradient(135deg,#5b8cff,#8b5cf6);color:#fff;font-weight:800;font-size:14px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:10px}
    .btn:disabled{opacity:.65;cursor:not-allowed}
    .link-row{margin-top:16px;font-size:13px;color:rgba(240,243,255,.8);text-align:center}
    .link-row a{color:#9bb7ff;text-decoration:none;font-weight:700}
    .alert{margin-bottom:14px;padding:12px 14px;border-radius:14px;font-size:13px;line-height:1.4;border:1px solid rgba(255,255,255,.18);background:rgba(0,0,0,.18)}
    .alert.error{border-color:rgba(255,80,120,.35);background:rgba(255,80,120,.12)}
    .alert.success{border-color:rgba(16,185,129,.35);background:rgba(16,185,129,.12)}
    .progress-overlay{position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,.6);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);z-index:60;padding:18px}
    .progress-overlay.open{display:flex}
    .progress-card{width:100%;max-width:520px;background:rgba(20,30,55,.92);border:1px solid rgba(255,255,255,.18);border-radius:18px;box-shadow:0 30px 60px rgba(0,0,0,.4);overflow:hidden}
    .progress-head{padding:14px 16px;border-bottom:1px solid rgba(255,255,255,.12);display:flex;align-items:center;gap:10px}
    .progress-title{font-weight:900;font-size:14px}
    .progress-body{padding:16px}
    .progress-meta{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
    .progress-text{font-size:12.5px;color:rgba(240,243,255,.85)}
    .progress-percent{font-size:12.5px;font-weight:900;color:#9bb7ff}
    .progress-bar{height:10px;border-radius:999px;background:rgba(255,255,255,.12);overflow:hidden;border:1px solid rgba(255,255,255,.12)}
    .progress-fill{height:100%;width:0%;border-radius:999px;background:linear-gradient(135deg,#5b8cff,#8b5cf6);transition:width .18s ease}
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
      <div class="title">Forgot Password</div>
      <div class="sub">Enter your email. We will send a one-time OTP to verify your identity and let you reset your password.</div>

      @if(session('status'))
        <div class="alert success">{{ session('status') }}</div>
      @endif

      @if($errors->any())
        <div class="alert error">{{ $errors->first() }}</div>
      @endif

      @if($passwordResetEnabled)
        <form method="POST" action="{{ route('password.otp.send') }}" id="otpRequestForm">
          @csrf
          <div class="input-group">
            <input type="email" name="email" id="email" class="input-field" placeholder="Email Address" required autocomplete="email" value="{{ old('email') }}">
            <i class="uil uil-envelope input-icon"></i>
          </div>
          <button type="submit" class="btn" id="sendOtpBtn">
            <i class="uil uil-message"></i> Send OTP
          </button>
        </form>
      @else
        <div class="alert error">Password reset is temporarily disabled by the administrator.</div>
      @endif

      <div class="link-row">
        Back to <a href="{{ route('login') }}">Login</a>
      </div>
    </div>
  </div>

  @if($passwordResetEnabled)
    <div class="progress-overlay" id="progressOverlay" aria-hidden="true">
      <div class="progress-card">
        <div class="progress-head">
          <i class="uil uil-envelope-send" style="font-size:18px;color:#9bb7ff"></i>
          <div class="progress-title">Sending OTP…</div>
        </div>
        <div class="progress-body">
          <div class="progress-meta">
            <div class="progress-text" id="progressText">Contacting mail server</div>
            <div class="progress-percent" id="progressPercent">0%</div>
          </div>
          <div class="progress-bar">
            <div class="progress-fill" id="progressFill"></div>
          </div>
        </div>
      </div>
    </div>
  @endif

  @if($passwordResetEnabled)
    <script>
      (function () {
        const form = document.getElementById('otpRequestForm');
        const btn = document.getElementById('sendOtpBtn');
        const overlay = document.getElementById('progressOverlay');
        const fill = document.getElementById('progressFill');
        const percentEl = document.getElementById('progressPercent');
        const textEl = document.getElementById('progressText');

      function openProgress() {
        overlay.classList.add('open');
        overlay.setAttribute('aria-hidden', 'false');
      }
      function setProgress(p, label) {
        const clamped = Math.max(0, Math.min(100, p));
        fill.style.width = clamped + '%';
        percentEl.textContent = clamped + '%';
        if (label) textEl.textContent = label;
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

        if (!form) return;
        form.addEventListener('submit', function (e) {
          e.preventDefault();
          const email = document.getElementById('email')?.value?.trim();
          if (!email) return;

        btn.disabled = true;
        openProgress();
        setProgress(0, 'Contacting mail server');

        let progress = 0;
        const steps = [
          { at: 15, label: 'Validating email' },
          { at: 35, label: 'Generating OTP' },
          { at: 60, label: 'Sending email' },
          { at: 85, label: 'Finalizing' },
        ];
        let stepIndex = 0;

        const interval = setInterval(() => {
          progress = Math.min(92, progress + 3);
          if (stepIndex < steps.length && progress >= steps[stepIndex].at) {
            setProgress(progress, steps[stepIndex].label);
            stepIndex++;
          } else {
            setProgress(progress);
          }
        }, 120);

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
          clearInterval(interval);
          setProgress(100, 'OTP sent');
          setTimeout(() => {
            const url = new URL("{{ route('password.reset') }}", window.location.origin);
            url.searchParams.set('email', email);
            window.location.href = url.toString();
          }, 450);
        })
        .catch((err) => {
          clearInterval(interval);
          setProgress(100, errorMessage(err, 'Failed to send OTP'));
          btn.disabled = false;
          setTimeout(() => {
            overlay.classList.remove('open');
            overlay.setAttribute('aria-hidden', 'true');
            setProgress(0, 'Contacting mail server');
          }, 900);
        });
        });
      })();
    </script>
  @endif
</body>
</html>
