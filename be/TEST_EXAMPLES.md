# Resident Module - Test Examples

Tài liệu này cung cấp các ví dụ curl để test tất cả chức năng của module Resident.

---

## 1. REGISTRATION & LOGIN

### Register New Resident
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "newresident@example.com",
    "ten": "Phạm Văn D",
    "mat_khau": "password123",
    "mat_khau_confirmation": "password123",
    "dien_thoai": "0945678901",
    "vai_tro": "cu_dan"
  }'
```

**Expected Response (201):**
```json
{
    "message": "User registered successfully",
    "user": {
        "id_nguoi_dung": 5,
        "email": "newresident@example.com",
        "ten": "Phạm Văn D",
        "dien_thoai": "0945678901",
        "vai_tro": "cu_dan"
    },
    "token": "random_token_here"
}
```

---

### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "resident1@example.com",
    "mat_khau": "password123"
  }'
```

**Expected Response (200):**
```json
{
    "message": "Login successful",
    "user": {
        "id_nguoi_dung": 1,
        "email": "resident1@example.com",
        "ten": "Nguyễn Văn A",
        "dien_thoai": "0912345678",
        "vai_tro": "cu_dan"
    },
    "token": "token_value"
}
```

**Save token for next requests:**
```bash
TOKEN="token_value"
```

---

## 2. USER PROFILE

### Get Profile
```bash
curl -X GET http://localhost:8000/api/users/1 \
  -H "Authorization: Bearer $TOKEN"
```

**Expected Response (200):**
```json
{
    "id_nguoi_dung": 1,
    "email": "resident1@example.com",
    "ten": "Nguyễn Văn A",
    "dien_thoai": "0912345678",
    "vai_tro": "cu_dan",
    "trang_thai": "active",
    "created_at": "2026-02-02T10:00:00Z",
    "updated_at": "2026-02-02T10:00:00Z"
}
```

---

### Update Profile
```bash
curl -X PUT http://localhost:8000/api/users/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "ten": "Nguyễn Văn A Updated",
    "dien_thoai": "0999888777"
  }'
```

**Expected Response (200):**
```json
{
    "message": "User updated successfully",
    "user": {
        "id_nguoi_dung": 1,
        "ten": "Nguyễn Văn A Updated",
        "dien_thoai": "0999888777"
    }
}
```

---

## 3. MAINTENANCE REQUESTS

### Create Maintenance Request
```bash
curl -X POST http://localhost:8000/api/yeu_cau \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "id_cu_dan": 1,
    "id_can_ho": 1,
    "id_loai_su_co": 2,
    "mo_ta": "Nước rỉ từ trần phòng ngủ, rất cấp tính",
    "thoi_gian_uu_tien": "gan"
  }'
```

**Expected Response (201):**
```json
{
    "message": "Maintenance request created successfully",
    "data": {
        "id_yeu_cau": 10,
        "id_cu_dan": 1,
        "id_can_ho": 1,
        "id_loai_su_co": 2,
        "mo_ta": "Nước rỉ từ trần phòng ngủ, rất cấp tính",
        "thoi_gian_uu_tien": "gan",
        "trang_thai": "moi",
        "created_at": "2026-02-02T15:30:00Z",
        "updated_at": "2026-02-02T15:30:00Z"
    }
}
```

**Save request ID:**
```bash
REQUEST_ID=10
```

---

### Get Request Details
```bash
curl -X GET http://localhost:8000/api/yeu_cau/$REQUEST_ID \
  -H "Authorization: Bearer $TOKEN"
```

**Expected Response (200):**
```json
{
    "id_yeu_cau": 10,
    "id_cu_dan": 1,
    "mo_ta": "Nước rỉ từ trần phòng ngủ",
    "trang_thai": "moi",
    "thoi_gian_uu_tien": "gan",
    "created_at": "2026-02-02T15:30:00Z",
    "cu_dan": {
        "id_nguoi_dung": 1,
        "ten": "Nguyễn Văn A",
        "email": "resident1@example.com"
    },
    "can_ho": {
        "id_can_ho": 1,
        "so_can_ho": "101",
        "tang": 1
    },
    "loai_su_co": {
        "id_loai_su_co": 2,
        "ten_loai": "Nước",
        "muc_uu_tien": 3
    },
    "hinh_anh": [],
    "phan_hoi": null,
    "phan_cong": []
}
```

---

### Get My Requests
```bash
curl -X GET http://localhost:8000/api/users/1/requests \
  -H "Authorization: Bearer $TOKEN"
```

