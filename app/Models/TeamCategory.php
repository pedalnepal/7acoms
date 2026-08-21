<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamCategory extends Model
{
  use SoftDeletes;

  protected $fillable = ['title', 'permalink', 'detail', 'image', 'meta_title', 'meta_description', 'meta_keyword', 'meta_robot', 'status'];

    protected $dates = ['deleted_at'];
    public function media()
    {
      return $this->hasOne('App\Models\Media', 'id', 'image');
    }

}