<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password</title>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<style>
body { font-family: Arial, sans-serif; background:#f0f2f5; display:flex; justify-content:center; align-items:center; height:100vh; }
.container { background:white; padding:30px; border-radius:10px; box-shadow:0 0 15px rgba(0,0,0,0.1); width:350px; }
h2 { text-align:center; margin-bottom:20px; }
input { width:100%; padding:10px; margin:8px 0; border-radius:5px; border:1px solid #ccc; box-sizing:border-box; }
button { width:100%; padding:10px; margin-top:10px; border:none; border-radius:5px; background:#4CAF50; color:white; cursor:pointer; }
button:hover { background:#45a049; }
.message { margin-top:10px; text-align:center; }
.link { text-align:center; margin-top:15px; }
.link a { color:#007bff; text-decoration:none; }
</style>
</head>
<body>

<div class="container">
    <h2>Forgot Password</h2>
    <p style="color:#666; font-size:14px; text-align:center; margin-bottom:15px;">Enter your email and we'll send you a link to reset your password.</p>

    <input type="email" id="forgot-email" placeholder="Email">
    <button onclick="sendResetLink()">Send Reset Link</button>

    <div class="link">
        <a href="/login">Back to Sign In</a>
    </div>

    <div class="message" id="message"></div>
</div>

<script>
const apiBase = '/api';

function sendResetLink() {
    const email = document.getElementById('forgot-email').value.trim();
    const msgEl = document.getElementById('message');

    if (!email) {
        msgEl.style.color = 'red';
        msgEl.textContent = 'Please enter your email.';
        return;
    }

    msgEl.textContent = '';
    axios.post(`${apiBase}/forgot-password`, { email })
        .then(res => {
            msgEl.style.color = '#4CAF50';
            msgEl.textContent = res.data.message || 'If an account exists for that email, we have sent a password reset link. Check your inbox.';
        })
        .catch(err => {
            msgEl.style.color = 'red';
            msgEl.textContent = err.response?.data?.message || 'Something went wrong. Try again later.';
            if (err.response?.status === 429) {
                msgEl.textContent = err.response?.data?.message || 'Too many requests. Please wait before trying again.';
            }
        });
}
</script>

</body>
</html>
