<?php
session_start();
require_once 'db.php';

// Fetch global settings dynamically from database for brand consistency
$global_settings = [];
$res = $conn->query("SELECT setting_key, setting_value FROM settings");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $global_settings[$row['setting_key']] = $row['setting_value'];
    }
}

$APP_NAME = $global_settings['ai_name'] ?? 'Atlas AI';
$COMPANY_NAME = $global_settings['company_name'] ?? 'Atlas Support';
$PRIMARY_ACCENT = $global_settings['primary_accent'] ?? '#4da3ff';
$DARK_SHELL = $global_settings['dark_shell_tone'] ?? '#1e1f2b';
$SIDEBAR_BG = $global_settings['sidebar_bg'] ?? '#11131d';
?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($APP_NAME) ?> Administration</title>

<style>
*{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body{
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #050b1f, <?= htmlspecialchars($DARK_SHELL) ?>);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
}

.admin-card{
    width: 550px;
    background: <?= htmlspecialchars($SIDEBAR_BG) ?>;
    border-radius: 20px;
    padding: 40px;
    text-align: center;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.45);
    border: 1px solid #2f3246;
}

h1{
    font-size: 38px;
    margin-bottom: 15px;
}

.description{
    color: #e4e8ff;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 25px;
}

.last-login{
    margin-bottom: 20px;
    color: #c9d2ff;
    font-size: 13px;
}

input{
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    background: <?= htmlspecialchars($DARK_SHELL) ?>;
    color: white;
    border: 1px solid #2f3246;
    outline: none;
}

input:focus {
    border-color: <?= htmlspecialchars($PRIMARY_ACCENT) ?>;
}

.login-btn{
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 8px;
    background: #dc3545;
    color: white;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.2s;
}

.login-btn:hover{
    background: #c82333;
}

.back-link{
    margin-top: 15px;
}

.back-link a{
    color: <?= htmlspecialchars($PRIMARY_ACCENT) ?>;
    text-decoration: none;
}

.back-link a:hover{
    text-decoration: underline;
}

.feature-box{
    background: #252a41;
    padding: 12px;
    border-radius: 8px;
    margin-top: 15px;
    margin-bottom: 15px;
    text-align: left;
    border: 1px solid #2f3246;
}

.warning{
    margin-top: 20px;
    padding: 15px;
    border-radius: 10px;
    background: #3e2640;
    border-left: 5px solid #ff5a5a;
    text-align: left;
    line-height: 1.6;
}

.footer{
    margin-top: 25px;
    font-size: 12px;
    color: #c5cef7;
    line-height: 1.6;
    border-top: 1px solid #2f3246;
    padding-top: 15px;
}
</style>

</head>
<body>

<div class="admin-card">

    <h1><?= htmlspecialchars($APP_NAME) ?> Administration</h1>

    <div class="last-login">
        🕒 Last Login: Not Available
    </div>

    <p class="description">
        Manage users, departments, security controls, knowledge repositories and <?= htmlspecialchars($APP_NAME) ?> platform settings.
    </p>

    <div>
        <input
            type="text"
            id="username"
            placeholder="Administrator Username"
            required>

        <input
            type="password"
            id="password"
            placeholder="Password"
            required>

        <button
            type="button"
            class="login-btn"
            onclick="login()">
            Sign In
        </button>
    </div>

    <div class="back-link">
        <a href="chat.php">
            ← Back to <?= htmlspecialchars($APP_NAME) ?>
        </a>
    </div>

    <div class="feature-box">
        🔐 Multi-Factor Authentication
    </div>

    <div class="warning">
        <strong>⚠ Administrator Access Only</strong>
        <br><br>
        Unauthorized access is prohibited. Activity may be monitored, logged and audited.
        <br><br>
        MFA Status: Not Enabled
    </div>

    <div class="footer">
        Powered by <?= htmlspecialchars($COMPANY_NAME) ?><br>
        <?= htmlspecialchars($APP_NAME) ?> Administration v1.0
    </div>

</div>

<script>
async function login(){
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;

    try {
        const response = await fetch(
            "login.php",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    username,
                    password
                })
            }
        );

        const result = await response.json();

        if(result.success){
            window.location.href = "dashboard.php";
        } else {
            alert("Invalid login");
        }
    } catch(err) {
        console.error(err);
        alert("An error occurred during sign in.");
    }
}
</script>

</body>
</html>
