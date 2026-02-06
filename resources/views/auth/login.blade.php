<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Library Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .login-header {
            background: #1a1a2e;
            padding: 40px 20px;
            text-align: center;
        }
        .login-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }
        .login-icon i {
            font-size: 36px;
            color: #e94560;
        }
        .login-header h2 {
            color: #fff;
            font-size: 22px;
            font-weight: 700;
            margin: 0;
        }
        .login-body {
            padding: 32px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 6px;
            display: block;
        }
        .form-group .form-control {
            padding: 10px 14px;
            font-size: 14px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            background: #f8f9fa;
        }
        .form-group .form-control:focus {
            border-color: #0f3460;
            box-shadow: 0 0 0 0.2rem rgba(15,52,96,0.15);
            background: #fff;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #e94560;
            border: none;
            border-radius: 6px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-login:hover {
            background: #d63851;
        }
        .login-footer {
            text-align: center;
            padding: 0 32px 24px;
            font-size: 13px;
            color: #6c757d;
        }
        .alert {
            font-size: 13px;
            border-radius: 6px;
        }
        .input-icon {
            position: relative;
        }
        .input-icon i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 14px;
        }
        .input-icon .form-control {
            padding-left: 40px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="login-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <h2>Library Management System</h2>
        </div>

        <div class="login-body">
            @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div><i class="fas fa-exclamation-circle me-1"></i> {{ $error }}</div>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" id="username" class="form-control"
                               placeholder="Enter username" value="{{ old('username') }}" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" class="form-control"
                               placeholder="Enter password" required>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i> LOGIN
                </button>
            </form>
        </div>

        <div class="login-footer">
            <p>Default credentials: admin / admin123</p>
        </div>
    </div>
</body>
</html>