**Expected Response (200):**
```json
[
    {
        "id_yeu_cau": 10,
        "id_cu_dan": 1,
        "mo_ta": "Nước rỉ từ trần phòng ngủ",
        "trang_thai": "moi",
        "thoi_gian_uu_tien": "gan",
        "created_at": "2026-02-02T15:30:00Z"
    },
    {
        "id_yeu_cau": 9,
        "id_cu_dan": 1,
        "mo_ta": "Điều hoà không lạnh",
        "trang_thai": "hoan_thanh",
        "thoi_gian_uu_tien": "kho",
        "created_at": "2026-02-01T10:00:00Z"
    }
]
```

---

### Filter Requests by Status
```bash
curl -X GET "http://localhost:8000/api/yeu_cau?trang_thai=hoan_thanh" \
  -H "Authorization: Bearer $TOKEN"
```

---

### Update Request
```bash
curl -X PUT http://localhost:8000/api/yeu_cau/$REQUEST_ID \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "mo_ta": "Nước rỉ từ trần phòng ngủ, rất cấp tính, cần xử lý ngay",
    "thoi_gian_uu_tien": "gan"
  }'
```

---

### Confirm Request
```bash
curl -X POST http://localhost:8000/api/yeu_cau/$REQUEST_ID/confirm \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{}'
```

**Expected Response (200):**
```json
{
    "message": "Request confirmed successfully",
    "data": {
        "id_yeu_cau": 10,
        "trang_thai": "da_xac_nhan"
    }
}
```

---

### Delete Request
```bash
curl -X DELETE http://localhost:8000/api/yeu_cau/$REQUEST_ID \
  -H "Authorization: Bearer $TOKEN"
```

**Expected Response (200):**
```json
{
    "message": "Request deleted successfully"
}
```

---

## 4. IMAGE UPLOAD

### Upload Image (Single)
```bash
curl -X POST http://localhost:8000/api/yeu_cau/$REQUEST_ID/upload-image \
  -H "Authorization: Bearer $TOKEN" \
  -F "hinh_anh=@/path/to/image.jpg"
```

**Expected Response (201):**
```json
{
    "message": "Image uploaded successfully",
    "data": {
        "id_hinh_anh": 15,
        "id_yeu_cau": 10,
        "duong_dan_file": "yeu_cau_bao_tri/10/1707048600_image.jpg",
        "ten_file": "1707048600_image.jpg",
        "mime_type": "image/jpeg",
        "kich_thuoc": 2048576,
        "created_at": "2026-02-02T16:00:00Z"
    },
    "url": "http://localhost:8000/storage/yeu_cau_bao_tri/10/1707048600_image.jpg"
}
```

**Save image ID:**
```bash
IMAGE_ID=15
```

---

### Upload Multiple Images
```bash
curl -X POST http://localhost:8000/api/yeu_cau/$REQUEST_ID/upload-image \
  -H "Authorization: Bearer $TOKEN" \
  -F "hinh_anh=@/path/to/image1.jpg" \
  -F "hinh_anh=@/path/to/image2.png"
```

---

### Delete Image
```bash
curl -X DELETE http://localhost:8000/api/hinh_anh/$IMAGE_ID \
  -H "Authorization: Bearer $TOKEN"
```

**Expected Response (200):**
```json
{
    "message": "Image deleted successfully"
}
```

---

## 5. FEEDBACK & RATING

### Submit Feedback (After Completion)
```bash
curl -X POST http://localhost:8000/api/phan_hoi \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "id_yeu_cau": 10,
    "id_cu_dan": 1,
    "danh_gia": 5,
    "binh_luan": "Dịch vụ tuyệt vời! Nhân viên chuyên nghiệp, nhanh chóng và thân thiện."
  }'
```

**Expected Response (201):**
```json
{
    "message": "Feedback submitted successfully",
    "data": {
        "id_phan_hoi": 20,
        "id_yeu_cau": 10,
        "id_cu_dan": 1,
        "danh_gia": 5,
        "binh_luan": "Dịch vụ tuyệt vời! Nhân viên chuyên nghiệp, nhanh chóng và thân thiện.",
        "created_at": "2026-02-02T17:00:00Z"
    }
}
```

---

### Get All Feedback
```bash
curl -X GET http://localhost:8000/api/phan_hoi \
  -H "Authorization: Bearer $TOKEN"
```

---

### Filter Feedback
```bash
curl -X GET "http://localhost:8000/api/phan_hoi?id_cu_dan=1&danh_gia=5" \
  -H "Authorization: Bearer $TOKEN"
```

---

### Update Feedback
```bash
curl -X PUT http://localhost:8000/api/phan_hoi/20 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "danh_gia": 4,
    "binh_luan": "Tốt lắm nhưng nhân viên có thể nhanh hơn"
  }'
```

---

