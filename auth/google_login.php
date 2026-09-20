<?php
/**
 * Google OAuth 2.0 Login Redirect
 * Menginisialisasi parameter state dan redirect ke Google Consent Screen
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

// Cek apakah kredensial Google OAuth sudah dikonfigurasi
if (GOOGLE_CLIENT_ID === 'YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com' || empty(GOOGLE_CLIENT_ID)) {
    set_flash('danger', 'Google OAuth belum dikonfigurasi. Silakan masukkan GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET di file config.php.');
    redirect(BASE_URL . '/guestbook');
}

// Generate random state untuk mencegah CSRF pada OAuth callback
$oauthState = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $oauthState;

// Susun URL otorisasi Google
$authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
    'client_id'     => GOOGLE_CLIENT_ID,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope'         => 'openid email profile',
    'state'         => $oauthState,
    'access_type'   => 'online',
    'prompt'        => 'select_account'
]);

// Redirect ke Google
header('Location: ' . $authUrl);
exit;
