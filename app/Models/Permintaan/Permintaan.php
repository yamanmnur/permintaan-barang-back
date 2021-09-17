<?php

namespace App\Models\Permintaan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
class Permintaan extends Model
{
    
    use SoftDeletes;

    protected $table = 'dat_permintaan';
    
    protected $fillable = [ 
       'id',
       'kode',
       'id_user',
       'tanggal_permintaan',
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

    public function detailPermintaan() {
        return $this->hasMany('App\Models\Permintaan\DetailPermintaan','id_permintaan')->orderBy('updated_at', 'desc');
    }
  
}
