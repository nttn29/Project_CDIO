<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TechnicianJob;
use Illuminate\Http\Request;

class TechnicianJobController extends Controller
{
    public function index(Request $request)
    {
        $query = TechnicianJob::query();

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

    public function show(TechnicianJob $job)
    {
        return response()->json($job);
    }

    public function showByCode(string $code)
    {
        $job = TechnicianJob::where('code', $code)->firstOrFail();
        return response()->json($job);
    }

    public function update(Request $request, TechnicianJob $job)
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

        $job->fill($data);
        $job->save();

        return response()->json($job);
    }

    public function store(Request $request)
    {
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

    public function destroy(TechnicianJob $job)
    {
        $job->delete();
        return response()->json(['message' => 'deleted']);
    }
}
