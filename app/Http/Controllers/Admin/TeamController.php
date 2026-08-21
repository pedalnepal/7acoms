<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (isset($_GET['trashed'])) {
            $teams = Team::orderBy('deleted_at', 'desc')->onlyTrashed()->paginate(100);
        }else{
            $teams = Team::orderBy('menu_order', 'desc')->paginate(100);
        }
        return view('admin.team.list', ['teams'=>$teams, 'title'=>'Team Members']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cats = \App\Models\TeamCategory::get();
        return view('admin.team.form', ['title'=>'Add Team Member', 'cats'=>$cats]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $team = new Team;
        $team->title            =   $request->title;
        $team->permalink        =   generate_permalink($team, $request->permalink ? Str::slug($request->permalink) : Str::slug($request->title));
        $team->team_category_id =   $request->team_category_id;
        $team->detail           =   $request->detail;
        $team->image            =   $request->image;
        $team->designation     =       $request->designation;
        $team->email            =   $request->email;
        // unchecked checkboxes aren't posted at all, hence the explicit cast
        $team->show_on_leadership = $request->has('show_on_leadership') ? 1 : 0;
        $team->save();
        $team->menu_order     =       $team->id;
        $team->save();
        \Session::flash('success_message', 'Team Member Detail successfully Added.');
        return redirect()->route('team.edit', $team->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cats = \App\Models\TeamCategory::get();
        $team = Team::findorFail($id);
        return view('admin.team.form', ['team'=>$team, 'title' => 'Edit Team Member', 'cats'=>$cats]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $team = Team::find($id);
        $team->title            =   $request->title;
        $team->team_category_id =   $request->team_category_id;
        $team->permalink        =   generate_permalink($team, $request->permalink ? Str::slug($request->permalink) : Str::slug($request->title));
        $team->detail           =   $request->detail;
        $team->image            =   $request->image;
        $team->designation     =       $request->designation;
        $team->email            =   $request->email;
        // unchecked checkboxes aren't posted at all, hence the explicit cast
        $team->show_on_leadership = $request->has('show_on_leadership') ? 1 : 0;
        $team->save();
        \Session::flash('success_message', 'Team Member Detail successfully Updated.');
        return redirect()->route('team.edit', $team->id);
    }
    public function restore(Request $request, $id)
    {
        $team = Team::withTrashed()->find($id);
        if($team){
            $team->restore();
            \Session::flash('success_message', 'Team restored successfully.');
            return redirect(route('team.index').'?trashed');
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
        $team = Team::withTrashed()->find($id);
        if ($team->trashed()) {
            $team->forceDelete();

        \Session::flash('success_message', 'Team permanently deleted.');
        }else{
            $team->delete();
        \Session::flash('success_message', 'Team successfully moved to trash.');
        }
        return redirect(route('team.index'));
    }
    public function ajax(Request $request)
    {
        switch($request->action)
        {

            case 'change_order':
                change_order(new Team, $request->AMenuData);
            break;

            default:

            break;
        }
        die();
    }
}
