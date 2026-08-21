<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
	protected $fillable = ['menu_id', 'parent_id', 'menu_title', 'menu_link', 'menu_class', 'menu_target', 'link_type', 'dbname', 'custom_link'];


}
