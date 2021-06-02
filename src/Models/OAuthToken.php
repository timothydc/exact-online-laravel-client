<?php
declare(strict_types=1);

namespace PolarisDC\ExactOnline\ExactOnlineClient\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ApiToken
 *
 * @property int                             $id
 * @property string                          $client_id
 * @property string|null                     $access_token
 * @property string|null                     $refresh_token
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int                             $expires_in
 * @method static Builder|OAuthToken newModelQuery()
 * @method static Builder|OAuthToken newQuery()
 * @method static Builder|OAuthToken query()
 * @method static Builder|OAuthToken whereAccessToken($value)
 * @method static Builder|OAuthToken whereClientId($value)
 * @method static Builder|OAuthToken whereCreatedAt($value)
 * @method static Builder|OAuthToken whereExpiresAt($value)
 * @method static Builder|OAuthToken whereId($value)
 * @method static Builder|OAuthToken whereRefreshToken($value)
 * @method static Builder|OAuthToken whereUpdatedAt($value)
 */
class OAuthToken extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'oauth_tokens';

    protected $dates = [
        'expires_at',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function setExpiresInAttribute($value)
    {
        $this->attributes['expires_at'] = Carbon::createFromTimestamp($value);
        $this->attributes['expires_in'] = $value;
    }
}
