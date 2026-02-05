# API Documentation - Resident Module (Cư dân)

## Overview
This document describes the API endpoints for the Resident (Cư dân) module, including registration, maintenance request management, image uploads, and feedback/ratings.

## Base URL
```
http://localhost:8000/api
```

## Authentication
Most endpoints require authentication. Use the token returned from login/register in the `Authorization` header:
```
Authorization: Bearer {token}
```

---

## 1. Authentication Endpoints

### 1.1 Register New Resident
**POST** `/register`

Create a new resident account.

**Request Body:**
```json
{
    "email": "resident@example.com",
    "ten": "Nguyễn Văn A",
    "mat_khau": "password123",
    "mat_khau_confirmation": "password123",
    "dien_thoai": "0912345678",
    "vai_tro": "cu_dan"
}
```

**Response (201):**
```json
{
    "message": "User registered successfully",
    "user": {
        "id_nguoi_dung": 1,
        "email": "resident@example.com",
        "ten": "Nguyễn Văn A",
        "dien_thoai": "0912345678",
        "vai_tro": "cu_dan"
    },
    "token": "token_value_here"
}
```

**Error Response (422):**
```json
{
    "errors": {
        "email": ["Email already registered"],
        "mat_khau": ["Password must be at least 6 characters"]
    }
}
```

---

### 1.2 Login
**POST** `/login`

Authenticate resident and get access token.

**Request Body:**
```json
{
    "email": "resident@example.com",
    "mat_khau": "password123"
}
```

**Response (200):**
```json
{
    "message": "Login successful",
    "user": {
        "id_nguoi_dung": 1,
        "email": "resident@example.com",
        "ten": "Nguyễn Văn A",
        "dien_thoai": "0912345678",
        "vai_tro": "cu_dan"
    },
    "token": "token_value_here"
}
```

**Error Response (401):**
```json
{
    "error": "Invalid email or password"
}
```

---

## 2. User Profile Endpoints

### 2.1 Get User Profile
**GET** `/users/{id}`

Get resident profile information.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "id_nguoi_dung": 1,
    "email": "resident@example.com",
    "ten": "Nguyễn Văn A",
    "mat_khau": "hashed_password",
    "dien_thoai": "0912345678",
    "vai_tro": "cu_dan",
    "trang_thai": "active",
    "created_at": "2026-02-02T10:00:00Z",
    "updated_at": "2026-02-02T10:00:00Z"
}
```

---

### 2.2 Update User Profile
**PUT** `/users/{id}`

Update resident profile information.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "ten": "Nguyễn Văn B",
    "dien_thoai": "0987654321",
    "mat_khau": "newpassword123",
    "mat_khau_confirmation": "newpassword123"
}
```

**Response (200):**
```json
{
    "message": "User updated successfully",
    "user": {
        "id_nguoi_dung": 1,
        "email": "resident@example.com",
        "ten": "Nguyễn Văn B",
        "dien_thoai": "0987654321",
        "vai_tro": "cu_dan",
        "trang_thai": "active"
    }
}
```

---

## 3. Maintenance Request Endpoints

### 3.1 Create Maintenance Request
**POST** `/yeu_cau`

