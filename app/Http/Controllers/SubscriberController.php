<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function index(): JsonResponse
    {
        $subscribers = Subscriber::latest()->get();

        return response()->json([
            'success' => true,
            'subscribers' => $subscribers,
        ]);
    }

    public function updateTokens(Request $request, $id): JsonResponse
    {
        $sub = Subscriber::findOrFail($id);

        if ($request->has('tokensUsed')) {
            $sub->tokens_used = (int) $request->tokensUsed;
        }
        if ($request->has('tokenLimit')) {
            $sub->token_limit = (int) $request->tokenLimit;
        }
        if ($request->has('status')) {
            $sub->status = $request->status;
        }

        $sub->save();

        AuditLog::record(
            action: 'UPDATE',
            entityType: 'Subscriber',
            description: "Updated tokens for subscriber '{$sub->email}'",
            entityId: (string) $sub->id,
            entityName: $sub->email,
            request: $request
        );

        return response()->json([
            'success' => true,
            'subscriber' => $sub,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $subscriber = Subscriber::where('id', $id)
            ->orWhere('email', $id)
            ->first();

        if (! $subscriber) {
            return response()->json([
                'success' => false,
                'message' => 'Subscriber not found',
            ], 404);
        }

        $subEmail = $subscriber->email;
        $subId = (string) $subscriber->id;
        $subscriber->delete();

        AuditLog::record(
            action: 'DELETE',
            entityType: 'Subscriber',
            description: "Deleted subscriber '{$subEmail}'",
            entityId: $subId,
            entityName: $subEmail,
            request: request()
        );

        return response()->json([
            'success' => true,
            'message' => 'Subscriber deleted successfully',
        ]);
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $deletedCount = 0;
        if (! empty($ids) && is_array($ids)) {
            $deletedCount = Subscriber::whereIn('id', $ids)->delete();
        } elseif ($request->input('all') === true) {
            $deletedCount = Subscriber::count();
            Subscriber::query()->delete();
        }

        AuditLog::record(
            action: 'BULK_DELETE',
            entityType: 'Subscriber',
            description: "Bulk deleted {$deletedCount} subscribers from directory",
            changes: ['ids' => $ids, 'all' => $request->input('all')],
            request: $request
        );

        return response()->json([
            'success' => true,
            'message' => 'Subscribers deleted successfully',
        ]);
    }
}
