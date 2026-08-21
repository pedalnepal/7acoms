<?php
namespace App\Http\Controllers\Admin;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Media as MediaModel;
use Spatie\Permission\Models\Role;

class MediaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage medias|delete medias');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $medias = new MediaModel;
        if (isset($_GET['trashed'])) {
            $medias = $medias->orderBy('deleted_at', 'desc')->onlyTrashed();
        }else{
            $medias = $medias->orderBy('id', 'desc');
        }
        if(isset($_GET['keyword']) && !empty($_GET['keyword'])){
            $medias = $medias->where('title', 'like', '%'.$_GET['keyword'].'%');
        }
        return view('admin.media.list', ['medias'=>$medias->paginate(10), 'title'=>'Media List']);
    }
    public function restore(Request $request, $id)
    {
        $media = MediaModel::withTrashed()->find($id);
        if($media){
            $media->restore();
            \Session::flash('success_message', 'Blog restored successfully.');
            return redirect(route('media.index').'?trashed');
        }else{
            return abort(404);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $media = MediaModel::withTrashed()->find($id);
        if ($media->trashed()) {
            $media->forceDelete();
            \Session::flash('success_message', 'Media permanently deleted.');
            return redirect(route('media.index').'?trashed');
        }else{
            $media->delete();
            \Session::flash('success_message', 'Media moved to trash.');
            return redirect(route('media.index'));
        }
    }
    public function action(Request $request)
    {
        if($request->hasFile('file'))
        {
            $file = $request->file('file');
            // $validator = Validator::make(array('file'=>$file), array('file' => 'image|required|max:5120'));

            $validator = Validator::make(
                ['file' => $file],
                [
                    'file' => 'required|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/svg+xml|max:5120'
                ]
            );


            if($validator->passes()){
                $media = new MediaModel;
                $destinationPath        =   'uploads/'.date("Y").'/'.date("m").'/';
                //$destinationPath        =   'uploads/2025/05/';
                $filename               =   $file->getClientOriginalName();
                $nfilename              =   Str::replaceLast('.'.$file->getClientOriginalExtension(), ' ', $filename);
                $media->original_file   =   Str::slug($nfilename, '-').'.'.$file->getClientOriginalExtension();
                $media->alt             =   $nfilename;
                $media->type            =   $file->getClientMimeType();
                $media->folder_path     =   $destinationPath;
                $media->save();

                $file_temp = public_path().'/'.$destinationPath.$media->original_file;
                if (file_exists($file_temp)) {
                    $media->original_file = Str::slug($nfilename, '-').'-'.$media->id.'.'.$file->getClientOriginalExtension();
                    $media->save();
                    $file->move($destinationPath,$media->original_file);
                }else{
                    $file->move($destinationPath,$media->original_file);
                }
                $media->regenerate_thumbnails();
                return json_encode(array('success_msg'=>'Upload Successfull.', 'media_id'=>$media->id, 'media_url'=>'/'.$destinationPath.$media->original_file));
            }else{
                $errors = $validator->errors();
                return json_encode(array('error_msg'=>implode('<br>', $errors->all())));
            }
        }else{
            return json_encode(array('error_msg'=>'No file selected.'));
        }
    }
    public function ajax(Request $request)
    {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
            if (isset($request->action)) {
                switch ($request->action) {
                    case 'load_more':
                        $images = MediaModel::Where('alt', 'like', '%' . $request->keyword . '%')->orderBy('id', 'desc')->skip($request->count)->take(12)->get();
                        $return_val = '';
                        foreach ($images as $key => $mediaItem) {
                            $return_val .='<div class="media-item" >
                        <input style="display: none;" type="radio" id="media_'.$mediaItem->id.'" name="smedia" value="'.$mediaItem->id.'" data-url="'.$mediaItem->get_attachment_url().'" data-alt="'.$mediaItem->alt.'">
                        <div class="media-inside">
                            <label for="media_'.$mediaItem->id.'" class="media-content">
                                '.$mediaItem->get_attachment().'
                            </label>
                            <div class="media-bottom">
                                <a href="javascript:void(0);" class="edit-btn media-send-to-edit" data-media="'.$mediaItem->id.'"></a>
                                <a href="javascript:void(0);" class="delete-btn media-send-to-trash" data-media="'.$mediaItem->id.'"></a>
                            </div>
                        </div>
                    </div>';
                        }
                        echo  $return_val;
                        break;
                        case 'trash_media':
                            $media = MediaModel::find($request->media_id);
                            if($media){
                                $media->delete();
                                echo json_encode(array('success_msg'=>'Media successfully moved to trash.'));
                            }else{
                                echo json_encode(array('error_msg'=>'Error while deleting media.'));
                            }

                        break;
                        case 'edit_media_with_filename':
                            $media = MediaModel::find($request->mediaid);
                            $media->alt = $request->main_media_title;
                            $media->save();
                            if (isset($request->main_media_file_name) && !empty($request->main_media_file_name)) {

                                    $newfilename = Str::slug($request->main_media_file_name);
                                    $unique_name = microtime();

                                    $info =  pathinfo(public_path($media->folder_path . $media->original_file));
                                    $ext = $info['extension'];

                                    if( !file_exists( public_path($media->folder_path . $newfilename.'.'.$ext ) ) ){
                                        rename(public_path($media->folder_path . $media->original_file), public_path($media->folder_path . $newfilename.'.'.$ext ));
                                        $media->original_file = $newfilename.'.'.$ext;
                                    }elseif( !file_exists( public_path($media->folder_path . $newfilename.'-'.$media->id.'.'.$ext ) ) ){
                                        rename(public_path($media->folder_path . $media->original_file), public_path($media->folder_path . $newfilename.'-'.$media->id.'.'.$ext ));
                                        $media->original_file = $newfilename.'-'.$media->id.'.'.$ext;
                                    }else{
                                        rename(public_path($media->folder_path . $media->original_file), public_path($media->folder_path . $newfilename.'-'.$media->id.'-'.$unique_name.'.'.$ext ));
                                        $media->original_file = $newfilename.'-'.$media->id.'-'.$unique_name.'.'.$ext;
                                    }
                                    $media->save();

                                    $media->regenerate_thumbnails();

                            }

                            return $media;

                        break;

                    default:
                        # code...
                        break;
                }
            }
        }
    }
}
