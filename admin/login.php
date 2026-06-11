<?php
// ============================================================
// ADMIN LOGIN
// admin/login.php
// ============================================================
session_start();
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — CAC Achievers House</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<!-- Animated background orbs -->
<div class="bg-orb orb-1"></div>
<div class="bg-orb orb-2"></div>
<div class="bg-orb orb-3"></div>

<div class="login-wrapper">

    <!-- Left panel - branding -->
    <div class="login-branding">
        <div class="branding-inner">
            <div class="brand-logo">
                <img src="../assets/logo/cac-logo.png" alt="CAC Logo"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div class="logo-fallback" style="display:none;">
                    <i class='bx bx-church'></i>
                </div>
            </div>
            <h1>CAC Achievers House</h1>
            <p>Where Faith Meets Destiny</p>

            <div class="brand-features">
                <div class="feature-item">
                    <i class='bx bx-shield-check'></i>
                    <span>Secure admin access</span>
                </div>
                <div class="feature-item">
                    <i class='bx bx-cog'></i>
                    <span>Full site management</span>
                </div>
                <div class="feature-item">
                    <i class='bx bx-chart'></i>
                    <span>Real-time dashboard</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right panel - form -->
    <div class="login-form-panel">
        <div class="login-card">

            <div class="login-header">
                <div class="login-icon">
                    <i class='bx bx-lock-alt'></i>
                </div>
                <h2>Admin Portal</h2>
                <p>Sign in to manage your church website</p>
            </div>

            <form action="auth.php" method="POST" class="login-form">

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-group">
                        <i class='bx bx-envelope'></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            required
                            placeholder="admin@cacachievers.com"
                            autocomplete="email"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <i class='bx bx-lock-alt'></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            placeholder="Enter your password"
                            autocomplete="current-password"
                        >
                        <button type="button" class="toggle-pw" id="togglePw" aria-label="Toggle password visibility">
                            <i class='bx bx-hide' id="pwIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="login-options">
                    <label class="remember-label">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="btn-login">
                    <span>Sign In</span>
                    <i class='bx bx-right-arrow-alt'></i>
                </button>

            </form>

            <a href="../index.php" class="back-home">
                <i class='bx bx-arrow-back'></i> Back to website
            </a>

        </div>
    </div>

</div>

<?php if ($error): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Login Failed',
    text: '<?= addslashes(htmlspecialchars($error)) ?>',
    confirmButtonColor: '#f97316'
});
</script>
<?php endif; ?>

<script>
const toggleBtn = document.getElementById('togglePw');
const pwInput   = document.getElementById('password');
const pwIcon    = document.getElementById('pwIcon');

toggleBtn.addEventListener('click', () => {
    const isHidden = pwInput.type === 'password';
    pwInput.type = isHidden ? 'text' : 'password';
    pwIcon.className = isHidden ? 'bx bx-show' : 'bx bx-hide';
});
</script>

</body>
</html>
