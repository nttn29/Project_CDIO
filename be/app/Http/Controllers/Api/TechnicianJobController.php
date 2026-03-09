<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TechnicianJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * TechnicianJobController – Quản lý danh sách công việc dành cho kỹ thuật viên.
 *
 * Controller này hỗ trợ hai chế độ hoạt động:
 *  1. Chế độ chính (primary mode): Dùng bảng technician_jobs nếu bảng đã tồn tại.
 *  2. Chế độ dự phòng (fallback mode): Đọc trực tiếp từ bảng phan_cong nếu bảng
 *     technician_jobs chưa được migrate. Giúp hệ thống luôn hoạt động dù chưa
 *     chạy migration mới nhất.
 *
 * Các endpoint (prefix /api/technician/jobs):
 *  GET    /          – Lấy danh sách công việc (có phân trang, lọc theo technician_id / status / q)
 *  GET    /stats     – Thống kê nhanh (hôm nay / đang làm / hoàn thành / 5 việc mới nhất)
 *  POST   /          – Tạo công việc mới (chỉ chế độ chính)
 *  GET    /{job}     – Xem chi tiết theo ID
 *  GET    /code/{c}  – Xem chi tiết theo mã code
 *  PATCH  /{job}     – Cập nhật trạng thái / thông tin công việc
 *  DELETE /{job}     – Xoá công việc (chỉ chế độ chính)
 */
