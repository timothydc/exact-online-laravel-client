<?php
declare(strict_types=1);

namespace PolarisDC\Exact\ExactOnlineConnector\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ApiToken
 *
 * @property int $id
 * @property string $client_id
 * @property string|null $access_token
 * @property string|null $refresh_token
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $expires_in
 * @method static Builder|ApiToken newModelQuery()
 * @method static Builder|ApiToken newQuery()
 * @method static Builder|ApiToken query()
 * @method static Builder|ApiToken whereAccessToken($value)
 * @method static Builder|ApiToken whereClientId($value)
 * @method static Builder|ApiToken whereCreatedAt($value)
 * @method static Builder|ApiToken whereExpiresAt($value)
 * @method static Builder|ApiToken whereId($value)
 * @method static Builder|ApiToken whereRefreshToken($value)
 * @method static Builder|ApiToken whereUpdatedAt($value)
 */

class ApiToken extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'api_tokens';

    protected $dates = [
        'expires_at',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function getExpiresInAttribute(){
        return $this->expires_at->timestamp;
    }

    public function setExpiresInAttribute($value){
        $this->attributes['expires_at'] = Carbon::createFromTimestamp($value);
    }
}
