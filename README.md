# ShopQuầnÁo

ShopQuầnÁo là một website thương mại điện tử đơn giản, nơi người dùng có thể duyệt, tìm kiếm và mua quần áo. Dự án được phát triển bằng Laravel và Vue.js.

---

## Mục tiêu và tính năng chính

### Mục tiêu
- Xây dựng một hệ thống thương mại điện tử cơ bản.
- Hỗ trợ người dùng tìm kiếm và đặt hàng online.

### Tính năng chính
- **Người dùng**:
  - Đăng ký, đăng nhập và quản lý tài khoản.
  - Thêm sản phẩm vào giỏ hàng và yêu thích.
  - Tính phí vận chuyển thông qua GHN API.
  - Thanh toán qua MoMo và VNPay.
- **Quản trị viên**:
  - Quản lý sách, đơn hàng, báo cáo.
  - Thêm/sửa/xóa danh mục, tác giả, nhà xuất bản.

---

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

## Demo

Link demo: [ShopQuầnÁo](https://your-demo-link.com)

### Hình ảnh minh họa
![Trang chủ](https://via.placeholder.com/800x400.png?text=Home+Page)
![Giỏ hàng](https://via.placeholder.com/800x400.png?text=Cart+Page)

---

## Công nghệ sử dụng
- **Frontend**: Vue.js, TailwindCSS
- **Backend**: Laravel
- **Cơ sở dữ liệu**: MySQL
- **Thanh toán**: VNPay, MoMo
- **API vận chuyển**: GHN API

---

## Liên hệ
- Tác giả: [Hoang0811](https://github.com/hoang0811)
- Email: your-email@example.com
