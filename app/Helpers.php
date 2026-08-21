<?php
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

if (!function_exists('get_attachment')) {
	function get_attachment($media_id=null, $size='full')
	{
        $media = \App\Models\Media::find($media_id);
		if($media){
			return $media->get_attachment($size);
    }
        return '';
	}
}
if (!function_exists('get_attachment_url')) {
	function get_attachment_url($media_id=null, $size='full')
	{
        $media = \App\Models\Media::find($media_id);
        if($media){
            return $media->get_attachment_url($size);
        }
	}
}
if (!function_exists('generate_permalink')) {
    function generate_permalink($modelObj, $permalink)
    {
        $new_permalink = $permalink;
        if (empty($permalink)) {
          return Str::slug(Str::random(40));
        }else{
          if($modelObj->where('permalink', $permalink)->withTrashed()->where('id', '!=', $modelObj->id)->first()){
              for ($i=1; $i < 1000; $i++) {
                  $new_permalink = $permalink.'-'.$i;
                  if (!$modelObj->where('permalink', $new_permalink)->withTrashed()->where('id', '!=', $modelObj->id)->first()) {
                      return Str::slug($new_permalink);
                  }
              }
          }
          return Str::slug($new_permalink);
        }
    }
}
if (!function_exists('change_order')) {
    function change_order($object, $menudatas){
        $array_menu_data    = array();
        $menudatas          = json_decode($menudatas);
        foreach ($menudatas as $key => $value) {
            $menuitem = $object->find($value->order);
            $array_menu_data[$menuitem->menu_order]   =   $value->order;
        }
        krsort($array_menu_data);
        $jk=0;
        foreach ($array_menu_data as $key => $value) {
            $menuitem = $object->find($menudatas[$jk]->order);
            $menuitem->menu_order = $key;
            $menuitem->save();
            $jk++;
        }
    }
}
