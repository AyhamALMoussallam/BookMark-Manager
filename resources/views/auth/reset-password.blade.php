<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password</title>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<style>
body { font-family: Arial, sans-serif; background:#f0f2f5; display:flex; justify-content:center; align-items:center; min-height:100vh; padding:20px; }
.container { background:white; padding:30px; border-radius:10px; box-shadow:0 0 15px rgba(0,0,0,0.1); width:350px; }
h2 { text-align:center; margin-bottom:20px; }
input { width:100%; padding:10px; margin:8px 0; border-radius:5px; border:1px solid #ccc; box-sizing:border-box; }
button { width:100%; padding:10px; margin-top:10px; border:none; border-radius:5px; background:#4CAF50; color:white; cursor:pointer; }
button:hover { background:#45a049; }
.message { margin-top:10px; text-align:center; }
.link { text-align:center; margin-top:15px; }
.link a { color:#007bff; text-decoration:none; }
.error-box { background:#fff3f3; border:1px solid #f44336; color:#c62828; padding:12px; border-radius:6px; margin-bottom:15px; font-size:14px; }
</style>
</head>
<body>

<div class="container">
    <h2>Set New Password</h2>

    <div id="invalid-link" class="error-box" style="display:none;">
        This link is invalid or has expired. <a href="/forgot-password">Request a new one</a>.
    </div>

    <form id="reset-form" style="display:none;">
        <input type="hidden" id="reset-token" name="token">
        <input type="email" id="reset-email" name="email" placeholder="Email" readonly style="background:#f5f5f5;">
        <input type="password" id="reset-password" name="password" placeholder="New password" minlength="6" required>
        <input type="password" id="reset-password-confirm" name="password_confirmation" placeholder="Confirm new password" minlength="6" required>
        <button type="submit">Reset Password</button>
    </form>

    <div class="link">
        <a href="/login">Back to Sign In</a>
    </div>

    <div class="message" id="message"></div>
</div>

<script>
const apiBase = '/api';

(function init() {
    const params = new URLSearchParams(window.location.search);
    const token = params.get('token');
    const email = params.get('email');

    if (!token || !email) {
        document.getElementById('invalid-link').style.display = 'block';
        return;
    }

    document.getElementById('invalid-link').style.display = 'none';
    document.getElementById('reset-form').style.display = 'block';
    document.getElementById('reset-token').value = token;
    document.getElementById('reset-email').value = email;
})();

document.getElementById('reset-form').addEventListener('submit', function (e) {
    e.preventDefault();

    const password = document.getElementById('reset-password').value;
    const confirm = document.getElementById('reset-password-confirm').value;
    const msgEl = document.getElementById('message');

    if (password.length < 6) {
        msgEl.style.color = 'red';
        msgEl.textContent = 'Password must be at least 6 characters.';
        return;
    }
    if (password !== confirm) {
        msgEl.style.color = 'red';
        msgEl.textContent = 'Passwords do not match.';
        return;
    }

    msgEl.textContent = '';

    axios.post(`${apiBase}/reset-password`, {
        token: document.getElementById('reset-token').value,
        email: document.getElementById('reset-email').value,
        password: password,
        password_confirmation: confirm,
    })
        .then(res => {
            msgEl.style.color = '#4CAF50';
            msgEl.textContent = res.data.message || 'Password reset successfully. You can now sign in.';
            setTimeout(function () {
                window.location.href = '/login';
            }, 2500);
        })
        .catch(err => {
            msgEl.style.color = 'red';
            msgEl.textContent = err.response?.data?.message || 'Failed to reset password. The link may have expired.';
        });
});
</script>

</body>
</html>
