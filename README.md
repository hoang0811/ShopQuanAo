# Shop bán quần áo

hop bán quần áo là một website thương mại điện tử đơn giản, nơi người dùng có thể duyệt, tìm kiếm và mua quần áo. Dự án được phát triển bằng Laravel.

---

## Mục tiêu và tính năng chính

### Mục tiêu
- Xây dựng một hệ thống thương mại điện tử cơ bản.
- Hỗ trợ người dùng tìm kiếm và đặt hàng online.

### Tính năng chính
- **Người dùng**:
  - Đăng ký, đăng nhập và quản lý tài khoản.
  - Thêm sản phẩm vào giỏ hàng và yêu thích.
  - Đặt hàng và thanh toán.
- **Quản trị viên**:
  - Quản lý thương hiệu.
  - Quản lý danh mục.
  - Quản lý sản phẩm.
  - Quản lí đơn hàng.
---
## Demo

Link demo: [ShopQuầnÁo](https://your-demo-link.com)

### Hình ảnh minh họa
![Trang chủ (1)](https://drive.google.com/file/d/1-WVQmCWV_rtMddYktOkHjFEYftCrjFYN/view?usp=drive_link)
![Sản Phẩm](https://drive.google.com/file/d/1r0h9ywOEfqf8DRZmOA67wufuaPgPChbs/view?usp=drive_link)


---

## Liên hệ
- Tác giả: [Hoang0811](https://github.com/hoang0811)
- Email: hoangthanh081102@gmail.com
## Hướng dẫn cài đặt

### Yêu cầu hệ thống
- PHP >= 8.1
- Composer
- Node.js và npm
- MySQL

### Các bước cài đặt
1. Clone repository:
   ```bash
   git clone https://github.com/hoang0811/ShopQuanAo.git
   cd ShopQuanAo
   ```

2. Cài đặt các thư viện PHP:
   ```bash
   composer install
   ```

3. Cài đặt các package JavaScript:
   ```bash
   npm install
   ```

4. Cấu hình môi trường:
   - Tạo file `.env`:
     ```bash
     cp .env.example .env
     ```
   - Cập nhật thông tin database trong file `.env`.

5. Chạy migration:
   ```bash
   php artisan migrate
   ```

6. Chạy server:
   ```bash
   php artisan serve
   npm run dev
   ```

7. Truy cập website tại [http://localhost:8000](http://localhost:8000).

---

