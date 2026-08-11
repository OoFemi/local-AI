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

$isLoggedIn = isset($_SESSION['user_id']);

$user = [
    'username' => 'Guest User',
    'department' => '-',
    'role' => 'Guest',
    'theme' => 'system',
    'response_style' => 'balanced',
    'last_login' => '-'
];

if ($isLoggedIn) {
    $userId = $_SESSION['user_id'];

    $stmt = $conn->prepare("
        SELECT
            username,
            department,
            role,
            theme,
            response_style,
            last_login
        FROM users
        WHERE id = ?
    ");

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $user = $row;
    }
}

// Clean up username/email display logic safely in PHP
$rawUsername = $user['username'];
if (strpos($rawUsername, '@') !== false) {
    $parts = explode('@', $rawUsername);
    $displayUsername = ucfirst($parts[0]);
} else {
    $nameParts = explode(' ', $rawUsername);
    $displayUsername = count($nameParts) > 1 ? $nameParts[0] . ' ' . $nameParts[count($nameParts) - 1] : $nameParts[0];
}
if (!$isLoggedIn) {
    $displayUsername = 'Guest User';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($APP_NAME) ?> - User Settings</title>

<style>
body {
    font-family: Segoe UI, sans-serif;
    background: #f5f6fa;
    color: #000;
    margin: 0;
    padding: 40px;
    transition: all .3s;
}

.container {
    max-width: 800px;
    margin: auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,.1);
    transition: all .3s;
}

.section {
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid #ddd;
}

h1, h2 {
    margin-bottom: 15px;
}

label {
    display: block;
    margin: 10px 0;
    cursor: pointer;
}

button {
    background: <?= htmlspecialchars($PRIMARY_ACCENT) ?>;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
}

button:hover {
    opacity: 0.9;
}

.back-link {
    margin-left: 15px;
    text-decoration: none;
    color: <?= htmlspecialchars($PRIMARY_ACCENT) ?>;
}

.back-link:hover {
    text-decoration: underline;
}

/* DARK MODE */
body.dark-mode {
    background: <?= htmlspecialchars($DARK_SHELL) ?>;
    color: white;
}

body.dark-mode .container {
    background: #11131d;
    color: white;
}

body.dark-mode .section {
    border-bottom: 1px solid #2f3246;
}

body.dark-mode a {
    color: <?= htmlspecialchars($PRIMARY_ACCENT) ?>;
}
</style>
</head>

<body>

<div class="container">

<h1>⚙ User Settings</h1>

<div class="section">
<h2>Account Information</h2>
<p>
<strong>Username:</strong>
<?php echo htmlspecialchars($displayUsername); ?>
</p>

<?php if($isLoggedIn): ?>
<p>
<strong>Department:</strong>
<?php echo htmlspecialchars($user['department']); ?>
</p>
<p>
<strong>Role:</strong>
<?php echo htmlspecialchars($user['role']); ?>
</p>
<p>
<strong>Last Login:</strong>
<?php echo htmlspecialchars($user['last_login']); ?>
</p>
<?php else: ?>
<p>
<strong>Role:</strong> Guest
</p>
<?php endif; ?>
</div>

<div class="section">
<h2>Appearance</h2>
<label><input type="radio" name="theme" value="light"> Light</label>
<label><input type="radio" name="theme" value="dark"> Dark</label>
<label><input type="radio" name="theme" value="system" checked> System Default</label>
</div>

<div class="section">
<h2>AI Response Style</h2>
<label><input type="radio" name="response_style" value="concise"> Concise</label>
<label><input type="radio" name="response_style" value="balanced" checked> Balanced</label>
<label><input type="radio" name="response_style" value="detailed"> Detailed</label>
</div>

<div class="section">
<h2>About <?= htmlspecialchars($APP_NAME) ?></h2>
<p>Version: 1.0.0 Enterprise</p>
<p>Powered by <?= htmlspecialchars($COMPANY_NAME) ?></p>
</div>

<button onclick="saveSettings()">
Save Settings
</button>

<a href="chat.php" class="back-link">
← Back to Chat
</a>

</div>

<script>
const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;

function applyTheme(theme){
    document.body.classList.remove("dark-mode");

    if(theme === "dark"){
        document.body.classList.add("dark-mode");
    }

    if(theme === "system"){
        if(window.matchMedia('(prefers-color-scheme: dark)').matches){
            document.body.classList.add("dark-mode");
        }
    }
}

const savedTheme = localStorage.getItem("theme") || "system";
applyTheme(savedTheme);

const themeRadio = document.querySelector(`input[name="theme"][value="${savedTheme}"]`);
if(themeRadio){
    themeRadio.checked = true;
}

const savedStyle = localStorage.getItem("response_style") || "balanced";
const styleRadio = document.querySelector(`input[name="response_style"][value="${savedStyle}"]`);
if(styleRadio){
    styleRadio.checked = true;
}

function saveSettings(){
    const theme = document.querySelector('input[name="theme"]:checked').value;
    const responseStyle = document.querySelector('input[name="response_style"]:checked').value;

    localStorage.setItem("theme", theme);
    localStorage.setItem("response_style", responseStyle);

    applyTheme(theme);

    if(!isLoggedIn){
        alert("Settings saved successfully.");
        return;
    }

    fetch(
        "save_user_settings.php",
        {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "theme=" + encodeURIComponent(theme) + "&response_style=" + encodeURIComponent(responseStyle)
        }
    )
    .then(() => {
        alert("Settings saved successfully.");
    });
}
</script>

</body>
</html>
