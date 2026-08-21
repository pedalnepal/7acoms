<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\TeamCategory;

class TeamCategoryController extends Controller
{
    public function index()
    {
        if (isset($_GET['trashed'])) {
            $cteams = TeamCategory::orderBy('deleted_at', 'desc')->onlyTrashed()->paginate(10);
        }else{
            // drag-sort is meaningless across pages, so keep them on one
            $cteams = TeamCategory::orderBy('menu_order', 'desc')->paginate(100);
        }
        return view('admin.cteam.list', ['cteams'=>$cteams, 'title'=>'Team Category List']);
    }
    public function create()
    {
        return view('admin.cteam.form', ['title'=>'Add Team Category']);
    }
    public function store(Request $request)
    {
        $cteam = new TeamCategory;
        $cteam->title            =   $request->title;
        $cteam->permalink        =   generate_permalink($cteam, $request->permalink ? Str::slug($request->permalink) : Str::slug($request->title));
        $cteam->detail           =   $request->detail;
        $cteam->image            =   $request->image;
        $cteam->meta_title       =   $request->meta_title;
        $cteam->meta_description =   $request->meta_description;
        $cteam->meta_keyword     =   $request->meta_keyword;
        $cteam->meta_robot       =   $request->meta_robot;
        $cteam->save();
        // seed the sort position so new categories land at the top, as Team does
        $cteam->menu_order       =   $cteam->id;
        $cteam->save();
        return redirect()->route('team-category.edit', $cteam->id);
    }
    public function show($id)
    {
        //
    }
    public function edit($id)
    {
        $cteam = TeamCategory::findorFail($id);
        return view('admin.cteam.form', ['cteam'=>$cteam, 'title' => 'Edit Team Category']);
    }
    public function update(Request $request, $id)
    {
        $cteam = TeamCategory::find($id);
        $cteam->title            =   $request->title;
        $cteam->permalink        =   generate_permalink($cteam, $request->permalink ? Str::slug($request->permalink) : Str::slug($request->title));
        $cteam->detail           =   $request->detail;
        $cteam->image            =   $request->image;
        $cteam->meta_title       =   $request->meta_title;
        $cteam->meta_description =   $request->meta_description;
        $cteam->meta_keyword     =   $request->meta_keyword;
        $cteam->meta_robot       =   $request->meta_robot;
        $cteam->save();
        \Session::flash('success_message', 'Team Category successfully Updated.');
        return redirect()->route('team-category.edit', $cteam->id);
    }
    public function restore(Request $request, $id)
    {
        $cteam = TeamCategory::withTrashed()->find($id);
        if($cteam){
            $cteam->restore();
            \Session::flash('success_message', 'Team Category restored successfully.');
            return redirect(route('team-category.index').'?trashed');
        }else{
            return abort(404);
        }
    }
    public function destroy($id)
    {
        $cteam = TeamCategory::withTrashed()->find($id);
        if ($cteam->trashed()) {
            $cteam->forceDelete();
            \Session::flash('success_message', 'Team Category permanently deleted.');
        }else{
            $cteam->delete();
            \Session::flash('success_message', 'Team Category successfully moved to trash.');
        }
        return redirect(route('team-category.index'));
    }
    public function ajax(Request $request)
    {
        switch($request->action)
        {

            case 'change_order':
                change_order(new TeamCategory, $request->AMenuData);
            break;

            default:

            break;
        }
        die();
    }
}