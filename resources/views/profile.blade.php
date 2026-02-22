<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile</title>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<style>
body { font-family: Arial, sans-serif; margin:0; padding:0; background:#f0f2f5; }
header { background:#4CAF50; color:white; padding:15px 20px; display:flex; justify-content:space-between; align-items:center; }
header a { color:white; text-decoration:none; font-weight:bold; }
header a:hover { text-decoration:underline; }
.container { padding:20px; max-width:500px; margin:0 auto; }
.card { background:white; padding:24px; border-radius:10px; box-shadow:0 1px 3px rgba(0,0,0,0.1); margin-bottom:20px; }
.card h3 { margin-top:0; margin-bottom:16px; color:#333; }
.info-row { margin-bottom:12px; }
.info-row label { display:block; font-size:12px; color:#666; margin-bottom:4px; }
.info-row .value { padding:8px 12px; background:#f5f5f5; border-radius:6px; color:#333; }
input[type="password"], input[type="text"] { width:100%; padding:10px 12px; margin:6px 0; border:1px solid #ccc; border-radius:6px; box-sizing:border-box; }
button { padding:10px 20px; border:none; border-radius:6px; cursor:pointer; font-size:14px; }
button.primary { background:#4CAF50; color:white; }
button.primary:hover { background:#45a049; }
.message { margin-top:10px; text-align:center; font-size:14px; }
.loading { text-align:center; color:#666; padding:20px; }
</style>
</head>
<body>

<header>
    <a href="/dashboard">&larr; Dashboard</a>
    <span id="header-name">Profile</span>
    <button onclick="logout()" style="background:rgba(255,255,255,0.2); color:white; padding:6px 12px;">Logout</button>
</header>

<div class="container">
    <div id="loading" class="card loading">Loading profile...</div>

    <div id="profile-content" style="display:none;">
        <!-- User info (read-only) -->
        <div class="card">
            <h3>Account info</h3>
            <div class="info-row">
                <label>Name</label>
                <div class="value" id="profile-name"></div>
            </div>
            <div class="info-row">
                <label>Email</label>
                <div class="value" id="profile-email"></div>
            </div>
        </div>

        <!-- Change password -->
        <div class="card">
            <h3>Change password</h3>
            <input type="password" id="current-password" placeholder="Current password" autocomplete="current-password">
            <input type="password" id="new-password" placeholder="New password" autocomplete="new-password">
            <input type="password" id="new-password-confirm" placeholder="Confirm new password" autocomplete="new-password">
            <button class="primary" onclick="changePassword()">Update password</button>
            <div class="message" id="password-message"></div>
        </div>
    </div>
</div>

<script>
const apiBase = '/api';
const token = localStorage.getItem('auth_token');
if (!token) {
    window.location.href = '/login';
}
const headers = { Authorization: 'Bearer ' + token };

function logout() {
    axios.post(`${apiBase}/logout`, {}, { headers })
        .then(() => {
            localStorage.removeItem('auth_token');
            window.location.href = '/login';
        })
        .catch(() => { window.location.href = '/login'; });
}

function loadProfile() {
    axios.get(`${apiBase}/user`, { headers })
        .then(res => {
            const user = res.data.user;
            document.getElementById('header-name').textContent = user.name;
            document.getElementById('profile-name').textContent = user.name;
            document.getElementById('profile-email').textContent = user.email;
            document.getElementById('loading').style.display = 'none';
            document.getElementById('profile-content').style.display = 'block';
        })
        .catch(err => {
            if (err.response && (err.response.status === 401 || err.response.status === 403)) {
                localStorage.removeItem('auth_token');
                window.location.href = '/login';
            } else {
                document.getElementById('loading').textContent = 'Failed to load profile.';
            }
        });
}

function changePassword() {
    const current = document.getElementById('current-password').value;
    const newPass = document.getElementById('new-password').value;
    const confirmPass = document.getElementById('new-password-confirm').value;
    const msgEl = document.getElementById('password-message');

    if (!current) {
        msgEl.style.color = 'red';
        msgEl.textContent = 'Please enter your current password.';
        return;
    }
    if (!newPass) {
        msgEl.style.color = 'red';
        msgEl.textContent = 'Please enter a new password.';
        return;
    }
    if (newPass.length < 6) {
        msgEl.style.color = 'red';
        msgEl.textContent = 'New password must be at least 6 characters.';
        return;
    }
    if (newPass !== confirmPass) {
        msgEl.style.color = 'red';
        msgEl.textContent = 'New password and confirmation do not match.';
        return;
    }

    msgEl.textContent = '';
    axios.put(`${apiBase}/profile/password`, {
        current_password: current,
        password: newPass,
        password_confirmation: confirmPass
    }, { headers })
        .then(() => {
            msgEl.style.color = 'green';
            msgEl.textContent = 'Password updated successfully.';
            document.getElementById('current-password').value = '';
            document.getElementById('new-password').value = '';
            document.getElementById('new-password-confirm').value = '';
        })
        .catch(err => {
            msgEl.style.color = 'red';
            msgEl.textContent = err.response?.data?.message || 'Failed to update password.';
        });
}

loadProfile();
</script>

</body>
</html>
