<?php

namespace App\Services;

use App\Enums\RoleScope;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class SettingService
{
    public function __construct(
        protected AuditLogService $auditLog
    ) {}

    public function get(string $key, RoleScope $scope, ?int $organizationId = null, mixed $default = null): mixed
    {
        $setting = Setting::where('scope', $scope->value)
            ->where('organization_id', $scope === RoleScope::SUPER_ADMIN ? null : $organizationId)
            ->where('key', $key)
            ->first();

        if (! $setting) {
            return $default;
        }

        return match ($setting->value_type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    public function set(string $key, mixed $value, RoleScope $scope, ?int $organizationId = null, string $group = 'general', string $valueType = 'string'): Setting
    {
        $orgId = $scope === RoleScope::SUPER_ADMIN ? null : $organizationId;
        $stringValue = is_array($value) ? json_encode($value) : (string) $value;

        $setting = Setting::updateOrCreate(
            [
                'scope' => $scope->value,
                'organization_id' => $orgId,
                'key' => $key,
            ],
            [
                'value' => $stringValue,
                'value_type' => $valueType,
                'group' => $group,
            ]
        );

        return $setting;
    }

    public function updateMany(array $settings, RoleScope $scope, ?int $organizationId = null, string $group = 'general'): void
    {
        DB::transaction(function () use ($settings, $scope, $organizationId, $group) {
            foreach ($settings as $key => $value) {
                $this->set($key, $value, $scope, $organizationId, $group);
            }

            $this->auditLog->log(
                action: 'settings.updated',
                module: 'settings',
                newValues: $settings,
                organizationId: $organizationId
            );
        });
    }

    public function getAllForScope(RoleScope $scope, ?int $organizationId = null): array
    {
        $query = Setting::where('scope', $scope->value);

        if ($scope === RoleScope::SUPER_ADMIN) {
            $query->whereNull('organization_id');
        } else {
            $query->where('organization_id', $organizationId);
        }

        return $query->pluck('value', 'key')->all();
    }
}
