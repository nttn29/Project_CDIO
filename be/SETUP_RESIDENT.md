# Setup Hướng dẫn - Module Resident (Cư dân)

## Tổng quan chức năng

Module Resident cung cấp đầy đủ các chức năng cho cư dân:

✅ **Đăng ký/Đăng nhập** - Authentication  
✅ **Tạo yêu cầu bảo trì** - Water/Electricity/Elevator/AC/Internet  
✅ **Upload ảnh** - Tùy chọn, hỗ trợ JPEG/PNG/GIF  
✅ **Theo dõi trạng thái** - Track request status and schedule  
✅ **Đánh giá** - Rating after completion (1-5 stars)

---

## Cấu trúc Files

```
be/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── NguoiDungController.php         ✅ Register/Login/Profile
│   │   │   ├── YeuCauBaoTriController.php      ✅ Request Management + Upload
│   │   │   └── PhanHoiController.php           ✅ Feedback/Rating
│   │   ├── Middleware/
│   │   │   ├── CheckResidentRole.php           ✅ Role Authorization
│   │   │   └── CheckStaffRole.php              ✅ Staff Authorization
│   │   └── Requests/
│   │       ├── RegisterRequest.php             ✅ Register Validation
│   │       ├── StoreYeuCauBaoTriRequest.php    ✅ Request Validation
│   │       └── StorePhanHoiRequest.php         ✅ Feedback Validation
│   └── Models/
│       ├── NguoiDung.php                       ✅ Resident Model
│       ├── YeuCauBaoTri.php                    ✅ Request Model
│       ├── HinhAnhYeuCau.php                   ✅ Image Model
│       ├── PhanHoi.php                         ✅ Feedback Model
│       ├── CanHo.php                           ✅ Apartment Model
│       ├── LoaiSuCo.php                        ✅ Issue Type Model
│       ├── ToaNha.php                          ✅ Building Model
│       ├── PhanCong.php                        ✅ Assignment Model
│       └── NhatKyCongViec.php                  ✅ Work Log Model
├── database/
│   ├── migrations/
│   │   └── 2026_02_02_000010_create_hinh_anh_yeu_cau_table.php  ✅ Image Table
│   └── seeders/
├── config/
│   └── filesystems.php                         ✅ Storage Configuration
├── routes/
│   └── web.php                                 ✅ API Routes
├── RESIDENT_API.md                             ✅ API Documentation
└── SETUP.md (this file)
```

---

## Installation & Setup

### 1. Database Migration

Run migrations to create tables:

```bash
cd be
php artisan migrate
```

This creates:
- `nguoi_dung` - Residents/Staff
- `yeu_cau_bao_tri` - Maintenance requests
- `hinh_anh_yeu_cau` - Request images
- `phan_hoi` - Feedback/ratings
- `can_ho` - Apartments
- `loai_su_co` - Issue types
- `toa_nha` - Buildings
- `phan_cong` - Assignments
- `nhat_ky_cong_viec` - Work logs

### 2. Create Storage Link

Make uploaded files publicly accessible:

```bash
php artisan storage:link
```

This creates a symlink from `public/storage` to `storage/app/public`.

### 3. Seed Initial Data (Optional)

Create sample residents and issue types:

```bash
php artisan db:seed
```

---

## API Endpoints Overview

### Authentication
```
POST   /api/register              - Register new resident
POST   /api/login                 - Login
```

### User Profile
```
GET    /api/users/{id}            - Get profile
PUT    /api/users/{id}            - Update profile
```

### Maintenance Requests
```
POST   /api/yeu_cau               - Create request
GET    /api/yeu_cau/{id}          - Get request details
GET    /api/yeu_cau               - Get all requests (staff)
PUT    /api/yeu_cau/{id}          - Update request
DELETE /api/yeu_cau/{id}          - Delete request
POST   /api/yeu_cau/{id}/confirm  - Confirm request
POST   /api/yeu_cau/{id}/status   - Change status (staff)
```

### Images
```
POST   /api/yeu_cau/{id}/upload-image  - Upload image
DELETE /api/hinh_anh/{id}               - Delete image
```

### Feedback/Rating
```
POST   /api/phan_hoi                    - Submit feedback
GET    /api/phan_hoi                    - Get all feedback
GET    /api/phan_hoi/{id}               - Get specific feedback
PUT    /api/phan_hoi/{id}               - Update feedback
DELETE /api/phan_hoi/{id}               - Delete feedback
GET    /api/phan_hoi/rating/average     - Get average rating
GET    /api/resident/{id}/rating        - Get resident rating
```

### Notifications & Tracking
```
GET    /api/users/{id}/requests         - Get my requests
GET    /api/users/{id}/notifications    - Get notifications
```

---

## Request Status Flow

```
moi (New)
    ↓
da_xac_nhan (Confirmed)
    ↓
dang_xu_ly (In Progress)
    ↓
hoan_thanh (Completed) → [Can Rate]
    ↓
[Optional] huy (Cancelled)
```

---

## Priority Levels

- **gan** (Urgent) - Should be handled ASAP
- **binh_thuong** (Normal) - Regular priority (default)
- **kho** (Low) - Can wait

---

## Image Upload Configuration

- **Location:** `storage/app/public/yeu_cau_bao_tri/{request_id}/`
- **Max size:** 2MB
- **Formats:** JPEG, PNG, JPG, GIF
- **Access URL:** `http://localhost:8000/storage/yeu_cau_bao_tri/{request_id}/{filename}`

### Environment Setup

