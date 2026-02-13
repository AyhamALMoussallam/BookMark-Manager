<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Google Callback</title>
<script>
    // API backend لازم يرجع token كـ ?token=...
    const token = new URLSearchParams(window.location.search).get('token');
    if(token) localStorage.setItem('auth_token', token);
    window.location.href = '/dashboard'; // redirect مباشرة بعد تخزين الـ token
</script>
</head>
<body></body>
</html>
