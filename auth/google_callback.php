<?php
/**
 * Google OAuth 2.0 Callback Handler
 * Menerima authorization code dari Google, menukar token, mengambil info user, dan menyimpan ke session visitor
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

// Cek error dari Google
if (!empty($_GET['error'])) {
    set_flash('danger', 'Login Google dibatalkan atau gagal: ' . e($_GET['error']));
    redirect(BASE_URL . '/pages/guestbook.php');
}

// Validasi state parameter untuk mencegah CSRF
$returnedState = $_GET['state'] ?? '';
$sessionState = $_SESSION['oauth_state'] ?? '';
unset($_SESSION['oauth_state']); // Hapus state setelah dipakai

if (empty($returnedState) || empty($sessionState) || !hash_equals($sessionState, $returnedState)) {
    set_flash('danger', 'Validasi sesi login Google tidak valid (State mismatch). Silakan coba lagi.');
    redirect(BASE_URL . '/pages/guestbook.php');
}

$code = $_GET['code'] ?? '';
if (empty($code)) {
    set_flash('danger', 'Otorisasi Google tidak mengembalikan authorization code.');
    redirect(BASE_URL . '/pages/guestbook.php');
}

// --------------------------------------------------------------------------
// 1. Tukar authorization code dengan Access Token via POST ke Google
// --------------------------------------------------------------------------
$tokenEndpoint = 'https://oauth2.googleapis.com/token';
$postData = http_build_query([
    'code'          => $code,
    'client_id'     => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'grant_type'    => 'authorization_code'
]);

$ch = curl_init($tokenEndpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$tokenResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if (!$tokenResponse || $httpCode !== 200) {
    // Fallback file_get_contents jika cURL gagal
    $opts = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $postData,
            'timeout' => 15
        ]
    ];
    $context = stream_context_create($opts);
    $tokenResponse = @file_get_contents($tokenEndpoint, false, $context);
}

$tokenData = json_decode((string)$tokenResponse, true);
if (empty($tokenData['access_token'])) {
    error_log("Google OAuth Token Exchange Failed: " . (string)$tokenResponse);
    set_flash('danger', 'Gagal menukarkan token otentikasi dengan Google. Pastikan Client ID dan Secret di config.php sudah benar.');
    redirect(BASE_URL . '/pages/guestbook.php');
}

$accessToken = $tokenData['access_token'];

// --------------------------------------------------------------------------
// 2. Ambil Informasi Profil User via Userinfo Endpoint
// --------------------------------------------------------------------------
$userInfoEndpoint = 'https://openidconnect.googleapis.com/v1/userinfo';

$ch = curl_init($userInfoEndpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $accessToken,
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$userResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if (!$userResponse || $httpCode !== 200) {
    $opts = [
        'http' => [
            'method' => 'GET',
            'header' => "Authorization: Bearer " . $accessToken . "\r\nAccept: application/json\r\n",
            'timeout' => 15
        ]
    ];
    $context = stream_context_create($opts);
    $userResponse = @file_get_contents($userInfoEndpoint, false, $context);
}

$userData = json_decode((string)$userResponse, true);

if (empty($userData['sub'])) {
    error_log("Google OAuth Userinfo Failed: " . (string)$userResponse);
    set_flash('danger', 'Gagal memuat profil pengguna dari Google.');
    redirect(BASE_URL . '/pages/guestbook.php');
}

// --------------------------------------------------------------------------
// 3. Simpan ke Session Visitor (Google Guest User)
// Terpisah secara ketat dari session admin
// --------------------------------------------------------------------------
set_guest_user([
    'google_id' => $userData['sub'],
    'name'      => $userData['name'] ?? 'Pengunjung Google',
    'email'     => $userData['email'] ?? '',
    'avatar'    => $userData['picture'] ?? ''
]);

set_flash('success', 'Selamat datang, ' . e($userData['name'] ?? 'Pengunjung') . '! Anda telah terverifikasi melalui Google.');
redirect(BASE_URL . '/pages/guestbook.php');
