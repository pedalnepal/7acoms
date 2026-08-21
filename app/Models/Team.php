<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use SoftDeletes;

	protected $fillable = ['title', 'detail', 'image', 'designation', 'email'];

    protected $dates = ['deleted_at'];
    public function media()
    {
    	return $this->hasOne('App\Models\Media', 'id', 'image');
    }
}