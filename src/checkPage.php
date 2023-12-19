<?php
function isLoginPage() {
    // Check if the "page" parameter is set to "login"
    if (isset($_GET['page']) && $_GET['page'] === 'login') {
        return true;
    }
    return false;
}

// Function to check if the current page is the register page
function isRegisterPage() {
    // Check if the "page" parameter is set to "register"
    if (isset($_GET['page']) && $_GET['page'] === 'register') {
        return true;
    }
    return false;
}
