<?php

namespace PsychoB\WebHook\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Request
 *
 * @property string uuid
 * @property Payload payload
 * @property string payload_uuid
 * @property string request_url
 * @property string request_header
 * @property string request_body
 * @property integer response_status
 * @property string response_headers
 * @property string response_body
 * @property \DateTime created_at
 * @property \DateTime updated_at
 */
class Request extends Model
{
    protected $table = 'webhook_requests';
    protected $dates = [
        'created_at',
        'updated_at',
    ];
    protected $primaryKey = 'uuid';
    protected $fillable = [
        'uuid',
        'payload',
        'payload_uuid',
        'request_url',
        'request_headers',
        'request_body',
        'response_status',
        'response_headers',
        'response_body',
    ];

    public $incrementing = false;

    public function payload()
    {
        return $this->belongsTo(Payload::class, 'uuid', 'payload_uuid');
    }
}