### Get Average Rating for Issue Type
```bash
curl -X GET "http://localhost:8000/api/phan_hoi/rating/average?id_loai_su_co=2" \
  -H "Authorization: Bearer $TOKEN"
```

**Expected Response (200):**
```json
{
    "loai_su_co_id": 2,
    "average_rating": 4.5,
    "total_reviews": 12
}
```

---

### Get Resident's Average Rating
```bash
curl -X GET http://localhost:8000/api/resident/1/rating \
  -H "Authorization: Bearer $TOKEN"
```

**Expected Response (200):**
```json
{
    "resident_id": 1,
    "average_rating": 4.8,
    "total_reviews": 5
}
```

---

### Delete Feedback
```bash
curl -X DELETE http://localhost:8000/api/phan_hoi/20 \
  -H "Authorization: Bearer $TOKEN"
```

---

## 6. NOTIFICATIONS

### Get Notifications
```bash
curl -X GET http://localhost:8000/api/users/1/notifications \
  -H "Authorization: Bearer $TOKEN"
```

**Expected Response (200):**
```json
[
    {
        "id_yeu_cau": 1,
        "id_cu_dan": 1,
        "mo_ta": "Nước rỉ từ trần phòng ngủ",
        "trang_thai": "hoan_thanh",
        "phan_cong": [
            {
                "id_phan_cong": 8,
                "id_nhan_vien": 4,
                "trang_thai": "hoan_thanh",
                "ngay_ket_thuc": "2026-02-02T14:30:00Z"
            }
        ]
    }
]
```

---

## 7. ERROR CASES

### Missing Required Field
```bash
curl -X POST http://localhost:8000/api/yeu_cau \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "id_cu_dan": 1,
    "id_can_ho": 1
  }'
```

**Response (422):**
```json
{
    "errors": {
        "id_loai_su_co": ["Maintenance type is required"],
        "mo_ta": ["Description is required"]
    }
}
```

---

### Invalid Email
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "invalid-email",
    "ten": "Test User",
    "mat_khau": "password123",
    "mat_khau_confirmation": "password123"
  }'
```

**Response (422):**
```json
{
    "errors": {
        "email": ["Email must be a valid email address"]
    }
}
```

---

### Invalid Rating (Not 1-5)
```bash
curl -X POST http://localhost:8000/api/phan_hoi \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "id_yeu_cau": 10,
    "id_cu_dan": 1,
    "danh_gia": 10,
    "binh_luan": "Invalid rating"
  }'
```

**Response (422):**
```json
{
    "errors": {
        "danh_gia": ["Rating must not exceed 5"]
    }
}
```

---

### Request Not Found
```bash
curl -X GET http://localhost:8000/api/yeu_cau/999 \
  -H "Authorization: Bearer $TOKEN"
```

**Response (404):**
```json
{
    "error": "Request not found"
}
```

---

### Unauthorized (No Token)
```bash
curl -X GET http://localhost:8000/api/yeu_cau/10
```

**Response (401):**
```json
{
    "error": "Unauthorized"
}
```

---

## Complete Workflow Example

### Step 1: Register
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"email": "test@example.com", "ten": "Test User", "mat_khau": "pass123", "mat_khau_confirmation": "pass123"}'
# Save token
```

### Step 2: Create Request
```bash
curl -X POST http://localhost:8000/api/yeu_cau \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"id_cu_dan": 1, "id_can_ho": 1, "id_loai_su_co": 2, "mo_ta": "Water leak", "thoi_gian_uu_tien": "gan"}'
# Save request ID
```

### Step 3: Upload Image
```bash
curl -X POST http://localhost:8000/api/yeu_cau/10/upload-image \
  -H "Authorization: Bearer $TOKEN" \
  -F "hinh_anh=@image.jpg"
```

### Step 4: Confirm Request
```bash
curl -X POST http://localhost:8000/api/yeu_cau/10/confirm \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{}'
```

### Step 5: Track Status
```bash
curl -X GET http://localhost:8000/api/users/1/requests \
  -H "Authorization: Bearer $TOKEN"
```

### Step 6: (After completion) Rate
```bash
curl -X POST http://localhost:8000/api/phan_hoi \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"id_yeu_cau": 10, "id_cu_dan": 1, "danh_gia": 5, "binh_luan": "Great!"}'
```

---

## Tips for Testing

1. **Use Postman** - Import the API collection
2. **Set environment variables** in Postman for TOKEN, REQUEST_ID, etc.
3. **Test error cases** - Try with missing/invalid data
4. **Check response codes** - 200, 201, 400, 404, 422, etc.
5. **Verify database** - Check if data is persisted correctly
6. **Test file uploads** - Try different image formats and sizes
7. **Test role-based access** - Use different user roles (resident, staff, admin)
