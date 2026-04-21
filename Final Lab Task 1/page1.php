<?php

$loginErrors = [];
$loginSuccess = "";
$changeErrors = [];
$changeSuccess = "";

function sanitize($data)
{
 return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function isValidUsername($username)
{
 return preg_match('/^[A-Za-z0-9._-]+$/', $username);
}

function hasRequiredSpecialChar($password)
{
 return preg_match('/[@#$%]/', $password);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 $formType = $_POST['form_type'] ?? '';

 if ($formType === 'login') {
 $username = trim($_POST['username'] ?? '');
 $password = trim($_POST['password'] ?? '');

 if ($username === '') {
 $loginErrors[] = 'User Name is required.';
 } else {
 if (strlen($username) < 2) {
 $loginErrors[] = 'User Name must contain at least two (2) characters.';
 }
 if (!isValidUsername($username)) {
 $loginErrors[] = 'User Name can contain alpha numeric characters, period, dash or underscore only.';
 }
 }

 if ($password === '') {
 $loginErrors[] = 'Password is required.';
 } else {
 if (strlen($password) < 8) {
 $loginErrors[] = 'Password must not be less than eight (8) characters.';
 }
 if (!hasRequiredSpecialChar($password)) {
 $loginErrors[] = 'Password must contain at least one special character (@, #, $, %).';
 }
 }

 if (empty($loginErrors)) {
 $loginSuccess = 'Login form validation passed successfully.';
 }
 }

 if ($formType === 'change_password') {
 $currentPassword = trim($_POST['current_password'] ?? '');
 $newPassword = trim($_POST['new_password'] ?? '');
 $retypePassword = trim($_POST['retype_password'] ?? '');

 if ($currentPassword === '') {
 $changeErrors[] = 'Current Password is required.';
 }

 if ($newPassword === '') {
 $changeErrors[] = 'New Password is required.';
 } else {
 if (strlen($newPassword) < 8) {
 $changeErrors[] = 'New Password must not be less than eight (8) characters.';
 }
 if (!hasRequiredSpecialChar($newPassword)) {
 $changeErrors[] = 'New Password must contain at least one special character (@, #, $, %).';
 }
 }

 if ($retypePassword === '') {
 $changeErrors[] = 'Retype New Password is required.';
 } elseif ($newPassword !== $retypePassword) {
 $changeErrors[] = 'New Password and Retype New Password must match.';
 }

 if (empty($changeErrors)) {
 $changeSuccess = 'Change Password form validation passed successfully.';
 }
 }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Assessment Task</title>
 <style>
 body {
 font-family: "Times New Roman", serif;
 background: #ffffff;
 color: #000;
 margin: 20px 60px;
 line-height: 1.35;
 }

 h2 {
 font-size: 20px;
 margin-bottom: 14px;
 }

 .task-block {
 margin-bottom: 42px;
 }

 .task-title {
 font-size: 18px;
 margin-bottom: 10px;
 }

 fieldset {
 width: 420px;
 border: 2px solid #8f8f8f;
 padding: 12px 16px 18px 16px;
 margin: 10px 0 18px 42px;
 }

 legend {
 font-size: 22px;
 font-weight: bold;
 padding: 0 4px;
 }

 table {
 width: 100%;
 border-collapse: collapse;
 }

 td {
 padding: 4px 4px;
 vertical-align: middle;
 font-size: 18px;
 }

 .label-cell {
 width: 180px;
 }

 .colon-cell {
 width: 18px;
 text-align: center;
 }

 input[type="text"],
 input[type="password"] {
 width: 210px;
 height: 24px;
 border: 1px solid #9f9f9f;
 font-size: 16px;
 padding: 2px 6px;
 box-sizing: border-box;
 }

 .hr-line {
 border: none;
 border-top: 1px solid #bfbfbf;
 margin: 14px 0 12px;
 }

 .remember-row {
 font-size: 18px;
 margin-bottom: 26px;
 }

 .actions {
 display: flex;
 align-items: center;
 gap: 8px;
 }

 button {
 font-family: "Times New Roman", serif;
 font-size: 18px;
 padding: 3px 10px;
 cursor: pointer;
 }

 a {
 color: blue;
 font-size: 18px;
 }

 .rules {
 margin-left: 48px;
 font-size: 18px;
 }

 .rules h3 {
 margin: 0 0 8px 0;
 font-size: 20px;
 }

 .rules ol,
 .rules ul {
 margin: 0;
 padding-left: 26px;
 }

 .error-box {
 width: 420px;
 margin: 10px 0 10px 42px;
 border: 1px solid #c00000;
 background: #fff3f3;
 color: #900;
 padding: 10px 14px;
 }

 .success-box {
 width: 420px;
 margin: 10px 0 10px 42px;
 border: 1px solid #0a7a0a;
 background: #f0fff0;
 color: #0a5c0a;
 padding: 10px 14px;
 }

 .green-label {
 color: green;
 }

 .red-label {
 color: red;
 }
 </style>
</head>
<body>
 <h2>ASSESSMENT TASK</h2>

 <div class="task-block">
 <div class="task-title">
 <strong>1.</strong>
 &nbsp; Design the following HTML form and perform following validations
 </div>

 <?php if (!empty($loginErrors)): ?>
 <div class="error-box">
 <strong>Login Form Errors:</strong>
 <ul>
 <?php foreach ($loginErrors as $error): ?>
 <li><?php echo sanitize($error); ?></li>
 <?php endforeach; ?>
 </ul>
 </div>
 <?php endif; ?>

 <?php if ($loginSuccess !== ''): ?>
 <div class="success-box">
 <?php echo sanitize($loginSuccess); ?>
 </div>
 <?php endif; ?>

 <form method="post" action="">
 <fieldset>
 <legend>LOGIN</legend>
 <input type="hidden" name="form_type" value="login">

 <table>
 <tr>
 <td class="label-cell"><label for="username"><strong>User Name</strong></label></td>
 <td class="colon-cell">:</td>
 <td>
 <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) && ($_POST['form_type'] ?? '') === 'login' ? sanitize($_POST['username']) : ''; ?>">
 </td>
 </tr>
 <tr>
 <td class="label-cell"><label for="password"><strong>Password</strong></label></td>
 <td class="colon-cell">:</td>
 <td>
 <input type="password" id="password" name="password">
 </td>
 </tr>
 </table>

 <hr class="hr-line">

 <div class="remember-row">
 <label>
 <input type="checkbox" name="remember_me" <?php echo isset($_POST['remember_me']) && ($_POST['form_type'] ?? '') === 'login' ? 'checked' : ''; ?>>
 Remember Me
 </label>
 </div>

 <div class="actions">
 <button type="submit">Submit</button>
 <a href="#">Forgot Password?</a>
 </div>
 </fieldset>
 </form>

 </div>

 <?php if (!empty($changeErrors)): ?>
 <div class="error-box">
 <strong>Change Password Form Errors:</strong>
 <ul>
 <?php foreach ($changeErrors as $error): ?>
 <li><?php echo sanitize($error); ?></li>
 <?php endforeach; ?>
 </ul>
 </div>
 <?php endif; ?>

 <?php if ($changeSuccess !== ''): ?>
 <div class="success-box">
 <?php echo sanitize($changeSuccess); ?>
 </div>
 <?php endif; ?>

 <form method="post" action="">
 <fieldset>
 <legend>CHANGE PASSWORD</legend>
 <input type="hidden" name="form_type" value="change_password">

 <table>
 <tr>
 <td class="label-cell"><label for="current_password"><strong>Current Password</strong></label></td>
 <td class="colon-cell">:</td>
 <td>
 <input type="password" id="current_password" name="current_password">
 </td>
 </tr>
 <tr>
 <td class="label-cell green-label"><label for="new_password"><strong>New Password</strong></label></td>
 <td class="colon-cell">:</td>
 <td>
 <input type="password" id="new_password" name="new_password">
 </td>
 </tr>
 <tr>
 <td class="label-cell red-label"><label for="retype_password"><strong>Retype New Password</strong></label></td>
 <td class="colon-cell">:</td>
 <td>
 <input type="password" id="retype_password" name="retype_password">
 </td>
 </tr>
 </table>

 <hr class="hr-line">

 <div class="actions">
 <button type="submit">Submit</button>
 </div>
 </fieldset>
 </form>
 </div>
</body>
</html>