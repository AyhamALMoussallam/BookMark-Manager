<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Auth Page</title>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<style>
body { font-family: Arial, sans-serif; background: #f0f2f5; display:flex; justify-content:center; align-items:center; height:100vh; }
.container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); width: 350px; }
h2 { text-align:center; margin-bottom: 20px; }
input { width: 100%; padding: 10px; margin: 8px 0; border-radius: 5px; border: 1px solid #ccc; }
button { width: 100%; padding: 10px; margin-top: 10px; border:none; border-radius:5px; background:#4CAF50; color:white; cursor:pointer; }
button:hover { background:#45a049; }
.google-btn { background:#db4437; }
.google-btn:hover { background:#c1351d; }
.message { margin-top: 10px; color: red; text-align:center; }
.toggle { text-align:center; margin-top:10px; cursor:pointer; color: #007bff; }
</style>
</head>
<body>

<div class="container">
    <h2 id="form-title">Sign In</h2>

    <!-- Sign In Form -->
    <div id="signin-form">
        <input type="email" id="login-email" placeholder="Email">
        <input type="password" id="login-password" placeholder="Password">
        <button onclick="login()">Sign In</button>
        <div class="toggle" onclick="toggleForms()">Don't have an account? Sign Up</div>
    </div>

    <!-- Sign Up Form -->
    <div id="signup-form" style="display:none;">
        <input type="text" id="signup-name" placeholder="Name">
        <input type="email" id="signup-email" placeholder="Email">
        <input type="password" id="signup-password" placeholder="Password">
        <button onclick="signup()">Sign Up</button>
        <div class="toggle" onclick="toggleForms()">Already have an account? Sign In</div>
    </div>

    <!-- Resend verification button -->
    <div id="resend-verification-container" style="text-align:center; margin-top:10px; display:none;">
        <button style="background:#007bff; color:white; padding:8px 12px; border:none; border-radius:5px; cursor:pointer;"
            onclick="resendVerificationEmail()">
            Resend Verification Email
        </button>
    </div>

    <!-- Google Login -->
    <button class="google-btn" onclick="googleLogin()">Sign In with Google</button>

    <div class="message" id="message"></div>
</div>

<script>
const apiBase = '/api';

function toggleForms() {
    const signin = document.getElementById('signin-form');
    const signup = document.getElementById('signup-form');
    const title = document.getElementById('form-title');

    if (signin.style.display === 'none') {
        signin.style.display = 'block';
        signup.style.display = 'none';
        title.textContent = 'Sign In';
    } else {
        signin.style.display = 'none';
        signup.style.display = 'block';
        title.textContent = 'Sign Up';
    }
}

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

function signup() {
    const name = document.getElementById('signup-name').value;
    const email = document.getElementById('signup-email').value;
    const password = document.getElementById('signup-password').value;

    axios.post(`${apiBase}/signup`, { name, email, password })
        .then(res => {
            document.getElementById('message').style.color = 'green';
            document.getElementById('message').textContent =
                'Sign Up successful! Please verify your email.';

            toggleForms();

            document.getElementById('resend-verification-container').style.display = 'block';
        })
        .catch(err => {
            document.getElementById('message').style.color = 'red';
            document.getElementById('message').textContent =
                err.response?.data?.message || 'Sign Up failed';
        });
}

function resendVerificationEmail() {
    const email =
        document.getElementById('login-email').value ||
        document.getElementById('signup-email').value;

    if (!email) {
        document.getElementById('message').textContent = 'Please enter your email first';
        return;
    }

    axios.post(`${apiBase}/email/verification-notification`, { email })
        .then(res => {
            document.getElementById('message').style.color = 'green';
            document.getElementById('message').textContent = res.data.message;
        })
        .catch(err => {
            document.getElementById('message').style.color = 'red';
            document.getElementById('message').textContent =
                err.response?.data?.message || 'Failed to resend';
        });
}

function googleLogin() {
    window.location.href = `${apiBase}/auth/google/redirect`;
}
</script>

</body>
</html>
