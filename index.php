<?php
header('Content-Type: text/html; charset=utf-8');

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'env.php';
require_once 'license.php';

// ================= SETTINGS =================
$settingsStmt = $pdo->query("
    SELECT setting_key, setting_value
    FROM settings
");

$settingsRows = $settingsStmt->fetchAll(PDO::FETCH_ASSOC);

$settings = [];

foreach ($settingsRows as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// ================= GAMES =================
$stmt = $pdo->query("
    SELECT *
    FROM game_packages
    ORDER BY sort_order ASC
");

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$gamesData = [];

foreach ($rows as $row) {

    $gameName = $row['game_name'];

    if (!isset($gamesData[$gameName])) {

        $gamesData[$gameName] = [
            'id' => strtolower(
                preg_replace('/[^a-zA-Z0-9]/', '', $gameName)
            ),
            'name' => $gameName,
            'icon' => $row['icon'],
            'packages' => []
        ];
    }

    $gamesData[$gameName]['packages'][] = [
        'value' => $row['diamonds'],
        'text' => $row['package_name'] . ' - ' . number_format($row['price']) . ' đ'
    ];
}

$gamesJson = json_encode(
    array_values($gamesData),
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);
?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars($settings['site_name'] ?? 'Napthe.vn') ?>
    </title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: #f5f5f5;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .top-banner {
            background: linear-gradient(90deg, #e62e2e, #ff8c00);
            color: white;
            padding: 12px 0;
            text-align: center;
            font-weight: bold;
            border-radius: 0 0 10px 10px;
        }

        .main-banner {
            height: 320px;

            background:
                url('<?= htmlspecialchars($settings['banner_image'] ?? '') ?>')
                center/cover no-repeat;

            border-radius: 0 0 15px 15px;

            position: relative;

            display: flex;

            align-items: center;

            justify-content: center;

            color: white;

            text-align: center;

            overflow: hidden;
        }

        .main-banner::before {

            content: '';

            position: absolute;

            top: 0;

            left: 0;

            width: 100%;

            height: 100%;

            background: rgba(0,0,0,0.5);
        }

        .banner-content {

            position: relative;

            z-index: 2;
        }

        .banner-content h1 {

            font-size: 42px;

            margin-bottom: 10px;
        }

        .banner-content p {

            font-size: 18px;
        }

        .game-list {

            display: flex;

            gap: 15px;

            overflow-x: auto;

            padding: 20px 0;

            white-space: nowrap;
        }

        .game-item {

            text-align: center;

            cursor: pointer;

            min-width: 130px;
        }

        .game-item img {

            width: 90px;

            height: 90px;

            object-fit: cover;

            border-radius: 15px;

            border: 3px solid transparent;

            transition: 0.3s;
        }

        .game-item.active img {

            border-color: #e62e2e;

            transform: scale(1.1);
        }

        .main-content {

            display: flex;

            gap: 30px;

            margin: 30px 0;

            flex-wrap: wrap;
        }

        .form-card {

            flex: 1;

            min-width: 380px;

            background: white;

            border-radius: 12px;

            padding: 30px;

            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .form-group {

            margin-bottom: 20px;
        }

        label {

            display: block;

            margin-bottom: 8px;

            font-weight: 600;
        }

        input,
        select {

            width: 100%;

            padding: 14px 15px;

            border: 1px solid #ddd;

            border-radius: 8px;

            font-size: 16px;
        }

        .btn-submit {

            background: #e62e2e;

            color: white;

            border: none;

            padding: 16px;

            font-size: 19px;

            font-weight: bold;

            border-radius: 8px;

            width: 100%;

            cursor: pointer;

            margin-top: 15px;
        }

    </style>
</head>

<body>

<div class="container">

    <div class="top-banner">
        <?= htmlspecialchars($settings['promotion_text'] ?? '') ?>
    </div>

    <div class="main-banner">

        <div class="banner-content">

            <h1>
                <?= htmlspecialchars($settings['main_banner_title'] ?? '') ?>
            </h1>

            <p>
                <?= htmlspecialchars($settings['main_banner_desc'] ?? '') ?>
            </p>

        </div>

    </div>

    <!-- GAME LIST -->
    <div class="game-list" id="gameList"></div>

    <div class="main-content">

        <div class="form-card">

            <h2 id="formTitle">🔥 Nạp Game</h2>

            <div class="form-group">

                <label>UID / Tên nhân vật</label>

                <input
                    type="text"
                    id="userId"
                    placeholder="Nhập UID game"
                >
            </div>

            <div class="form-group">

                <label>Chọn gói nạp</label>

                <select id="package"></select>

            </div>

            <!-- PROVIDER -->
            <div class="form-group">

                <label>Phương thức thanh toán</label>

                <select
                    id="provider"
                    onchange="toggleQR()"
                >

                    <option value="viettel">Viettel</option>

                    <option value="vinaphone">Vinaphone</option>

                    <option value="mobifone">Mobifone</option>

                    <option value="garena">Garena</option>

                    <option value="qr">QR Code</option>

                </select>
            </div>

            <!-- CARD -->
            <div id="cardFields">

                <div class="form-group">

                    <label>Mã thẻ</label>

                    <input
                        type="text"
                        id="cardNumber"
                        placeholder="Nhập mã thẻ"
                    >
                </div>

                <div class="form-group">

                    <label>Số serial</label>

                    <input
                        type="text"
                        id="serialNumber"
                        placeholder="Nhập số serial"
                    >
                </div>

            </div>

            <!-- QR -->
            <div id="qrSection"
                 style="
                    display:none;
                    text-align:center;
                    padding:20px;
                    background:#f8f9fa;
                    border:2px dashed #e62e2e;
                    border-radius:12px;
                 ">

                <p>
                    <strong>Quét mã QR để thanh toán</strong>
                </p>

                <img
                    src="<?= htmlspecialchars($settings['qr_image'] ?? '') ?>"
                    alt="QR"
                    style="
                        width:280px;
                        margin:15px 0;
                    "
                >

                <br>

                <button
                    type="button"
                    onclick="submitNapThe()"
                    style="
                        background:#28a745;
                        color:white;
                        border:none;
                        padding:14px 25px;
                        border-radius:8px;
                        font-size:16px;
                        font-weight:bold;
                        cursor:pointer;
                    "
                >
                    ✅ Xác nhận thanh toán
                </button>

            </div>

            <!-- SUBMIT -->
            <button
                onclick="submitNapThe()"
                class="btn-submit"
                id="submitBtn"
            >

                <i class="fas fa-bolt"></i>

                NẠP NGAY

            </button>

        </div>

    </div>

</div>

<script>

const games = <?= $gamesJson ?>;

let currentGame =
    games.length > 0
        ? games[0]
        : null;

// ================= RENDER GAMES =================
function renderGames() {

    const container =
        document.getElementById('gameList');

    container.innerHTML = '';

    games.forEach(game => {

        const div =
            document.createElement('div');

        div.className =
            `game-item ${
                currentGame &&
                game.id === currentGame.id
                    ? 'active'
                    : ''
            }`;

        div.innerHTML = `
            <img src="${game.icon || 'default-game.png'}">
            <span>${game.name}</span>
        `;

        div.onclick = () => selectGame(game);

        container.appendChild(div);
    });
}

// ================= SELECT GAME =================
function selectGame(game) {

    currentGame = game;

    renderGames();

    document.getElementById('formTitle').textContent =
        `🔥 Nạp ${game.name}`;

    const select =
        document.getElementById('package');

    select.innerHTML =
        '<option value="">Chọn gói nạp</option>';

    game.packages.forEach(p => {

        const opt =
            document.createElement('option');

        opt.value = p.value;

        opt.textContent = p.text;

        select.appendChild(opt);
    });
}

// ================= QR TOGGLE =================
function toggleQR() {

    const isQR =
        document.getElementById('provider').value === 'qr';

    document.getElementById('cardFields').style.display =
        isQR ? 'none' : 'block';

    document.getElementById('qrSection').style.display =
        isQR ? 'block' : 'none';

    document.getElementById('submitBtn').style.display =
        isQR ? 'none' : 'block';
}

// ================= SUBMIT =================
async function submitNapThe() {

    if (!currentGame) {

        return alert('Chưa có game nào!');
    }

    const userId =
        document.getElementById('userId').value.trim();

    const packageSelect =
        document.getElementById('package').value;

    const provider =
        document.getElementById('provider').value;

    if (!userId || !packageSelect) {

        return alert('Vui lòng nhập đầy đủ thông tin!');
    }

    if (provider !== 'qr') {

        const card =
            document.getElementById('cardNumber')
                .value.trim();

        const seri =
            document.getElementById('serialNumber')
                .value.trim();

        if (!card || !seri) {

            return alert(
                'Vui lòng nhập mã thẻ và serial!'
            );
        }
    }

    const data = {

        userId: userId,

        game: currentGame.name,

        package: packageSelect,

        value:
            document.getElementById('package')
                .options[
                    document.getElementById('package')
                        .selectedIndex
                ].text,

        provider: provider,

        cardNumber:
            document.getElementById('cardNumber')
                ? document.getElementById('cardNumber')
                    .value.trim()
                : '',

        serialNumber:
            document.getElementById('serialNumber')
                ? document.getElementById('serialNumber')
                    .value.trim()
                : ''
    };

    try {

        const res = await fetch(
            'process.php',
            {
                method: 'POST',

                headers: {
                    'Content-Type':
                        'application/json'
                },

                body: JSON.stringify(data)
            }
        );

        const result = await res.json();

        alert(
            result.status === 'success'
                ? '✅ Giao dịch đã gửi!'
                : '❌ ' + result.message
        );

    } catch (e) {

        alert('❌ Lỗi kết nối server!');
    }
}

// ================= INIT =================
window.onload = () => {

    if (games.length === 0) {

        document.getElementById('gameList').innerHTML =
            '<p>Chưa có game nào.</p>';

        return;
    }

    renderGames();

    selectGame(games[0]);
};

</script>

</body>
</html>