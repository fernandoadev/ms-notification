<?php

namespace App\Models;

use App\Enums\ChannelEnum;
use App\Enums\CommunicationStatusEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Communication extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /** @var string */
    protected $table = 'communications';

    /** @var bool */
    public $incrementing = false;

    /** @var string */
    protected $keyType = 'string';

    /** @var array */
    protected $fillable = [
        'recipient',
        'channel',
        'subject',
        'message',
        'origin_system',
        'status',
        'attempts',
        'processed_at',
        'failed_at',
    ];

    protected $casts = [
        'channel' => ChannelEnum::class,
        'status' => CommunicationStatusEnum::class,
        'processed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function logs()
    {
        return $this->hasMany(CommunicationLog::class);
    }
}
