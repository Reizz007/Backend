<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_email',
        'action',
        'entity_type',
        'entity_id',
        'entity_name',
        'description',
        'device_id',
        'device_name',
        'ip_address',
        'user_agent',
        'changes',
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Record an audit log entry conveniently
     */
    public static function record(
        string $action,
        string $entityType,
        string $description,
        ?string $entityId = null,
        ?string $entityName = null,
        ?array $changes = null,
        ?Request $request = null,
        ?string $userEmail = null
    ): self {
        $req = $request ?? request();
        $deviceId = $req ? trim((string) ($req->header('X-Device-Id') ?? $req->input('device_id') ?? '')) : null;
        $deviceName = $req ? trim((string) ($req->header('X-Device-Name') ?? $req->input('device_name') ?? '')) : null;
        $ip = $req ? ($req->ip() ?: '127.0.0.1') : '127.0.0.1';
        $userAgent = $req ? substr((string) $req->userAgent(), 0, 500) : null;
        $email = $userEmail;

        if (empty($email) && $req) {
            $email = $req->user()?->email
                ?? $req->input('adminEmail')
                ?? $req->input('email')
                ?? Setting::get('admin_email', 'chetranicky@gmail.com');
        }

        return self::create([
            'user_id' => $req?->user()?->id,
            'user_email' => $email,
            'action' => strtoupper($action),
            'entity_type' => $entityType,
            'entity_id' => $entityId ? (string) $entityId : null,
            'entity_name' => $entityName ? (string) $entityName : null,
            'description' => $description,
            'device_id' => $deviceId ?: 'unknown',
            'device_name' => $deviceName ?: 'Browser Device',
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'changes' => $changes,
        ]);
    }
}
