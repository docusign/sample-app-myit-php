<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class Envelope
 *
 * @property int $id
 * @property string $ext_id
 * @property string $batch_id
 * @property string $token_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Envelope extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ext_id',
        'batch_id',
        'token_id',
    ];

    /**
     * Get by ext ID
     *
     * @param string $extId
     * @return $this|null
     */
    public function getByExtId(string $extId): ?self
    {
        return self::query()
            ->where('ext_id', $extId)
            ->first();
    }

    /**
     * Get by tokenID
     *
     * @param string $tokenId
     * @return Collection
     */
    public function getByTokenId(string $tokenId): Collection
    {
        return self::query()
            ->where('token_id', $tokenId)
            ->get();
    }

    /**
     * Get envelopes without ext ID
     *
     * @return Collection
     */
    public function getWithoutExtId(): Collection
    {
        return self::query()
            ->whereNull('ext_id')
            ->get();
    }
}
