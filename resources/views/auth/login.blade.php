<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<style>
body { font-family: Arial, sans-serif; background:#f0f2f5; display:flex; justify-content:center; align-items:center; height:100vh; }
.container { background:white; padding:30px; border-radius:10px; box-shadow:0 0 15px rgba(0,0,0,0.1); width:350px; }
h2 { text-align:center; margin-bottom:20px; }
input { width:100%; padding:10px; margin:8px 0; border-radius:5px; border:1px solid #ccc; }
button { width:100%; padding:10px; margin-top:10px; border:none; border-radius:5px; background:#4CAF50; color:white; cursor:pointer; }
button:hover { background:#45a049; }
.google-btn { background:#db4437; }
.google-btn:hover { background:#c1351d; }
.message { margin-top:10px; text-align:center; }
.link { text-align:center; margin-top:10px; }
.link a { color:#007bff; text-decoration:none; }
</style>
</head>
<body>

<div class="container">
    <h2>Sign In</h2>

    <input type="email" id="login-email" placeholder="Email">
    <input type="password" id="login-password" placeholder="Password">
    <button onclick="login()">Sign In</button>

    <div class="link">
        <a href="/forgot-password">Forgot password?</a>
    </div>
    <div class="link">
        <a href="/signup">Don't have an account? Sign Up</a>
    </div>

    <button class="google-btn" onclick="googleLogin()">Sign In with Google</button>

    <div class="message" id="message"></div>
</div>

<script>
const apiBase = '/api';

function login() {
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;

    axios.post(`${apiBase}/login`, { email, password })
        .then(res => {
            localStorage.setItem('auth_token', res.data.token);
            window.location.href = '/dashboard';
        })
        .catch(err => {
            document.getElementById('message').style.color = 'red';
            document.getElementById('message').textContent =
                err.response?.data?.message || 'Login failed';
        });
}

function googleLogin() {
    window.location.href = `${apiBase}/auth/google/redirect`;
}
</script>

</body>
</html>