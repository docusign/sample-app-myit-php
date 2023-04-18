<?php

namespace App\Models;

use App\Enums\DocumentItemTypesEnum;
use App\Services\CurrentUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Class PermissionProfile
 *
 * @property int $id
 * @property string $name
 * @property string $ext_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Collection $equipments
 * @property Collection $software
 * @property Collection $employees
 *
 * @package App\Models
 */
class PermissionProfile extends Model
{
    /**
     * Admin permission profile ID
     */
    public const ADMIN_ID = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'ext_id',
    ];

    /**
     * Equipments relation
     *
     * @return BelongsToMany
     */
    public function equipments(): BelongsToMany
    {
        return $this->belongsToMany(
            DocumentItem::class,
            'document_item_permission_profile',
            'profile_id',
            'item_id'
        )->where('type', DocumentItemTypesEnum::EQUIPMENT);
    }

    /**
     * Software relation
     *
     * @return BelongsToMany
     */
    public function software(): BelongsToMany
    {
        return $this->belongsToMany(
            DocumentItem::class,
            'document_item_permission_profile',
            'profile_id',
            'item_id'
        )->where('type', DocumentItemTypesEnum::SOFTWARE);
    }

    /**
     * Employees relation
     *
     * @return HasMany
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'permission_profile_id')
            ->where('token_id', app(CurrentUser::class)->getTokenId());
    }
}
