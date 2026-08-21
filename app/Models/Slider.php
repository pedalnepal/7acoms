<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slider extends Model
{
    //
    use SoftDeletes;

	protected $fillable = ['title', 'caption', 'image', 'status', 'theme', 'link_title', 'links'];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected $dates = ['deleted_at'];
    public function media()
    {
    	return $this->hasOne('App\Models\Media', 'id', 'image');
    }
}
