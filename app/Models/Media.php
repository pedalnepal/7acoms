<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Storage;

class Media extends Model
{
    use SoftDeletes;

    protected $fillable = ['attachment_path', 'alt', 'type', 'original_file', 'folder_path', 'user_id', 'user_type'];


    protected $dates = ['deleted_at'];
    private $image_sizes = array(
        array('small_thumb', 100, 100),
        array('thumb', 200, 200),
        array('m_thumb', 320, 320),
        array('thumb_500', 500, 500),
        array('b_thumb', 450, 250),
        array('thumb_600x600', 600, 600),
        array('cat_thumb', 450, 450),
        array('g_thumb', 400, 400),
        array('team_thumb', 420, 550),
        array('pub_cat_thumb', 400, 500),
        array('cat_thumb_500_375', 500, 375),
        array('thumb_1200x1200', 1200, 1200, false)
    );
    //
    public static function get_all_attachment(){
        $images = self::orderBy('id', 'desc')->take(40)->get()->reverse()->values();
        $return_arr = array();
        foreach ($images as $key => $image) {
            $return_arr[] = array(
                    'name'  =>  $image->alt,
                    'size'  =>  $image->id,
                    'type'  =>  $image->type,
                    'file'  => asset($image->folder_path . $image->original_file),
                    // 'file'  =>  Storage::url($image->folder_path.$image->original_file)

            );
        }
        return json_encode($return_arr);
    }
    public function destroyAttachments($withOriginal=false)
    {
        $upload_folder = $this->folder_path;
        $images = @unserialize($this->attachment_path);
        // $regenerate_image = $this->original_file;
        if(is_array($images)){
                foreach ($images as $image_key => $image) {
                        $file = $this->folder_path.$image;
                        if (Storage::exists($file)) {
                                Storage::delete($file);
                        }
                }
        }
        if (Storage::exists($upload_folder.$this->original_file) && $withOriginal) {
            Storage::delete($upload_folder.$this->original_file);
        }
    }
    public function regenerate_thumbnails()
    {
        $upload_folder = $this->folder_path;
        $images = @unserialize($this->attachment_path);
        $regenerate_image = $this->original_file;
        if (Storage::mimeType($upload_folder.$regenerate_image)=='image/svg+xml') {
            return true;
        }
        if (Storage::exists($upload_folder.$regenerate_image)) {
            if(is_array($images)){
                foreach ($images as $image_key => $image) {
                    $file = $this->folder_path.$image;
                    if (Storage::exists($file)) {
                        Storage::delete($file);
                    }
                }
            }
            $attachment_path = $regenerate_image;
            $attachment_paths = array();
            foreach ($this->image_sizes as $key => $value) {
                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                $img = $manager->read(Storage::get($upload_folder.$attachment_path));
                if(!isset($value[3])){
                    $value[3] = 1;
                }elseif($value[3]==true){
                    $value[3] = 1;
                }
                $explode = explode('.', $attachment_path); // split all parts
                $end = '';
                $begin = '';
                if(count($explode) > 0){
                    $end = array_pop($explode); // removes the last element, and returns it
                    if(count($explode) > 0){
                        $begin = implode('.', $explode); // glue the remaining pieces back together
                    }
                }
                $att_name = $begin.'-'.$value[1].'x'.$value[2].'.'.$end;
                if($value[3]){
                    $img = $img->cover($value[1], $value[2]);
                }else{
                    $img = $img->scaleDown($value[1], $value[2]);
                }
                $imgen = $img->encode();
                                Storage::put($upload_folder .$att_name,$imgen);
                $attachment_paths[$value[0]] =  $att_name;
            }
            $this->attachment_path = serialize($attachment_paths);
            $this->save();
        }else{
            return 'Source File does not Exist';
        }
    }
    // public function get_attachment($size='full')
    // {
    //     $paths  = @unserialize($this->attachment_path);
    //     if (isset($paths[$size])) {
    //         $path   = $paths[$size];
    //     }else{
    //         $path   = $this->original_file;
    //     }
    //     if (Storage::exists($this->folder_path. $path) && $size!='full') {
    //         return '<img src="'.Storage::url($this->folder_path . $path).'" alt="'.$this->alt.'" loading="lazy">';
    //                 }elseif (Storage::disk('local')->exists($this->folder_path. $path) && $size!='full') {
    //                         return '<img src="'.Storage::url($this->folder_path . $path).'" alt="'.$this->alt.'" loading="lazy">';
    //                 }else{
    //         return '<img src="'.Storage::url($this->folder_path . $this->original_file).'" alt="'.$this->alt.'" loading="lazy">';
    //     }
    // }

    public function get_attachment_url($size = 'full')
    {
        $paths = @unserialize($this->attachment_path);
        $path = isset($paths[$size]) ? $paths[$size] : $this->original_file;

        $fullPath = public_path($this->folder_path . $path);

        if (file_exists($fullPath)) {
            return asset($this->folder_path . $path);
        }

        return asset($this->folder_path . $this->original_file);
    }

    // public function get_attachment_url($size='full')
    // {
    //     $paths  = @unserialize($this->attachment_path);
    //     if (isset($paths[$size])) {
    //         $path   = $paths[$size];
    //     }else{
    //         $path   = $this->original_file;
    //     }
    //     if (Storage::exists($this->folder_path. $path)  && $size!='full') {
    //         return Storage::url($this->folder_path. $path);
    //     }elseif (Storage::disk('local')->exists($this->folder_path. $path) && $size!='full') {
    //                 return Storage::url($this->folder_path. $path);
    //             }
    //             else{
    //         return Storage::url($this->folder_path. $this->original_file);
    //     }
    // }

    public function get_attachment($size = 'full')
    {
        $paths = @unserialize($this->attachment_path);
        $path = isset($paths[$size]) ? $paths[$size] : $this->original_file;

        $fullPath = public_path($this->folder_path . $path);

        if (file_exists($fullPath)) {
            return '<img src="' . asset($this->folder_path . $path) . '" alt="' . e($this->alt) . '" loading="lazy">';
        }

        return '<img src="' . asset($this->folder_path . $this->original_file) . '" alt="' . e($this->alt) . '" loading="lazy">';
    }

}
