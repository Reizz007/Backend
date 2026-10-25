<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs with optional device and action filters.
     */
    public function index(Request $request): JsonResponse
    {
        $currentDeviceId = trim((string) ($request->header('X-Device-Id') ?? $request->query('device_id') ?? ''));
        $primaryDeviceId = Setting::get('primary_device_id', '');

        $query = AuditLog::query()->latest();

        // Filter: Show Only My Device
        if ($request->boolean('only_my_device')) {
            $myDevice = ! empty($currentDeviceId) ? $currentDeviceId : $primaryDeviceId;
            if (! empty($myDevice)) {
                $query->where('device_id', $myDevice);
            }
        } elseif ($request->filled('device_id') && $request->device_id !== 'all') {
            $query->where('device_id', $request->device_id);
        }

        // Filter: Action (CREATE, UPDATE, DELETE, SETTINGS, etc.)
        if ($request->filled('action') && $request->action !== 'all') {
            $actionUpper = strtoupper((string) $request->action);
            if ($actionUpper === 'EDIT') {
                $query->whereIn('action', ['UPDATE', 'EDIT', 'SETTINGS_CHANGE']);
            } else {
                $query->where('action', $actionUpper);
            }
        }

        // Filter: Entity Type (Product, Category, Order, etc.)
        if ($request->filled('entity_type') && $request->entity_type !== 'all') {
            $query->where('entity_type', ucfirst($request->entity_type));
        }

        // Search query
        if ($request->filled('search')) {
            $term = trim((string) $request->search);
            $query->where(function ($q) use ($term) {
                $q->where('description', 'like', "%{$term}%")
                    ->orWhere('entity_name', 'like', "%{$term}%")
                    ->orWhere('device_id', 'like', "%{$term}%")
                    ->orWhere('device_name', 'like', "%{$term}%")
                    ->orWhere('user_email', 'like', "%{$term}%")
                    ->orWhere('ip_address', 'like', "%{$term}%");
            });
        }

        $limit = min((int) ($request->input('limit', 100)), 200);
        $logs = $query->take($limit)->get();

        // Calculate summary stats
        $totalLogs = AuditLog::count();
        $myDeviceLogs = ! empty($currentDeviceId)
            ? AuditLog::where('device_id', $currentDeviceId)->count()
            : 0;
        $deleteLogs = AuditLog::whereIn('action', ['DELETE', 'BULK_DELETE'])->count();
        $editLogs = AuditLog::whereIn('action', ['UPDATE', 'EDIT', 'SETTINGS_CHANGE'])->count();

        return response()->json([
            'success' => true,
            'logs' => $logs,
            'stats' => [
                'total' => $totalLogs,
                'myDevice' => $myDeviceLogs,
                'deletes' => $deleteLogs,
                'edits' => $editLogs,
            ],
            'currentDeviceId' => $currentDeviceId,
            'primaryDeviceId' => $primaryDeviceId,
        ]);
    }

    /**
     * Clear all audit logs or logs for a specific device.
     */
    public function clear(Request $request): JsonResponse
    {
        $currentDeviceId = trim((string) ($request->header('X-Device-Id') ?? ''));

        if ($request->boolean('only_my_device') && ! empty($currentDeviceId)) {
            AuditLog::where('device_id', $currentDeviceId)->delete();
            $message = 'Audit logs for this device cleared successfully';
        } else {
            AuditLog::truncate();
            $message = 'All audit logs cleared from database';
        }

        AuditLog::record(
            action: 'DELETE',
            entityType: 'AuditLog',
            description: $message,
            request: $request
        );

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Delete a single audit log entry.
     */
    public function destroy($id): JsonResponse
    {
        $log = AuditLog::findOrFail($id);
        $log->delete();

        return response()->json([
            'success' => true,
            'message' => 'Audit log entry deleted',
        ]);
    }
}