Create a new maintenance request for water/electricity/elevator/air conditioning/internet, etc.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "id_cu_dan": 1,
    "id_can_ho": 5,
    "id_loai_su_co": 2,
    "mo_ta": "Water is leaking from the ceiling in the bedroom",
    "thoi_gian_uu_tien": "gan"
}
```

**Priority Levels (thoi_gian_uu_tien):**
- `gan` - Urgent/High priority
- `binh_thuong` - Normal (default)
- `kho` - Low priority

**Response (201):**
```json
{
    "message": "Maintenance request created successfully",
    "data": {
        "id_yeu_cau": 10,
        "id_cu_dan": 1,
        "id_can_ho": 5,
        "id_loai_su_co": 2,
        "mo_ta": "Water is leaking from the ceiling in the bedroom",
        "thoi_gian_uu_tien": "gan",
        "trang_thai": "moi",
        "created_at": "2026-02-02T10:15:00Z",
        "updated_at": "2026-02-02T10:15:00Z"
    }
}
```

---

### 3.2 Get Maintenance Request Details
**GET** `/yeu_cau/{id}`

Get detailed information about a specific maintenance request including images, assignments, and feedback.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "id_yeu_cau": 10,
    "id_cu_dan": 1,
    "id_can_ho": 5,
    "id_loai_su_co": 2,
    "mo_ta": "Water is leaking from the ceiling in the bedroom",
    "thoi_gian_uu_tien": "gan",
    "trang_thai": "hoan_thanh",
    "created_at": "2026-02-02T10:15:00Z",
    "updated_at": "2026-02-02T14:30:00Z",
    "cu_dan": {
        "id_nguoi_dung": 1,
        "ten": "Nguyễn Văn A",
        "email": "resident@example.com"
    },
    "can_ho": {
        "id_can_ho": 5,
        "so_can_ho": "201",
        "tang": 2
    },
    "loai_su_co": {
        "id_loai_su_co": 2,
        "ten_loai": "Nước",
        "muc_uu_tien": 3
    },
    "hinh_anh": [
        {
            "id_hinh_anh": 15,
            "duong_dan_file": "yeu_cau_bao_tri/10/image1.jpg",
            "ten_file": "image1.jpg",
            "mime_type": "image/jpeg",
            "kich_thuoc": 2048576,
            "created_at": "2026-02-02T10:20:00Z"
        }
    ],
    "phan_hoi": {
        "id_phan_hoi": 5,
        "danh_gia": 5,
        "binh_luan": "Great service! Quickly fixed the issue.",
        "created_at": "2026-02-02T15:00:00Z"
    },
    "phan_cong": [
        {
            "id_phan_cong": 8,
            "id_nhan_vien": 10,
            "ngay_bat_dau": "2026-02-02T10:30:00Z",
            "ngay_ket_thuc": "2026-02-02T14:30:00Z",
            "trang_thai": "hoan_thanh"
        }
    ]
}
```

---

### 3.3 Track Maintenance Request Status
**GET** `/users/{id}/requests`

Get all maintenance requests from a specific resident with their current status.

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `trang_thai` (optional) - Filter by status: moi, da_xac_nhan, dang_xu_ly, hoan_thanh, huy

**Response (200):**
```json
[
    {
        "id_yeu_cau": 10,
        "id_cu_dan": 1,
        "mo_ta": "Water is leaking from the ceiling in the bedroom",
        "trang_thai": "hoan_thanh",
        "thoi_gian_uu_tien": "gan",
        "created_at": "2026-02-02T10:15:00Z",
        "updated_at": "2026-02-02T14:30:00Z",
        "loai_su_co": {
            "id_loai_su_co": 2,
            "ten_loai": "Nước"
        },
        "phan_hoi": {
            "danh_gia": 5,
            "binh_luan": "Great service!"
        }
    },
    {
        "id_yeu_cau": 9,
        "id_cu_dan": 1,
        "mo_ta": "Air conditioner not working properly",
        "trang_thai": "dang_xu_ly",
        "thoi_gian_uu_tien": "binh_thuong",
        "created_at": "2026-02-01T15:20:00Z",
        "updated_at": "2026-02-02T08:00:00Z"
    }
]
```

**Status Meanings:**
- `moi` - New/Created
- `da_xac_nhan` - Confirmed
- `dang_xu_ly` - In Progress
- `hoan_thanh` - Completed
- `huy` - Cancelled

---

### 3.4 Update Maintenance Request
**PUT** `/yeu_cau/{id}`

Update maintenance request details (before it's confirmed).

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "mo_ta": "Water is leaking from the ceiling in the master bedroom",
    "thoi_gian_uu_tien": "gan"
}
```

**Response (200):**
```json
{
    "message": "Request updated successfully",
    "data": {
        "id_yeu_cau": 10,
        "id_cu_dan": 1,
        "mo_ta": "Water is leaking from the ceiling in the master bedroom",
        "thoi_gian_uu_tien": "gan",
        "trang_thai": "moi",
        "updated_at": "2026-02-02T10:30:00Z"
    }
}
```

---

### 3.5 Confirm Maintenance Request
**POST** `/yeu_cau/{id}/confirm`

Confirm that the maintenance request is valid and ready for processing.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{}
```

