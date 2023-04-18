<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * Class DocumentItem
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class DocumentItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
    ];

    /**
     * Get by IDs
     *
     * @param array $ids
     * @return Collection
     */
    public function getByIds(array $ids): Collection
    {
        return self::query()->whereIn('id', $ids)->get();
    }

    /**
     * Get by type
     *
     * @param string $type
     * @return Collection
     */
    public function getByType(string $type): Collection
    {
        return self::query()->where('type', $type)->get();
    }

    /**
     * Permission profiles relation
     *
     * @return BelongsToMany
     */
    public function permissionProfiles(): BelongsToMany
    {
        return $this->belongsToMany(
            PermissionProfile::class,
            'document_item_permission_profile',
            'item_id',
            'profile_id'
        );
    }
}
