<?php

namespace App\Models\Permintaan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class DetailPermintaan extends Model
{
    
    use SoftDeletes;

    protected $table = 'dat_detail_permintaan';
    
    protected $fillable = [ 
       'id',
       'id_permintaan',
       'id_barang',
       'kuantiti',
       'status',
       'keterangan',
       'created_at',
       'updated_at',
       'deleted_at',
       'created_by',
       'updated_by',
       'deleted_by',
    ];
    public $timestamps = false;
    protected $primaryKey = 'id';
    public $incrementing = false;
    
    protected $keyType = 'string';
}
