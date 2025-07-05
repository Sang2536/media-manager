<?php

namespace App\Http\Controllers;

use App\Enums\LogActionEnum;
use App\Models\MediaLog;
use Illuminate\Http\Request;

class MediaLogController extends Controller
{
    public function index() {
        $logs = MediaLog::orderByDesc('created_at')->paginate(12);

        $logs->transform(function ($log) {
            $enum = LogActionEnum::fromAction($log->action);

            $hasError = stripos($log->action, 'error');
            if ($hasError !== false) {
                $enum = LogActionEnum::fromAction(LogActionEnum::ERROR->value);
                $log->hasError = true;
            }

            $log->action_icon = $enum?->icon() ?? '⚙️';
            $log->action_color = $enum?->badgeColor() ?? [
                'bg' => 'bg-gray-100',
                'text' => 'text-gray-700',
            ];

            return $log;
        });

        return view('media.logs.index', compact('logs'));
    }

    public function create() {
        //
    }

    public function store() {
        //
    }

    public function show() {
        //
    }

    public function edit() {
        //
    }

    public function update()
    {
        //
    }

    public function destroy()
    {
        //
    }

    public function clear(Request $request)
    {
        $user = $request->user();
        $count = MediaLog::count();

        // Ghi log hành động xoá trước khi xoá toàn bộ
        $log = MediaLog::create([
            'user_id'     => $user?->id,
            'action'      => 'delete_logs',
            'target_type' => 'media_logs',
            'target_id'   => null,
            'description' => "Xoá toàn bộ {$count} bản ghi trong media_logs",
            'data'        => [
                'deleted_count' => $count,
                'log_type' => 'system:self-logging'
            ],
            'ip'          => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

        // force delete
        MediaLog::where('id', '!=', $log->id)->forceDelete();

        return response()->json(['message' => 'Tất cả log đã được xoá.']);
    }
}
