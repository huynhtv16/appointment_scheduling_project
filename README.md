# HỆ THỐNG QUẢN LÝ LỊCH KHÁM BỆNH

Phiên bản rút gọn: hướng dẫn cài đặt và mô tả nhanh cho project

Mục tiêu
--
Hệ thống hỗ trợ bệnh nhân, bác sĩ và nhân viên y tế trong việc đặt lịch, quản lý hồ sơ bệnh án và theo dõi hoạt động khám chữa bệnh.

Kiến trúc
--
- Backend: Laravel (RESTful API) — thư mục [BackEnd](BackEnd/)
- Frontend: Nuxt 3 (Vue 3) — thư mục [FrontEnd](FrontEnd/)

Công nghệ chính
--
- PHP >= 8.1, Laravel
- MySQL
- Laravel Sanctum (authentication)
- Node.js >= 18, Nuxt 3, Vue 3
- Axios, TailwindCSS

Yêu cầu hệ thống
--
- PHP >= 8.1
- Composer
- Node.js >= 18
- MySQL
- Git

Hướng dẫn cài đặt nhanh
--
1. Clone repository

   git clone <repo_url>
   cd appointment_scheduling_project

2. Backend (Laravel)

   cd BackEnd
   composer install
   cp .env.example .env
   chỉnh sửa `.env` để cấu hình DB (ví dụ `DB_DATABASE=appointment_db`)
   php artisan key:generate
   php artisan migrate
   php artisan serve

   Backend mặc định chạy tại http://127.0.0.1:8000

3. Frontend (Nuxt 3)

   cd ../FrontEnd
   npm install
   npm run dev

   Frontend mặc định chạy tại http://localhost:3000

Tính năng chính
--
- F01 – Quản lý tài khoản: đăng ký, đăng nhập, đăng xuất, cập nhật thông tin
- F02 – Quản lý lịch hẹn: bệnh nhân đặt lịch, nhân viên xác nhận/hủy, bác sĩ cập nhật trạng thái
- F03 – Hồ sơ bệnh án: bác sĩ ghi chẩn đoán, đơn thuốc; bệnh nhân xem lịch sử khám
- F04 – Dịch vụ y tế: CRUD dịch vụ, tìm kiếm
- F05 – Thông báo: gửi email xác nhận, quản lý mẫu
- F06 – Thống kê: báo cáo theo ngày/tháng, theo bác sĩ, export Excel/PDF
- F07 – Quản lý người dùng: phân quyền, khóa/mở tài khoản

Cấu trúc dự án (tóm tắt)
--
- BackEnd/: Laravel API, migrations, models, controllers, repositories
- FrontEnd/: Nuxt 3 app, components, pages, composables

Tài liệu liên quan
--
- Xem hướng dẫn chi tiết Backend: [BackEnd/README.md](BackEnd/README.md)
- Xem hướng dẫn chi tiết Frontend: [FrontEnd/README.md](FrontEnd/README.md)

Góp ý và phát triển
--
- Muốn thêm badges, hướng dẫn seed dữ liệu, hoặc ví dụ .env mẫu nào khác không?
- Nếu muốn, tôi có thể tạo file `.env.example` ở root hoặc hướng dẫn tạo dữ liệu mẫu.

Giấy phép
--
Tùy chọn — thêm thông tin license nếu cần.

Liên hệ
--
Thêm hướng dẫn liên hệ hoặc thông tin tác giả nếu cần.
