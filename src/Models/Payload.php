<?php

namespace PsychoB\WebHook\Models;

use PsychoB\WebHook\Services\UuidService;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Payload
 *
 * @property string uuid
 * @property string status
 * @property array data
 * @property array request_headers
 * @property string request_method
 * @property string request_url
 * @property string user_agent
 * @property \DateTime created_at
 * @property \DateTime updated_at
 */
class Payload extends Model
{
    public const STATUS_OK = 'ok';
    public const STATUS_RETRY = 'retry';
    public const STATUS_FAILED = 'failed';
    public const STATUS_INITIALIZED = 'initialized';

    protected $table = 'webhook_payloads';
    protected $dates = [
        'created_at', 'updated_at',
    ];
    protected $primaryKey = 'uuid';
    protected $fillable = [
        'json', 'status', 'data', 'request_headers', 'request_method', 'request_url', 'user_agent',
    ];
    protected $casts = [
        'data' => 'array',
        'request_headers' => 'array',
    ];

    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        // automatically set transaction_id to new id
        static::creating(function (Model $model) {
            $model->{$model->getKeyName()} = UuidService::random();
        });
    }
}