class TechnicianJobController extends Controller
{
    /**
     * Lấy danh sách công việc của kỹ thuật viên (có phân trang).
     *
     * Query params hỗ trợ:
     *  - technician_id : lọc theo ID kỹ thuật viên
     *  - status        : lọc theo trạng thái (moi|dang_xu_ly|hoan_thanh|huy|all)
     *  - q             : tìm kiếm tự do theo code / title / location
     *  - per_page      : số bản ghi mỗi trang (mặc định 5)
     */
    public function index(Request $request)
    {
        // Nếu bảng technician_jobs chưa tồn tại, dùng dữ liệu fallback từ phan_cong
        if (!$this->hasTechnicianJobsTable()) {
            return response()->json($this->paginateFallbackAssignments($request));
        }

        $query = TechnicianJob::query();

        // Lọc theo kỹ thuật viên cụ thể
        if ($request->filled('technician_id')) {
            $query->where('technician_id', (int) $request->get('technician_id'));
        }

        // Lọc theo trạng thái (bỏ qua nếu là 'all')
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Tìm kiếm theo code, tiêu đề hoặc địa điểm
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('code', 'like', "%{$q}%")
                    ->orWhere('title', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%");
            });
        }

        $perPage = (int) $request->get('per_page', 5);
        $jobs = $query->orderByDesc('created_at')->paginate($perPage);

        return response()->json($jobs);
    }

    /**
     * Xem chi tiết một công việc theo ID.
     *
     * Nếu bảng technician_jobs chưa tồn tại, tìm bản ghi tương ứng
     * trong bảng phan_cong (fallback).
     *
     * @param mixed $job ID công việc
     */
    public function show($job)
    {
        if (!$this->hasTechnicianJobsTable()) {
            $assignment = $this->findFallbackAssignment((int) $job);
            if (!$assignment) {
                return response()->json(['message' => 'Not found'], 404);
            }
            return response()->json($assignment);
        }

        $model = TechnicianJob::findOrFail((int) $job);
        return response()->json($model);
    }

    /**
     * Xem chi tiết một công việc theo mã code (ví dụ: "PC-12").
     *
     * Fallback: mã code dạng "PC-{id}" được ánh xạ sang id_phan_cong trong bảng phan_cong.
     *
     * @param string $code Mã code công việc
     */
    public function showByCode(string $code)
    {
        if (!$this->hasTechnicianJobsTable()) {
            // Fallback chỉ hỗ trợ mã định dạng "PC-{id}"
            if (!Str::startsWith($code, 'PC-')) {
                return response()->json(['message' => 'Not found'], 404);
            }
            $assignmentId = (int) Str::after($code, 'PC-');
            $assignment = $this->findFallbackAssignment($assignmentId);
            if (!$assignment) {
                return response()->json(['message' => 'Not found'], 404);
            }
            return response()->json($assignment);
        }

        $job = TechnicianJob::where('code', $code)->firstOrFail();
        return response()->json($job);
    }

    /**
     * Cập nhật thông tin / trạng thái của một công việc.
     *
     * Sau khi cập nhật trạng thái, hệ thống tự động đồng bộ ngược lại
     * bảng phan_cong và yeu_cau_bao_tri (qua syncAssignmentStatusFromJob).
     *
     * Chế độ fallback: ghi thẳng vào bảng phan_cong và yeu_cau_bao_tri.
     *
     * @param Request $request Dữ liệu cần cập nhật
     * @param mixed   $job     ID công việc
     */
    public function update(Request $request, $job)
    {
        $data = $request->validate([
            'code'         => 'nullable|string',
            'title'        => 'nullable|string',
            'location'     => 'nullable|string|nullable',
            'description'  => 'nullable|string|nullable',
            'scheduled_at' => 'nullable|date',
            'due_at'       => 'nullable|date',
            'status'       => 'nullable|in:moi,dang_xu_ly,hoan_thanh,huy',
            'priority'     => 'nullable|in:thap,trung_binh,cao',
            'technician_id'=> 'nullable|integer',
        ]);

        // ── Chế độ fallback: cập nhật trực tiếp bảng phan_cong ───────────
        if (!$this->hasTechnicianJobsTable()) {
            $assignmentId = (int) $job;
            $assignment = DB::table('phan_cong')->where('id_phan_cong', $assignmentId)->first();
            if (!$assignment) {
                return response()->json(['message' => 'Not found'], 404);
            }

            $assignmentUpdate = ['updated_at' => now()];

            // Cập nhật kỹ thuật viên phụ trách nếu có trong request
            if (array_key_exists('technician_id', $data)) {
                $assignmentUpdate['id_ky_thuat_vien'] = $data['technician_id'];
            }

            // Tách scheduled_at thành ngay_phan_cong và gio_hen cho bảng phan_cong
            if (array_key_exists('scheduled_at', $data) && !empty($data['scheduled_at'])) {
                $dateTime = date_create($data['scheduled_at']);
                if ($dateTime) {
                    $assignmentUpdate['ngay_phan_cong'] = $dateTime->format('Y-m-d');
                    $assignmentUpdate['gio_hen']        = $dateTime->format('H:i:s');
                }
            }

            // Đồng bộ trạng thái sang bảng phan_cong và yeu_cau_bao_tri
            if (array_key_exists('status', $data)) {
                $assignmentUpdate['trang_thai'] = $this->mapAssignmentStatusFromTechStatus($data['status']);
                DB::table('yeu_cau_bao_tri')
                    ->where('id_yeu_cau', $assignment->id_yeu_cau)
                    ->update([
                        'trang_thai' => $this->mapRequestStatusFromTechStatus($data['status']),
                        'updated_at' => now(),
                    ]);
            }

            DB::table('phan_cong')->where('id_phan_cong', $assignmentId)->update($assignmentUpdate);
            return response()->json($this->findFallbackAssignment($assignmentId));
        }

        // ── Chế độ chính: cập nhật bảng technician_jobs ─────────────────
        $model = TechnicianJob::findOrFail((int) $job);
        $model->fill($data);
        $model->save();

        // Đồng bộ trạng thái ngược lại bảng phan_cong (nếu code là dạng "PC-{id}")
        $this->syncAssignmentStatusFromJob($model);

        return response()->json($model);
    }

    /**
     * Tạo mới một công việc trong bảng technician_jobs.
     *
     * Chỉ khả dụng khi bảng technician_jobs đã được migrate.
     * Thông thường, công việc được tạo tự động từ PhanCongController::store;
     * endpoint này dành cho trường hợp tạo thủ công.
     *
     * @param Request $request Dữ liệu công việc mới
     */
    public function store(Request $request)
    {
        // Từ chối nếu bảng chưa tồn tại
        if (!$this->hasTechnicianJobsTable()) {
            return response()->json(['message' => 'Technician jobs table not ready'], 503);
        }

        $data = $request->validate([
            'code'         => 'required|string|unique:technician_jobs,code',
            'title'        => 'required|string',
            'location'     => 'nullable|string',
            'description'  => 'nullable|string',
            'status'       => 'nullable|in:moi,dang_xu_ly,hoan_thanh,huy',
            'priority'     => 'nullable|in:thap,trung_binh,cao',
            'scheduled_at' => 'nullable|date',
            'due_at'       => 'nullable|date',
            'technician_id'=> 'nullable|integer',
        ]);

        $job = TechnicianJob::create($data);
        return response()->json($job, 201);
    }

    /**
     * Xoá một công việc khỏi bảng technician_jobs.
     *
     * Không hỗ trợ xoá ở chế độ fallback vì dữ liệu thuộc bảng phan_cong
     * và việc xoá có thể ảnh hưởng đến toàn bộ quy trình phân công.
     *
     * @param mixed $job ID công việc cần xoá
     */
    public function destroy($job)
    {
        if (!$this->hasTechnicianJobsTable()) {
            return response()->json(['message' => 'Delete is not supported in fallback mode'], 400);
        }

        $model = TechnicianJob::findOrFail((int) $job);
        $model->delete();
        return response()->json(['message' => 'deleted']);
    }

    /**
     * Trả về thống kê nhanh cho màn hình trang chủ của ứng dụng kỹ thuật viên.
     *
     * Response JSON:
     *  - today  : số công việc được lên lịch hôm nay
     *  - doing  : số công việc đang thực hiện
     *  - done   : số công việc đã hoàn thành
     *  - latest : 5 công việc mới tạo gần nhất
     *
     * @param Request $request Query param: technician_id (lọc theo kỹ thuật viên)
     */
    public function stats(Request $request) {
        // ── Fallback: đọc từ bảng phan_cong ─────────────────────────────
        if (!$this->hasTechnicianJobsTable()) {
            $technicianId = $request->filled('technician_id') ? (int) $request->get('technician_id') : null;
            $q = DB::table('phan_cong');
            if ($technicianId) {
                $q->where('id_ky_thuat_vien', $technicianId);
            }

            $today = now()->toDateString();
            $todayJobs = (clone $q)->whereDate('ngay_phan_cong', $today)->count();
            $doing     = (clone $q)->where('trang_thai', 'dang_thuc_hien')->count();
            $done      = (clone $q)->where('trang_thai', 'hoan_thanh')->count();

            // Lấy 5 phân công mới nhất kèm thông tin căn hộ
            $latest = (clone $q)
                ->leftJoin('yeu_cau_bao_tri as y', 'y.id_yeu_cau', '=', 'phan_cong.id_yeu_cau')
                ->leftJoin('can_ho as c', 'c.id_can_ho', '=', 'y.id_can_ho')
                ->select('phan_cong.*', 'y.mo_ta', 'c.so_can_ho')
                ->orderByDesc('phan_cong.created_at')
                ->take(5)
                ->get()
                ->map(function ($row) {
                    return [
                        'id'       => $row->id_phan_cong,
                        'code'     => 'PC-' . $row->id_phan_cong,
                        'title'    => $row->mo_ta ?: 'Cong viec bao tri',
                        'location' => $row->so_can_ho ? ('Can ho ' . $row->so_can_ho) : null,
                        'status'   => $this->mapTechStatusFromAssignmentStatus($row->trang_thai),
                    ];
                });

            return response()->json([
                'today'  => $todayJobs,
                'doing'  => $doing,
                'done'   => $done,
                'latest' => $latest,
            ]);
        }

        // ── Chế độ chính: đọc từ bảng technician_jobs ───────────────────
        $today = now()->toDateString();
        $todayJobs = TechnicianJob::whereDate('scheduled_at', $today)->count(); // công việc hôm nay
        $doing     = TechnicianJob::where('status', 'dang_xu_ly')->count();     // đang thực hiện
        $done      = TechnicianJob::where('status', 'hoan_thanh')->count();     // đã hoàn thành
        $latest    = TechnicianJob::orderByDesc('created_at')->take(5)->get();  // 5 việc gần nhất

        return response()->json([
            'today'  => $todayJobs,
            'doing'  => $doing,
            'done'   => $done,
            'latest' => $latest,
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════
    //  PRIVATE HELPERS
    // ══════════════════════════════════════════════════════════════════════

    /**
     * Kiểm tra bảng technician_jobs đã tồn tại trong CSDL chưa.
     * Dùng để quyết định chạy chế độ chính hay chế độ fallback.
     */
    private function hasTechnicianJobsTable(): bool
    {
        return Schema::hasTable('technician_jobs');
    }

    /**
     * Trả về danh sách công việc dạng phân trang từ bảng phan_cong (fallback).
     *
     * Dữ liệu trả về được chuẩn hoá theo cấu trúc giống TechnicianJob để
     * frontend không cần phân biệt chế độ đang hoạt động.
     *
     * @param Request $request Tham số phân trang và lọc
     * @return array Cấu trúc phân trang: current_page, data, last_page, per_page, total
     */
    private function paginateFallbackAssignments(Request $request): array
    {
        $page         = max((int) $request->get('page', 1), 1);
        $perPage      = max((int) $request->get('per_page', 5), 1);
        $status       = $request->get('status');
        $q            = $request->get('q');
        $technicianId = $request->filled('technician_id') ? (int) $request->get('technician_id') : null;

        // Join với yeu_cau_bao_tri, loai_su_co, can_ho để lấy đầy đủ thông tin
        $query = DB::table('phan_cong as p')
            ->leftJoin('yeu_cau_bao_tri as y', 'y.id_yeu_cau', '=', 'p.id_yeu_cau')
            ->leftJoin('loai_su_co as l', 'l.id_loai_su_co', '=', 'y.id_loai_su_co')
            ->leftJoin('can_ho as c', 'c.id_can_ho', '=', 'y.id_can_ho')
            ->select('p.*', 'y.mo_ta', 'y.thoi_gian_uu_tien', 'l.ten_loai', 'c.so_can_ho');

        if ($technicianId) {
            $query->where('p.id_ky_thuat_vien', $technicianId);
        }

        // Chuyển đổi trạng thái tech → trạng thái phan_cong trước khi lọc
        if ($status && $status !== 'all') {
            $query->where('p.trang_thai', $this->mapAssignmentStatusFromTechStatus($status));
        }

        // Tìm kiếm tự do theo mô tả, loại sự cố, số căn hộ hoặc ID phân công
        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('y.mo_ta', 'like', "%{$q}%")
                    ->orWhere('l.ten_loai', 'like', "%{$q}%")
                    ->orWhere('c.so_can_ho', 'like', "%{$q}%")
                    ->orWhere('p.id_phan_cong', 'like', "%{$q}%");
            });
        }

        $total = (clone $query)->count();
        $rows  = $query
            ->orderByDesc('p.created_at')
            ->forPage($page, $perPage)
            ->get()
            ->map(function ($row) {
                // Gộp ngay_phan_cong + gio_hen thành chuỗi datetime scheduled_at
                $scheduledAt = null;
                if (!empty($row->ngay_phan_cong)) {
                    $scheduledAt = $row->ngay_phan_cong . ' ' . ($row->gio_hen ?: '09:00:00');
                }

                return [
                    'id'            => $row->id_phan_cong,
                    'code'          => 'PC-' . $row->id_phan_cong,
                    'title'         => $row->ten_loai ?: 'Yeu cau bao tri',
                    'location'      => $row->so_can_ho ? ('Can ho ' . $row->so_can_ho) : null,
                    'description'   => $row->mo_ta,
                    'scheduled_at'  => $scheduledAt,
                    'due_at'        => null,
                    'status'        => $this->mapTechStatusFromAssignmentStatus($row->trang_thai),
                    'priority'      => $this->mapPriorityFromRequest($row->thoi_gian_uu_tien),
                    'technician_id' => $row->id_ky_thuat_vien,
                    'created_at'    => $row->created_at,
                    'updated_at'    => $row->updated_at,
                ];
            })
            ->values();

        return [
            'current_page' => $page,
            'data'         => $rows,
            'last_page'    => (int) ceil($total / $perPage),
            'per_page'     => $perPage,
            'total'        => $total,
        ];
    }

    /**
     * Tìm và trả về một bản ghi phân công từ bảng phan_cong theo ID (fallback).
     *
     * @param int $assignmentId ID phân công (id_phan_cong)
     * @return array|null Mảng chuẩn hoá theo cấu trúc TechnicianJob, hoặc null nếu không tìm thấy
     */
    private function findFallbackAssignment(int $assignmentId): ?array
    {
        $row = DB::table('phan_cong as p')
            ->leftJoin('yeu_cau_bao_tri as y', 'y.id_yeu_cau', '=', 'p.id_yeu_cau')
            ->leftJoin('loai_su_co as l', 'l.id_loai_su_co', '=', 'y.id_loai_su_co')
            ->leftJoin('can_ho as c', 'c.id_can_ho', '=', 'y.id_can_ho')
            ->where('p.id_phan_cong', $assignmentId)
            ->select('p.*', 'y.mo_ta', 'y.thoi_gian_uu_tien', 'l.ten_loai', 'c.so_can_ho')
            ->first();

        if (!$row) {
            return null;
        }

        $scheduledAt = null;
        if (!empty($row->ngay_phan_cong)) {
            $scheduledAt = $row->ngay_phan_cong . ' ' . ($row->gio_hen ?: '09:00:00');
        }

        return [
            'id'            => $row->id_phan_cong,
            'code'          => 'PC-' . $row->id_phan_cong,
            'title'         => $row->ten_loai ?: 'Yeu cau bao tri',
            'location'      => $row->so_can_ho ? ('Can ho ' . $row->so_can_ho) : null,
            'description'   => $row->mo_ta,
            'scheduled_at'  => $scheduledAt,
            'due_at'        => null,
            'status'        => $this->mapTechStatusFromAssignmentStatus($row->trang_thai),
            'priority'      => $this->mapPriorityFromRequest($row->thoi_gian_uu_tien),
            'technician_id' => $row->id_ky_thuat_vien,
            'created_at'    => $row->created_at,
            'updated_at'    => $row->updated_at,
        ];
    }

    /**
     * Chuyển đổi trạng thái từ bảng phan_cong sang trạng thái TechnicianJob.
     *
     * Bảng ánh xạ:
     *  dang_thuc_hien → dang_xu_ly
     *  hoan_thanh     → hoan_thanh
     *  huy            → huy
     *  (mặc định)     → moi
     */
    private function mapTechStatusFromAssignmentStatus(?string $status): string
    {
        return match ($status) {
            'dang_thuc_hien' => 'dang_xu_ly',
            'hoan_thanh'     => 'hoan_thanh',
            'huy'            => 'huy',
            default          => 'moi',
        };
    }

    /**
     * Chuyển đổi trạng thái TechnicianJob sang trạng thái của bảng phan_cong.
     *
     * Bảng ánh xạ:
     *  dang_xu_ly  → dang_thuc_hien
     *  hoan_thanh  → hoan_thanh
     *  huy         → huy
     *  (mặc định)  → cho_thuc_hien
     */
    private function mapAssignmentStatusFromTechStatus(?string $status): string
    {
        return match ($status) {
            'dang_xu_ly' => 'dang_thuc_hien',
            'hoan_thanh' => 'hoan_thanh',
            'huy'        => 'huy',
            default      => 'cho_thuc_hien',
        };
    }

    /**
     * Chuyển đổi trạng thái TechnicianJob sang trạng thái của bảng yeu_cau_bao_tri.
     *
     * Bảng ánh xạ:
     *  dang_xu_ly  → dang_xu_ly
     *  hoan_thanh  → hoan_thanh
     *  huy         → tu_choi
     *  (mặc định)  → da_xac_nhan
     */
    private function mapRequestStatusFromTechStatus(?string $status): string
    {
        return match ($status) {
            'dang_xu_ly' => 'dang_xu_ly',
            'hoan_thanh' => 'hoan_thanh',
            'huy'        => 'tu_choi',
            default      => 'da_xac_nhan',
        };
    }

    /**
     * Chuyển đổi giá trị thoi_gian_uu_tien (từ yeu_cau_bao_tri) sang mức ưu tiên chuẩn.
     *
     * Bảng ánh xạ:
     *  thap              → thap
     *  cao/khan_cap/gan  → cao
     *  (mặc định)        → trung_binh
     */
    private function mapPriorityFromRequest(?string $priority): string
    {
        return match ($priority) {
            'thap'                  => 'thap',
            'cao', 'khan_cap', 'gan'=> 'cao',
            default                 => 'trung_binh',
        };
    }

    /**
     * Đồng bộ trạng thái từ TechnicianJob ngược lại bảng phan_cong và yeu_cau_bao_tri.
     *
     * Chỉ chạy khi code của job có dạng "PC-{id}" (tức job được tạo từ phân công admin).
     * Giúp admin nhìn thấy tiến độ thực tế do kỹ thuật viên cập nhật.
     *
     * @param TechnicianJob $job Bản ghi vừa được cập nhật
     */
    private function syncAssignmentStatusFromJob(TechnicianJob $job): void
    {
        // Chỉ xử lý khi code theo định dạng "PC-{id}"
        if (!str_starts_with((string) $job->code, 'PC-')) {
            return;
        }

        $assignmentId = (int) substr((string) $job->code, 3);
        if ($assignmentId <= 0) {
            return;
        }

        $assignment = DB::table('phan_cong')
            ->where('id_phan_cong', $assignmentId)
            ->first();

        if (!$assignment) {
            return;
        }

        // Chuyển trạng thái tech → trạng thái phan_cong
        $assignmentStatus = match ($job->status) {
            'dang_xu_ly' => 'dang_thuc_hien',
            'hoan_thanh' => 'hoan_thanh',
            'huy'        => 'huy',
            default      => 'cho_thuc_hien',
        };

        DB::table('phan_cong')
            ->where('id_phan_cong', $assignmentId)
            ->update([
                'trang_thai' => $assignmentStatus,
                'updated_at' => now(),
            ]);

        // Chuyển trạng thái tech → trạng thái yeu_cau_bao_tri
        $requestStatus = match ($job->status) {
            'dang_xu_ly' => 'dang_xu_ly',
            'hoan_thanh' => 'hoan_thanh',
            'huy'        => 'tu_choi',
            default      => 'da_xac_nhan',
        };

        DB::table('yeu_cau_bao_tri')
            ->where('id_yeu_cau', $assignment->id_yeu_cau)
            ->update([
                'trang_thai' => $requestStatus,
                'updated_at' => now(),
            ]);
    }
}
