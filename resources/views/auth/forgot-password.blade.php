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
  </style>
</head>
<body>
  <div class="blob-container">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
  </div>

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

      <form method="POST" action="{{ route('password.otp.send') }}">
        @csrf
        <div class="input-group">
          <input type="email" name="email" class="input-field" placeholder="Email Address" required autocomplete="email" value="{{ old('email') }}">
          <i class="uil uil-envelope input-icon"></i>
        </div>
        <button type="submit" class="btn">
          <i class="uil uil-message"></i> Send OTP
        </button>
      </form>

      <div class="link-row">
        Back to <a href="{{ route('login') }}">Login</a>
      </div>
    </div>
  </div>
</body>
</html>

