<?php

namespace PsychoB\WebHook\Models;

use PsychoB\WebHook\Services\UuidService;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ReceivedRequest
 *
 * @property string uuid
 * @property string data
 * @property string payload_uuid
 * @property string request_uuid
 * @property \DateTime created_at
 * @property \DateTime updated_at
 */
class ReceivedRequest extends Model
{
    protected $table = 'webhook_test_receive';
    protected $dates = [
        'created_at', 'updated_at',
    ];
    protected $primaryKey = 'uuid';
    protected $fillable = [
        'uuid', 'data', 'payload_uuid', 'request_uuid'
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