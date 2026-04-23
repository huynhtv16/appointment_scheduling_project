
# HỆ THỐNG QUẢN LÝ LỊCH KHÁM BỆNH

Tài liệu này hướng dẫn nhanh cách cài đặt và chạy hệ thống quản lý lịch khám, bao gồm backend (Laravel API) và frontend (Nuxt 3).

**Tổng quan**
- Backend: Laravel REST API (thư mục `be/`)
- Frontend: Nuxt 3 (thư mục `fe/`)

**Yêu cầu tối thiểu**
- PHP >= 8.1, Composer
- MySQL / MariaDB
- Node.js >= 18, npm hoặc pnpm
- Git

**Cài đặt nhanh (từ đầu)**

1) Clone repository

```bash
git clone <repo_url>
cd appointment_scheduling_project
```

2) Backend (Laravel)

```bash
cd be
composer install
```

- Tạo file môi trường và chỉnh cấu hình DB / URL:

```powershell
Copy-Item .env.example .env    # Windows PowerShell
# hoặc trên macOS/Linux: cp .env.example .env
# Mở be/.env và cập nhật DB_*, APP_URL (ví dụ APP_URL=http://127.0.0.1:8000)
```

- Khởi tạo ứng dụng và chạy migration + seeder (nếu cần):

```bash
php artisan key:generate
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8000
```

3) Frontend (Nuxt 3)

```bash
cd ../fe
npm install
# Tạo fe/.env hoặc .env.local với:
# NUXT_PUBLIC_API_BASE_URL=http://127.0.0.1:8000/api
npm run dev
```

**Truy cập**
- Frontend: http://localhost:3000
- Backend (API): http://127.0.0.1:8000/api

**Chức năng chính**
- Quản lý người dùng: đăng ký/đăng nhập, phân quyền (admin, staff,...)
- Quản lý bệnh nhân: tạo, chỉnh sửa, xóa, tìm kiếm hồ sơ
- Quản lý lịch hẹn: đặt lịch, xác nhận, hủy, theo dõi trạng thái
- Lưu trữ kết quả khám: `ExaminationResult` liên kết với bệnh nhân và lịch hẹn
- API REST cho frontend: endpoints cho patients, appointments, results, users

**Kiểm thử & dữ liệu mẫu**
- Dùng seeder để tạo dữ liệu demo: chạy `php artisan migrate --seed`.
- Tests: chạy PHPUnit trong thư mục `be/` nếu đã cấu hình: `vendor/bin/phpunit`.

**Lưu ý khi triển khai**
- Kiểm tra kỹ biến môi trường trong `be/.env` và `fe/.env` trước khi deploy.
- Cấu hình HTTPS và CORS khi đưa lên production.

**Liên hệ**
- Huynh Tran — huynhtv.vn@gmail.com

---


