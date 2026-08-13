<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PipelineLog extends Model
{
    protected $table = 'pipeline_logs';

    protected $fillable = [
        'lead_id',
        'from_pipeline_id',
        'to_pipeline_id',
        'from_pipeline_name',
        'to_pipeline_name',
        'action',
        'created_by',
    ];


    public $timestamps = false; // karena tabel tidak pakai updated_at

    protected $dates = ['changed_at'];

    /** RELASI KE LEAD */
    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    /** USER YANG MENGUBAH PIPELINE */
    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
