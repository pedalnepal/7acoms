<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Page as PageModel;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (isset($_GET['trashed'])) {
            $pages = PageModel::orderBy('deleted_at', 'desc')->onlyTrashed()->paginate(10);
        }else{
            $pages = PageModel::orderBy('id', 'desc')->paginate(10);
        }
        return view('admin.page.list', ['pages'=>$pages, 'title'=>'Pages List']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.page.form', ['title'=>'Add Page']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $page = new PageModel;
        $page->title            =   $request->title;
        $page->caption          =   $request->caption;
        $page->permalink        =   generate_permalink($page, $request->permalink ? Str::slug($request->permalink) : Str::slug($request->title));
        $page->detail           =   $request->detail;
        $page->image            =   $request->image;
        $page->meta_title       =   $request->meta_title;
        $page->meta_description =   $request->meta_description;
        $page->meta_keyword =   $request->meta_keyword;
        $page->meta_robot       =   $request->meta_robot;
        $page->save();
        \Session::flash('success_message', 'Page successfully Created.');
        return redirect()->route('page.edit', $page->id);
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
        $page = PageModel::findorFail($id);
        //print_r($page);
        return view('admin.page.form', ['page'=>$page, 'title' => 'Edit Page']);
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

        $page = PageModel::find($id);

        $page->title            =   $request->title;
        $page->caption          =   $request->caption;
        $page->permalink        =   generate_permalink($page, $request->permalink ? Str::slug($request->permalink) : Str::slug($request->title));
        $page->detail           =   $request->detail;
        $page->image            =   $request->image;
        $page->meta_title       =   $request->meta_title;
        $page->meta_description =   $request->meta_description;
        $page->meta_keyword =   $request->meta_keyword;
        $page->meta_robot       =   $request->meta_robot;
        $page->save();
        \Session::flash('success_message', 'Page successfully Updated.');
        return redirect()->route('page.edit', $page->id);
    }
    public function restore(Request $request, $id)
    {

        $page = PageModel::withTrashed()->find($id);
        if($page){
            $page->restore();
            \Session::flash('success_message', 'Page restored successfully.');
            return redirect(route('page.index').'?trashed');
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
        $page = PageModel::withTrashed()->find($id);
        if ($page->trashed()) {
            $page->forceDelete();

        \Session::flash('success_message', 'Page permanently deleted.');
        }else{
            $page->delete();
        \Session::flash('success_message', 'Page successfully moved to trash.');
        }
        return redirect(route('page.index'));
    }
}
