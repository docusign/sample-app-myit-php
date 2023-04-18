<?php

namespace App\Models;

use App\Enums\DocumentItemTypesEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Laravel\Passport\Token;

/**
 * Class Employee
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $display_email
 * @property string $token_id
 * @property string $ext_id
 * @property string $site_id
 * @property int $permissionProfileId
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property PermissionProfile $permissionProfile
 * @property Collection $equipments
 * @property Collection $software
 * @property Collection $documentItems
 *
 * @package App\Models
 */
class Employee extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'display_email',
        'token_id',
        'ext_id',
        'site_id',
        'permission_profile_id',
    ];

    /**
     * Get by token
     *
     * @param Token $token
     * @return Collection
     */
    public function getByToken(Token $token): Collection
    {
        return $this->getByTokenId($token->id);
    }

    /**
     * Get by token ID
     *
     * @param string $tokenId
     * @return Collection
     */
    public function getByTokenId(string $tokenId): Collection
    {
        return self::query()
            ->with(['permissionProfile', 'equipments', 'software'])
            ->where('token_id', $tokenId)
            ->get();
    }

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
     * Get by ext ID
     *
     * @param string $extId
     * @return $this|null
     */
    public function getByExtId(string $extId): ?self
    {
        return self::query()->where('ext_id', $extId)->first();
    }

    /**
     * Permission profile relation
     *
     * @return BelongsTo
     */
    public function permissionProfile(): BelongsTo
    {
        return $this->belongsTo(PermissionProfile::class, 'permission_profile_id');
    }

    /**
     * Equipments relation
     *
     * @return BelongsToMany
     */
    public function equipments(): BelongsToMany
    {
        return $this->documentItems()->where('type', DocumentItemTypesEnum::EQUIPMENT);
    }

    /**
     * Software relation
     *
     * @return BelongsToMany
     */
    public function software(): BelongsToMany
    {
        return $this->documentItems()->where('type', DocumentItemTypesEnum::SOFTWARE);
    }

    /**
     * Document items relation
     *
     * @return BelongsToMany
     */
    public function documentItems(): BelongsToMany
    {
        return $this->belongsToMany(
            DocumentItem::class,
            'employee_document_item',
            'employee_id',
            'item_id'
        );
    }
}
