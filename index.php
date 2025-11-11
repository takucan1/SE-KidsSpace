<?php

    session_start();
    $errors = [
        'login' => $_SESSION['login_error'] ?? '',
        'register' => $_SESSION['register_error'] ?? ''
    ];

    $activeForm = $_SESSION['active_form'] ?? 'login';

    session_unset();

    function showError($errors, $activeForm) {
        return !empty($errors) ? "<p class='error-message'>$errors</p>" : '';
    }

    function isActiveForm($formName, $activeForm) {
        return $formName === $activeForm ? 'active' : '';
    }
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KidsSpace</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="login-page">     
    <div class="container">
        <div class="form-box <?= isActiveForm('login', $activeForm); ?>" id="login-form">
            <form action="login_register.php" method="post">
                <p class="kids-space">
                    <span class="span">&nbsp;</span>
                    <span class="text-wrapper-2">K</span>
                    <span class="text-wrapper-3">i</span>
                    <span class="text-wrapper-4">d</span>
                    <span class="text-wrapper-2">sS</span>
                    <span class="text-wrapper-5">p</span>
                    <span class="text-wrapper-3">a</span>
                    <span class="text-wrapper-4">c</span>
                    <span class="text-wrapper-2">e</span>
                </p>
                <h2>Login</h2>
                <?= showError($errors['login'], $activeForm);?>
                <input type="email" name="email" placeholder="Email" required>  
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
                <p>Don't have an account? <a href="#" onclick="showForm('register-form')">Register</a></p>
            </form>
        </div>
    </div>


    <div class="form-box <?= isActiveForm('register', $activeForm); ?>" id="register-form">
            <form action="login_register.php" method="post">
                <h2>Register</h2>
                <?= showError($errors['register'], $activeForm);?>
                <input type="name" name="name" placeholder="Name" required>
                <input type="email" name="email" placeholder="Email" required>  
                <input type="password" name="password" placeholder="Password" required>
                <select name="role" >
                    <option value="">---Select Role---</option>
                    <option value="student">student</option>
                    <option value="teacher">teacher</option>
                </select>
                 <input type="checkbox" name="terms" required> I understand the terms and conditions</input>
                <button type="submit" name="register">Register</button>
                <p>Already have an account? <a href="#" onclick="showForm('login-form')">Login</a></p>
            </form>
        </div>

        <script src="script.js"></script>

    </main>
</body>
</html>