Ensure `.env` has:
```env
FILESYSTEM_DISK=public
APP_URL=http://localhost:8000
```

---

## Role-Based Access

### Resident (cu_dan)
- Register/Login
- Create/Update/Delete own requests
- Upload images
- View own requests
- Submit ratings
- Update profile

### Staff (nhan_vien)
- View all requests
- Update request status
- Create assignments
- Log work activities

### Manager (quan_ly)
- All staff permissions
- Create/Manage staff
- Analytics

### Admin (admin)
- All permissions
- User management
- System configuration

---

## Testing with Postman

### 1. Register
```
Method: POST
URL: http://localhost:8000/api/register
Body (JSON):
{
    "email": "resident@example.com",
    "ten": "Nguyễn Văn A",
    "mat_khau": "password123",
    "mat_khau_confirmation": "password123",
    "dien_thoai": "0912345678"
}
```

### 2. Login
```
Method: POST
URL: http://localhost:8000/api/login
Body (JSON):
{
    "email": "resident@example.com",
    "mat_khau": "password123"
}
```

Response includes token - save for next requests.

### 3. Create Request
```
Method: POST
URL: http://localhost:8000/api/yeu_cau
Headers:
  Authorization: Bearer {token_from_login}
Body (JSON):
{
    "id_cu_dan": 1,
    "id_can_ho": 5,
    "id_loai_su_co": 2,
    "mo_ta": "Water is leaking from ceiling",
    "thoi_gian_uu_tien": "gan"
}
```

### 4. Upload Image
```
Method: POST
URL: http://localhost:8000/api/yeu_cau/1/upload-image
Headers:
  Authorization: Bearer {token}
Body (form-data):
  hinh_anh: [select image file]
```

### 5. Rate Request
```
Method: POST
URL: http://localhost:8000/api/phan_hoi
Headers:
  Authorization: Bearer {token}
Body (JSON):
{
    "id_yeu_cau": 1,
    "id_cu_dan": 1,
    "danh_gia": 5,
    "binh_luan": "Great service!"
}
```

---

## Validation Rules

### Register Request
- **email** - Required, unique, valid email format
- **ten** - Required, max 255 characters
- **mat_khau** - Required, min 6 characters, must be confirmed
- **dien_thoai** - Optional, max 20 characters
- **vai_tro** - Optional, must be: cu_dan, nhan_vien, quan_ly, admin

### Maintenance Request
- **id_cu_dan** - Required, must exist in nguoi_dung
- **id_can_ho** - Required, must exist in can_ho
- **id_loai_su_co** - Required, must exist in loai_su_co
- **mo_ta** - Required, max 1000 characters
- **thoi_gian_uu_tien** - Optional, must be: gan, binh_thuong, kho

### Feedback Request
- **id_yeu_cau** - Required, must exist
- **id_cu_dan** - Required, must exist
- **danh_gia** - Required, integer 1-5
- **binh_luan** - Optional, max 1000 characters

### Image Upload
- **hinh_anh** - Required, must be image
- **Format** - JPEG, PNG, JPG, GIF
- **Size** - Max 2MB

---

## Error Handling

All endpoints return standard HTTP status codes:

- **200** - Success
- **201** - Created
- **204** - No Content
- **400** - Bad Request
- **401** - Unauthorized
- **403** - Forbidden
- **404** - Not Found
- **409** - Conflict
- **422** - Validation Error
- **500** - Server Error

Example error response:
```json
{
    "error": "Resource not found"
}
```

Validation errors:
```json
{
    "errors": {
        "email": ["Email already registered"],
        "mat_khau": ["Password must be at least 6 characters"]
    }
}
```

---

## Performance Considerations

1. **Eager Loading** - Controllers use `.with()` to prevent N+1 queries
2. **Pagination** - Can be added for large result sets
3. **Caching** - Implement for frequently accessed data
4. **Image Optimization** - Consider image compression for uploads

---

## Security Features

✅ Password hashing (Laravel's Hash)  
✅ Input validation  
✅ Role-based access control  
✅ CSRF protection (if using web forms)  
✅ File upload validation  
✅ Relationship validation (foreign keys)

---

## Future Enhancements

- [ ] JWT authentication instead of tokens
- [ ] Email notifications
- [ ] SMS notifications
- [ ] Real-time updates (WebSockets)
- [ ] Image compression on upload
- [ ] Pagination for endpoints
- [ ] Advanced filtering/search
- [ ] Export reports (PDF/Excel)
- [ ] Mobile app API optimization
- [ ] Rate limiting

---

## Troubleshooting

### Storage link not working
```bash
php artisan storage:link --force
```

### Images not uploading
- Check `storage/app/public` permissions
- Ensure `filesystems.php` has public disk configured
- Verify file size limit in `.env`

### Migration errors
```bash
php artisan migrate:reset
php artisan migrate
```

### Clear cache if needed
```bash
php artisan cache:clear
php artisan route:cache
php artisan config:cache
```

---

## Next Steps

1. ✅ **Resident Module** - Complete
2. 📋 **Staff Module** - To implement (PhanCongController, NhatKyCongViecController)
3. 🏢 **Admin Module** - To implement (Management endpoints)
4. 🔐 **Authentication** - Consider upgrading to JWT/Sanctum
5. 📱 **Frontend** - Vue 3 integration

---

## Contact & Support

For questions or issues, refer to:
- [Full API Documentation](./RESIDENT_API.md)
- [Laravel Documentation](https://laravel.com/docs)
- [Project Repository](https://github.com/your-repo)
