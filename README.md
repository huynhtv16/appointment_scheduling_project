
# HỆ THỐNG QUẢN LÝ LỊCH KHÁM BỆNH


Hệ thống quản lý lịch khám cho bệnh viện/phòng khám gồm backend (API Laravel) và frontend (Nuxt 3). File này là hướng dẫn nhanh để cài đặt và khởi chạy.

---

**Kiến trúc & thư mục**
- Backend: Laravel REST API — thư mục `be/`
- Frontend: Nuxt 3 (Vue 3) — thư mục `fe/`

**Yêu cầu tối thiểu**
- PHP >= 8.1, Composer
- MySQL / MariaDB
- Node.js >= 18, npm
- Git

**Hướng dẫn nhanh (Setup từ đầu)**

1) Clone repository

```bash
git clone <repo_url>
cd appointment_scheduling_project
```

2) Backend (Laravel)

```bash
cd be
composer install
copy .env.example .env    # Windows PowerShell: Copy-Item .env.example .env
# chỉnh file be/.env: DB_* và APP_URL
php artisan key:generate
php artisan migrate
php artisan serve --host=127.0.0.1 --port=8000
```

3) Frontend (Nuxt 3)

```bash
cd fe
npm install
# tạo fe/.env: NUXT_PUBLIC_API_BASE_URL=http://127.0.0.1:8000/api
npm run dev
```

**Truy cập**
- Frontend: http://localhost:3000
- Backend (API): http://127.0.0.1:8000/api
-
**Các chức năng chính**
- Quản lý người dùng: đăng nhập/đăng ký, phân quyền cơ bản (vai trò `admin`, `staff`, ...).
- Quản lý bệnh nhân: tạo, chỉnh sửa, xóa và tìm kiếm hồ sơ bệnh nhân.
- Quản lý lịch hẹn: đặt lịch, xác nhận/hủy, theo dõi trạng thái lịch khám.
- Kết quả khám bệnh: lưu trữ và truy xuất `ExaminationResult` gắn với bệnh nhân và lịch hẹn.
- API REST cho frontend: endpoint cho bệnh nhân, lịch hẹn, kết quả khám và người dùng.
- Phân quyền vai trò và kiểm soát truy cập cơ bản.
- Seeder & dữ liệu demo để khởi tạo môi trường thử nghiệm.
- Kiểm thử cơ bản: cấu hình `phpunit` và thư mục `tests/` để chạy unit/feature tests.
- Frontend Nuxt 3: giao diện quản lý và gọi các API từ backend.

**Liên hệ & hỗ trợ**
- Huynh Tran
- Gmail: huynhtv.vn@gmail.com

---

Phiên bản này dành cho khách hàng: chỉ gồm nội dung cần thiết để triển khai và chạy ứng dụng. Muốn tôi tạo các file cấu hình mẫu (ví dụ `.env.example` cho backend/frontend) không?


