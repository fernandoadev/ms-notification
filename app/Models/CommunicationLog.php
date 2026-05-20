<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommunicationLog extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /** @var string */
    protected $table = 'communication_logs';

    /** @var bool */
    public $incrementing = false;

    /** @var string */
    protected $keyType = 'string';

    /** @var array */
    protected $fillable = [
        'communication_id',
        'level',
        'message',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function communication()
    {
        return $this->belongsTo(Communication::class);
    }
}