**Response (200):**
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

### 3.6 Delete Maintenance Request
**DELETE** `/yeu_cau/{id}`

Delete a maintenance request (usually only new requests can be deleted).

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "message": "Request deleted successfully"
}
```

---

## 4. Image Upload Endpoints

### 4.1 Upload Image for Maintenance Request
**POST** `/yeu_cau/{id}/upload-image`

Upload image(s) related to the maintenance issue for documentation.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body:**
```
Form-data:
- hinh_anh: [binary image file]
```

**Supported formats:** JPEG, PNG, JPG, GIF
**Max file size:** 2MB

**Response (201):**
```json
{
    "message": "Image uploaded successfully",
    "data": {
        "id_hinh_anh": 15,
        "id_yeu_cau": 10,
        "duong_dan_file": "yeu_cau_bao_tri/10/1707044400_water_leak.jpg",
        "ten_file": "1707044400_water_leak.jpg",
        "mime_type": "image/jpeg",
        "kich_thuoc": 2048576,
        "created_at": "2026-02-02T10:20:00Z"
    },
    "url": "http://localhost:8000/storage/yeu_cau_bao_tri/10/1707044400_water_leak.jpg"
}
```

---

### 4.2 Delete Image
**DELETE** `/hinh_anh/{id}`

Remove an image from maintenance request.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "message": "Image deleted successfully"
}
```

---

## 5. Feedback and Rating Endpoints

### 5.1 Submit Feedback/Rating
**POST** `/phan_hoi`

Submit feedback and rating after maintenance is completed (1-5 stars).

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "id_yeu_cau": 10,
    "id_cu_dan": 1,
    "danh_gia": 5,
    "binh_luan": "Excellent work! Very professional and fast. The issue was completely resolved."
}
```

**Response (201):**
```json
{
    "message": "Feedback submitted successfully",
    "data": {
        "id_phan_hoi": 5,
        "id_yeu_cau": 10,
        "id_cu_dan": 1,
        "danh_gia": 5,
        "binh_luan": "Excellent work! Very professional and fast. The issue was completely resolved.",
        "created_at": "2026-02-02T15:00:00Z",
        "updated_at": "2026-02-02T15:00:00Z"
    }
}
```

**Error (409 - Conflict):**
```json
{
    "error": "Feedback already exists for this request",
    "existing_id": 5
}
```

**Error (400 - Bad Request):**
```json
{
    "error": "Can only rate completed maintenance requests",
    "current_status": "dang_xu_ly"
}
```

---

### 5.2 Update Feedback
**PUT** `/phan_hoi/{id}`

Update existing feedback/rating.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "danh_gia": 4,
    "binh_luan": "Updated comment: Good service."
}
```

**Response (200):**
```json
{
    "message": "Feedback updated successfully",
    "data": {
        "id_phan_hoi": 5,
        "id_yeu_cau": 10,
        "danh_gia": 4,
        "binh_luan": "Updated comment: Good service.",
        "updated_at": "2026-02-02T15:10:00Z"
    }
}
```

---

### 5.3 Get All Feedback
**GET** `/phan_hoi`

Get all feedback entries (with optional filters).

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `id_cu_dan` (optional) - Filter by resident ID
- `danh_gia` (optional) - Filter by rating (1-5)

**Response (200):**
```json
[
    {
        "id_phan_hoi": 5,
        "id_yeu_cau": 10,
        "id_cu_dan": 1,
        "danh_gia": 5,
        "binh_luan": "Excellent work!",
        "created_at": "2026-02-02T15:00:00Z",
        "yeu_cau": {
            "id_yeu_cau": 10,
            "mo_ta": "Water is leaking from the ceiling"
        },
        "cu_dan": {
            "id_nguoi_dung": 1,
            "ten": "Nguyễn Văn A"
        }
    }
]
```

---

### 5.4 Delete Feedback
**DELETE** `/phan_hoi/{id}`

