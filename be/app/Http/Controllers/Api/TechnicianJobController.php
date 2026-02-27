<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TechnicianJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class TechnicianJobController extends Controller
{
    public function index(Request $request)
    {
        if (!$this->hasTechnicianJobsTable()) {
            return response()->json($this->paginateFallbackAssignments($request));
        }

        $query = TechnicianJob::query();
        if ($request->filled('technician_id')) {
            $query->where('technician_id', (int) $request->get('technician_id'));
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

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

    public function showByCode(string $code)
    {
        if (!$this->hasTechnicianJobsTable()) {
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

    public function update(Request $request, $job)
    {
        $data = $request->validate([
            'code' => 'nullable|string',
            'title' => 'nullable|string',
            'location' => 'nullable|string|nullable',
            'description' => 'nullable|string|nullable',
            'scheduled_at' => 'nullable|date',
            'due_at' => 'nullable|date',
            'status' => 'nullable|in:moi,dang_xu_ly,hoan_thanh,huy',
            'priority' => 'nullable|in:thap,trung_binh,cao',
            'technician_id' => 'nullable|integer',
        ]);

        if (!$this->hasTechnicianJobsTable()) {
            $assignmentId = (int) $job;
            $assignment = DB::table('phan_cong')->where('id_phan_cong', $assignmentId)->first();
            if (!$assignment) {
                return response()->json(['message' => 'Not found'], 404);
            }

            $assignmentUpdate = ['updated_at' => now()];
            if (array_key_exists('technician_id', $data)) {
                $assignmentUpdate['id_ky_thuat_vien'] = $data['technician_id'];
            }
            if (array_key_exists('scheduled_at', $data) && !empty($data['scheduled_at'])) {
                $dateTime = date_create($data['scheduled_at']);
                if ($dateTime) {
                    $assignmentUpdate['ngay_phan_cong'] = $dateTime->format('Y-m-d');
                    $assignmentUpdate['gio_hen'] = $dateTime->format('H:i:s');
                }
            }
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

        $model = TechnicianJob::findOrFail((int) $job);
        $model->fill($data);
        $model->save();
        $this->syncAssignmentStatusFromJob($model);

        return response()->json($model);
    }

    public function store(Request $request)
    {
        if (!$this->hasTechnicianJobsTable()) {
            return response()->json(['message' => 'Technician jobs table not ready'], 503);
        }

        $data = $request->validate([
            'code' => 'required|string|unique:technician_jobs,code',
            'title' => 'required|string',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'nullable|in:moi,dang_xu_ly,hoan_thanh,huy',
            'priority' => 'nullable|in:thap,trung_binh,cao',
            'scheduled_at' => 'nullable|date',
            'due_at' => 'nullable|date',
            'technician_id' => 'nullable|integer',
        ]);

        $job = TechnicianJob::create($data);
        return response()->json($job, 201);
    }

    public function destroy($job)
    {
        if (!$this->hasTechnicianJobsTable()) {
            return response()->json(['message' => 'Delete is not supported in fallback mode'], 400);
        }

        $model = TechnicianJob::findOrFail((int) $job);
        $model->delete();
        return response()->json(['message' => 'deleted']);
    }

    public function stats(Request $request) {
        if (!$this->hasTechnicianJobsTable()) {
            $technicianId = $request->filled('technician_id') ? (int) $request->get('technician_id') : null;
            $q = DB::table('phan_cong');
            if ($technicianId) {
                $q->where('id_ky_thuat_vien', $technicianId);
            }

            $today = now()->toDateString();
            $todayJobs = (clone $q)->whereDate('ngay_phan_cong', $today)->count();
            $doing = (clone $q)->where('trang_thai', 'dang_thuc_hien')->count();
            $done = (clone $q)->where('trang_thai', 'hoan_thanh')->count();

            $latest = (clone $q)
                ->leftJoin('yeu_cau_bao_tri as y', 'y.id_yeu_cau', '=', 'phan_cong.id_yeu_cau')
                ->leftJoin('can_ho as c', 'c.id_can_ho', '=', 'y.id_can_ho')
                ->select('phan_cong.*', 'y.mo_ta', 'c.so_can_ho')
                ->orderByDesc('phan_cong.created_at')
                ->take(5)
                ->get()
                ->map(function ($row) {
                    return [
                        'id' => $row->id_phan_cong,
                        'code' => 'PC-' . $row->id_phan_cong,
                        'title' => $row->mo_ta ?: 'Cong viec bao tri',
                        'location' => $row->so_can_ho ? ('Can ho ' . $row->so_can_ho) : null,
                        'status' => $this->mapTechStatusFromAssignmentStatus($row->trang_thai),
                    ];
                });

            return response()->json([
                'today' => $todayJobs,
                'doing' => $doing,
                'done' => $done,
                'latest' => $latest,
            ]);
        }

        $today = now()->toDateString();
        // total jobs today
        $todayJobs = TechnicianJob::whereDate('scheduled_at', $today)->count();
        // doing
        $doing = TechnicianJob::where('status', 'dang_xu_ly')->count();
        // done
        $done = TechnicianJob::where('status', 'hoan_thanh')->count();
        // latest jobs
        $latest = TechnicianJob::orderByDesc('created_at')->take(5)->get();

        return response()->json([
            'today' => $todayJobs,
            'doing' => $doing,
            'done' => $done,
            'latest' => $latest
        ]);
    }

    private function hasTechnicianJobsTable(): bool
    {
        return Schema::hasTable('technician_jobs');
    }

    private function paginateFallbackAssignments(Request $request): array
    {
        $page = max((int) $request->get('page', 1), 1);
        $perPage = max((int) $request->get('per_page', 5), 1);
        $status = $request->get('status');
        $q = $request->get('q');
        $technicianId = $request->filled('technician_id') ? (int) $request->get('technician_id') : null;

        $query = DB::table('phan_cong as p')
            ->leftJoin('yeu_cau_bao_tri as y', 'y.id_yeu_cau', '=', 'p.id_yeu_cau')
            ->leftJoin('loai_su_co as l', 'l.id_loai_su_co', '=', 'y.id_loai_su_co')
            ->leftJoin('can_ho as c', 'c.id_can_ho', '=', 'y.id_can_ho')
            ->select('p.*', 'y.mo_ta', 'y.thoi_gian_uu_tien', 'l.ten_loai', 'c.so_can_ho');

        if ($technicianId) {
            $query->where('p.id_ky_thuat_vien', $technicianId);
        }

        if ($status && $status !== 'all') {
            $query->where('p.trang_thai', $this->mapAssignmentStatusFromTechStatus($status));
        }

        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('y.mo_ta', 'like', "%{$q}%")
                    ->orWhere('l.ten_loai', 'like', "%{$q}%")
                    ->orWhere('c.so_can_ho', 'like', "%{$q}%")
                    ->orWhere('p.id_phan_cong', 'like', "%{$q}%");
            });
        }

        $total = (clone $query)->count();
        $rows = $query
            ->orderByDesc('p.created_at')
            ->forPage($page, $perPage)
            ->get()
            ->map(function ($row) {
                $scheduledAt = null;
                if (!empty($row->ngay_phan_cong)) {
                    $scheduledAt = $row->ngay_phan_cong . ' ' . ($row->gio_hen ?: '09:00:00');
                }

                return [
                    'id' => $row->id_phan_cong,
                    'code' => 'PC-' . $row->id_phan_cong,
                    'title' => $row->ten_loai ?: 'Yeu cau bao tri',
                    'location' => $row->so_can_ho ? ('Can ho ' . $row->so_can_ho) : null,
                    'description' => $row->mo_ta,
                    'scheduled_at' => $scheduledAt,
                    'due_at' => null,
                    'status' => $this->mapTechStatusFromAssignmentStatus($row->trang_thai),
                    'priority' => $this->mapPriorityFromRequest($row->thoi_gian_uu_tien),
                    'technician_id' => $row->id_ky_thuat_vien,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ];
            })
            ->values();

        return [
            'current_page' => $page,
            'data' => $rows,
            'last_page' => (int) ceil($total / $perPage),
            'per_page' => $perPage,
            'total' => $total,
        ];
    }

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
            'id' => $row->id_phan_cong,
            'code' => 'PC-' . $row->id_phan_cong,
            'title' => $row->ten_loai ?: 'Yeu cau bao tri',
            'location' => $row->so_can_ho ? ('Can ho ' . $row->so_can_ho) : null,
            'description' => $row->mo_ta,
            'scheduled_at' => $scheduledAt,
            'due_at' => null,
            'status' => $this->mapTechStatusFromAssignmentStatus($row->trang_thai),
            'priority' => $this->mapPriorityFromRequest($row->thoi_gian_uu_tien),
            'technician_id' => $row->id_ky_thuat_vien,
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at,
        ];
    }

    private function mapTechStatusFromAssignmentStatus(?string $status): string
    {
        return match ($status) {
            'dang_thuc_hien' => 'dang_xu_ly',
            'hoan_thanh' => 'hoan_thanh',
            'huy' => 'huy',
            default => 'moi',
        };
    }

    private function mapAssignmentStatusFromTechStatus(?string $status): string
    {
        return match ($status) {
            'dang_xu_ly' => 'dang_thuc_hien',
            'hoan_thanh' => 'hoan_thanh',
            'huy' => 'huy',
            default => 'cho_thuc_hien',
        };
    }

    private function mapRequestStatusFromTechStatus(?string $status): string
    {
        return match ($status) {
            'dang_xu_ly' => 'dang_xu_ly',
            'hoan_thanh' => 'hoan_thanh',
            'huy' => 'tu_choi',
            default => 'da_xac_nhan',
        };
    }

    private function mapPriorityFromRequest(?string $priority): string
    {
        return match ($priority) {
            'thap' => 'thap',
            'cao', 'khan_cap', 'gan' => 'cao',
            default => 'trung_binh',
        };
    }

    private function syncAssignmentStatusFromJob(TechnicianJob $job): void
    {
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

        $assignmentStatus = match ($job->status) {
            'dang_xu_ly' => 'dang_thuc_hien',
            'hoan_thanh' => 'hoan_thanh',
            'huy' => 'huy',
            default => 'cho_thuc_hien',
        };

        DB::table('phan_cong')
            ->where('id_phan_cong', $assignmentId)
            ->update([
                'trang_thai' => $assignmentStatus,
                'updated_at' => now(),
            ]);

        $requestStatus = match ($job->status) {
            'dang_xu_ly' => 'dang_xu_ly',
            'hoan_thanh' => 'hoan_thanh',
            'huy' => 'tu_choi',
            default => 'da_xac_nhan',
        };

        DB::table('yeu_cau_bao_tri')
            ->where('id_yeu_cau', $assignment->id_yeu_cau)
            ->update([
                'trang_thai' => $requestStatus,
                'updated_at' => now(),
            ]);
    }
}
