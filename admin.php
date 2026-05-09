<?php
session_start();
require_once 'env.php';
require_once 'license.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("mysql:host=".$_ENV['DB_HOST'].";dbname=".$_ENV['DB_NAME'].";charset=utf8mb4", 
               $_ENV['DB_USER'], $_ENV['DB_PASS']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ====================== XỬ LÝ AJAX ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';

    // Lưu Settings (Banner, Tên Web...)
    if ($action === 'save_settings') {
        $settings = [
            'site_name' => $input['site_name'] ?? 'Napthe.vn',
            'main_banner_title' => $input['main_banner_title'],
            'main_banner_desc' => $input['main_banner_desc'],
            'promotion_text' => $input['promotion_text']
        ];
        foreach ($settings as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
            $stmt->execute([$key, $value, $value]);
        }
        echo json_encode(['success' => true, 'message' => '✅ Lưu cài đặt thành công!']);
        exit;
    }

    // Thêm gói nạp
    if ($action === 'add_package') {
        $stmt = $pdo->prepare("INSERT INTO game_packages (game_name, icon, package_name, diamonds, price) VALUES (?,?,?,?,?)");
        $stmt->execute([$input['game_name'], $input['icon'], $input['package_name'], $input['diamonds'], $input['price']]);
        echo json_encode(['success' => true, 'message' => '✅ Thêm gói thành công!']);
        exit;
    }

    // Sửa gói nạp
    if ($action === 'update_package') {
        $stmt = $pdo->prepare("UPDATE game_packages SET game_name=?, icon=?, package_name=?, diamonds=?, price=? WHERE id=?");
        $stmt->execute([$input['game_name'], $input['icon'], $input['package_name'], $input['diamonds'], $input['price'], $input['id']]);
        echo json_encode(['success' => true, 'message' => '✅ Cập nhật thành công!']);
        exit;
    }

    // Xóa gói nạp
    if ($action === 'delete_package') {
        $stmt = $pdo->prepare("DELETE FROM game_packages WHERE id = ?");
        $stmt->execute([$input['id']]);
        echo json_encode(['success' => true]);
        exit;
    }

    // Load gói nạp
    if ($action === 'load_packages') {
        $stmt = $pdo->query("SELECT * FROM game_packages ORDER BY game_name, sort_order");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo $_ENV['SITE_NAME'] ?? 'Napthe.vn'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f9; margin:0; }
        .sidebar { width:260px; background:#2c3e50; color:white; position:fixed; height:100%; padding-top:20px; }
        .sidebar h2 { text-align:center; padding:20px; border-bottom:1px solid #34495e; }
        .sidebar li { padding:15px 25px; cursor:pointer; }
        .sidebar li:hover, .sidebar li.active { background:#34495e; }
        .main { margin-left:260px; padding:20px; }
        .card { background:white; border-radius:10px; padding:25px; box-shadow:0 4px 15px rgba(0,0,0,0.1); margin-bottom:20px; }
        input, textarea { width:100%; padding:10px; margin:8px 0; border:1px solid #ddd; border-radius:6px; }
        button { padding:12px 20px; background:#e62e2e; color:white; border:none; border-radius:6px; cursor:pointer; }
        .edit-btn { background:#28a745; }
        table { width:100%; border-collapse:collapse; margin-top:10px; }
        th, td { padding:12px; text-align:left; border-bottom:1px solid #eee; }
        .status { padding:5px 12px; border-radius:20px; font-size:14px; text-align:center; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>🔧 ADMIN PANEL</h2>
    <ul style="list-style:none; padding:0;">
        <li onclick="showTab(0)" class="active">🏠 Dashboard</li>
        <li onclick="showTab(1)">🏷️ Banner & Tên Web</li>
        <li onclick="showTab(2)">🎮 Quản Lý Gói Nạp</li>
        <li onclick="logout()" style="color:#ff6b6b;">🚪 Đăng xuất</li>
    </ul>
</div>

<div class="main">

    <!-- Tab 0: Dashboard -->
    <div id="tab0" class="tab">
        <h1>Chào mừng, <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong>!</h1>
        <div class="card">
            <h3>Thông tin hệ thống</h3>
            <p><strong>Website:</strong> <?php echo $_ENV['SITE_NAME'] ?? 'Napthe.vn'; ?></p>
            <p><strong>License:</strong> <?php echo $_ENV['LICENSE_KEY'] ?? 'Chưa có'; ?></p>
        </div>
    </div>

    <!-- Tab 1: Banner & Tên Web -->
    <div id="tab1" class="tab" style="display:none;">
        <h2>🏷️ Chỉnh Banner & Tên Web</h2>
        <div class="card">
            <label>Tên Website</label>
            <input type="text" id="site_name" value="<?php echo $_ENV['SITE_NAME'] ?? 'Napthe.vn'; ?>">
            <label>Tiêu đề Banner Chính</label>
            <input type="text" id="main_banner_title" value="NẠP THẺ GAME">
            <label>Mô tả Banner</label>
            <input type="text" id="main_banner_desc" value="Nhanh • An toàn • Giá tốt nhất">
            <label>Khuyến mãi</label>
            <textarea id="promotion_text" rows="3">Nạp từ 100.000đ tặng thêm 10% giá trị</textarea>
            <button onclick="saveSettings()">💾 Lưu Cài Đặt</button>
        </div>
    </div>

    <!-- Tab 2: Quản Lý Gói Nạp -->
    <div id="tab2" class="tab" style="display:none;">
        <h2>🎮 Quản Lý Gói Nạp</h2>
        <div class="card">
            <h3>Thêm / Sửa Gói Nạp</h3>
            <input type="hidden" id="edit_id">
            <input type="text" id="game_name" placeholder="Tên Game">
            <input type="text" id="icon" placeholder="Link Icon Game">
            <input type="text" id="package_name" placeholder="Tên gói">
            <input type="text" id="diamonds" placeholder="Số lượng">
            <input type="number" id="price" placeholder="Giá tiền">
            <button onclick="savePackage()">💾 Lưu Gói</button>
            <button onclick="resetForm()" style="background:#6c757d;">Reset</button>
        </div>

        <div class="card">
            <h3>Danh sách Gói Nạp</h3>
            <button onclick="loadPackages()">🔄 Tải Lại</button>
            <table id="packageTable">
                <thead><tr><th>Game</th><th>Icon</th><th>Gói</th><th>Số lượng</th><th>Giá</th><th>Hành động</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

<script>
let editingId = null;

function showTab(n) {
    document.querySelectorAll('.tab').forEach(t => t.style.display = 'none');
    document.getElementById('tab' + n).style.display = 'block';
}

async function saveSettings() {
    const data = {
        action: 'save_settings',
        site_name: document.getElementById('site_name').value,
        main_banner_title: document.getElementById('main_banner_title').value,
        main_banner_desc: document.getElementById('main_banner_desc').value,
        promotion_text: document.getElementById('promotion_text').value
    };
    await fetch('admin.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(data)});
    alert('✅ Đã lưu cài đặt!');
}

async function savePackage() {
    const data = {
        action: editingId ? 'update_package' : 'add_package',
        id: editingId,
        game_name: document.getElementById('game_name').value,
        icon: document.getElementById('icon').value,
        package_name: document.getElementById('package_name').value,
        diamonds: document.getElementById('diamonds').value,
        price: document.getElementById('price').value
    };

    if (!data.game_name || !data.package_name) return alert("Thiếu thông tin!");

    await fetch('admin.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(data)});
    alert(editingId ? '✅ Cập nhật thành công!' : '✅ Thêm thành công!');
    resetForm();
    loadPackages();
}

async function loadPackages() {
    const res = await fetch('admin.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({action:'load_packages'})});
    const packages = await res.json();
    let html = '';
    packages.forEach(p => {
        html += `<tr>
            <td>${p.game_name}</td>
            <td><img src="${p.icon}" width="50"></td>
            <td>${p.package_name}</td>
            <td>${p.diamonds}</td>
            <td>${Number(p.price).toLocaleString('vi-VN')} đ</td>
            <td>
                <button class="edit-btn" onclick="editPackage(${p.id},'${p.game_name}','${p.icon}','${p.package_name}','${p.diamonds}',${p.price})">Sửa</button>
                <button onclick="deletePackage(${p.id})" style="background:#dc3545;">Xóa</button>
            </td>
        </tr>`;
    });
    document.querySelector('#packageTable tbody').innerHTML = html;
}

function editPackage(id, game_name, icon, package_name, diamonds, price) {
    editingId = id;
    document.getElementById('game_name').value = game_name;
    document.getElementById('icon').value = icon;
    document.getElementById('package_name').value = package_name;
    document.getElementById('diamonds').value = diamonds;
    document.getElementById('price').value = price;
    showTab(2);
}

async function deletePackage(id) {
    if (confirm('Xóa gói này?')) {
        await fetch('admin.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({action:'delete_package', id:id})});
        loadPackages();
    }
}

function resetForm() {
    editingId = null;
    document.getElementById('game_name').value = '';
    document.getElementById('icon').value = '';
    document.getElementById('package_name').value = '';
    document.getElementById('diamonds').value = '';
    document.getElementById('price').value = '';
}

async function loadHistory() {
    const res = await fetch('admin.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({action:'load_history'})});
    const data = await res.json();
    let html = '';
    data.forEach(r => {
        html += `<tr>
            <td>${r.created_at}</td>
            <td>${r.game}</td>
            <td>${r.user_id}</td>
            <td>${r.value}</td>
            <td>${r.provider}</td>
            <td>${r.card_number || '-'}</td>
            <td>${r.serial_number || '-'}</td>
            <td><span class="status" style="background:${r.status==='success'?'#28a745':'#ffc107'};color:white;">${r.status}</span></td>
        </tr>`;
    });
    document.querySelector('#historyTable tbody').innerHTML = html || '<tr><td colspan="8" style="text-align:center;padding:30px;">Chưa có giao dịch</td></tr>';
}

function logout() {
    if (confirm('Đăng xuất?')) window.location.href = 'index.php';
}

// Khởi tạo
window.onload = () => {
    loadPackages();
};
</script>
</body>
</html>