Delete feedback entry.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "message": "Feedback deleted successfully"
}
```

---

### 5.5 Get Average Rating for Maintenance Type
**GET** `/phan_hoi/rating/average`

Get average rating for a specific maintenance type.

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `id_loai_su_co` (required) - Maintenance type ID

**Response (200):**
```json
{
    "loai_su_co_id": 2,
    "average_rating": 4.5,
    "total_reviews": 12
}
```

---

### 5.6 Get Resident's Average Rating
**GET** `/resident/{id}/rating`

Get resident's overall service rating based on their completed requests.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "resident_id": 1,
    "average_rating": 4.8,
    "total_reviews": 5
}
```

---

## 6. Notifications Endpoint

### 6.1 Get Notifications
**GET** `/users/{id}/notifications`

Get notifications about completed maintenance tasks.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
[
    {
        "id_yeu_cau": 10,
        "id_cu_dan": 1,
        "mo_ta": "Water is leaking from the ceiling",
        "trang_thai": "hoan_thanh",
        "phan_cong": [
            {
                "id_phan_cong": 8,
                "id_nhan_vien": 10,
                "trang_thai": "hoan_thanh",
                "ngay_ket_thuc": "2026-02-02T14:30:00Z"
            }
        ]
    }
]
```

---

## Error Responses

### 400 - Bad Request
```json
{
    "error": "Description of what went wrong"
}
```

### 401 - Unauthorized
```json
{
    "error": "Invalid email or password"
}
```

### 403 - Forbidden
```json
{
    "error": "You do not have permission to access this resource"
}
```

### 404 - Not Found
```json
{
    "error": "Request not found"
}
```

### 409 - Conflict
```json
{
    "error": "Resource already exists"
}
```

### 422 - Validation Error
```json
{
    "errors": {
        "field_name": ["Error message 1", "Error message 2"]
    }
}
```

### 500 - Server Error
```json
{
    "error": "Internal server error description"
}
```

---

## Example Workflow: Creating and Tracking a Maintenance Request

### Step 1: Register/Login
```bash
POST /api/register
{
    "email": "resident@example.com",
    "ten": "Nguyễn Văn A",
    "mat_khau": "password123",
    "mat_khau_confirmation": "password123",
    "dien_thoai": "0912345678"
}
```

### Step 2: Create Maintenance Request
```bash
POST /api/yeu_cau
Authorization: Bearer {token}
{
    "id_cu_dan": 1,
    "id_can_ho": 5,
    "id_loai_su_co": 2,
    "mo_ta": "Water is leaking",
    "thoi_gian_uu_tien": "gan"
}
```

### Step 3: Upload Image(s) - Optional
```bash
POST /api/yeu_cau/10/upload-image
Authorization: Bearer {token}
Content-Type: multipart/form-data

hinh_anh: [image file]
```

### Step 4: Confirm Request
```bash
POST /api/yeu_cau/10/confirm
Authorization: Bearer {token}
{}
```

### Step 5: Track Status
```bash
GET /api/users/1/requests
Authorization: Bearer {token}
```

### Step 6: Rate After Completion
```bash
POST /api/phan_hoi
Authorization: Bearer {token}
{
    "id_yeu_cau": 10,
    "id_cu_dan": 1,
    "danh_gia": 5,
    "binh_luan": "Excellent service!"
}
```

---

## Testing with cURL/Postman

**Register:**
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "resident@example.com",
    "ten": "Nguyễn Văn A",
    "mat_khau": "password123",
    "mat_khau_confirmation": "password123"
  }'
```

**Create Request:**
```bash
curl -X POST http://localhost:8000/api/yeu_cau \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "id_cu_dan": 1,
    "id_can_ho": 5,
    "id_loai_su_co": 2,
    "mo_ta": "Water leaking",
    "thoi_gian_uu_tien": "gan"
  }'
```

**Upload Image:**
```bash
curl -X POST http://localhost:8000/api/yeu_cau/10/upload-image \
  -H "Authorization: Bearer {token}" \
  -F "hinh_anh=@/path/to/image.jpg"
```
