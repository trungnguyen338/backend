<?php
function isEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function hashPassword($password)
{
    return password_hash($password, PASSWORD_BCRYPT);
}
function verifyPassword($password, $hash)
{
    return password_verify($password, $hash);
}

function isValidPhone($phone)
{
    $phone = preg_replace('/\s+|-/', '', $phone);
    $pattern = '/^(03|05|07|08|09)\d{8}$/';
    return preg_match($pattern, $phone) === 1;
}
