<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Slider;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (isset($_GET['trashed'])) {
            $sliders = Slider::orderBy('deleted_at', 'desc')->onlyTrashed()->paginate(10);
        }else{
            $sliders = Slider::orderBy('menu_order', 'desc')->paginate(10);
        }
        return view('admin.slider.list', ['sliders'=>$sliders, 'title'=>'Slider List']);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.slider.form', ['title'=>'Add Slider']);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $slider = new Slider;
        $slider->fill($request->all())->save();
        $slider->menu_order = $slider->id;
        $slider->save();
        return redirect()->route('slider.edit', $slider->id);
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
        $slider = Slider::find($id);
        return view('admin.slider.form', ['slider'=>$slider, 'title' => 'Edit Slider']);
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
        $slider = Slider::findOrFail($id);
        $slider->fill($request->all())->save();
        return redirect()->route('slider.edit', $slider->id);
    }
    public function restore(Request $request, $id)
    {
        $slider = Slider::withTrashed()->find($id);
        if($slider){
            $slider->restore();
            \Session::flash('success_message', 'Slider restored successfully.');
            return redirect(route('slider.index').'?trashed');
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
         $slider = Slider::withTrashed()->find($id);
         if ($slider->trashed()) {
             $slider->forceDelete();
             \Session::flash('success_message', 'Slider deleted permanently.');
             return redirect(route('slider.index').'?trashed');
         }else{
             $slider->delete();
             \Session::flash('success_message', 'Slider moved to trash.');
             return redirect(route('slider.index'));
         }
     }
     public function ajax(Request $request)
     {
         switch($request->action)
         {

             case 'change_order':
                 change_order(new Slider, $request->AMenuData);
             break;

             default:

             break;
         }
         die('t');
     }
 }
