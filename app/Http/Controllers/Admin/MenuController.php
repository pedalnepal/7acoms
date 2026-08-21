<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	$menu = Menu::paginate(10);
        return view('admin.menu.list', ['menus'=>$menu, 'title'=>'Menu List']);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id=null)
    {
        if ($id==null) {
            abort(404);
        }
        $menuobj = new Menu;
        $menucat = Menu::where('id', $id)->get()->first();
        if($menucat){
            return view('admin.menu.form', ['parent_menus' => MenuItem::where('menu_id', $id)->get(), 'title'=>'Add Menu Item', 'menucat'=>$menucat, 'alllinks'=>array(), 'parent_links'=>$menuobj->get_parent_links($id)]);
        }
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $menuitem = new MenuItem;
        $request->request->add(['menu_id' => $id]);
        $menuitem->fill($request->all())->save();

        $menuitem->menu_order = $menuitem->id;
        $menuitem->save();

        session()->flash('success', 'Menu item added successfully!');
        return redirect()->route('menu.edit', $menuitem->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        /*$menuuuuuus = MenuItem::get();
        foreach ($menuuuuuus as $key => $menuuuuuu) {
            $menuuuuuu->menu_order = $menuuuuuu->id;
            $menuuuuuu->save();
        }*/
    	$menu = Menu::where('id', $id)->get()->first();
        $menuobj = new Menu;
        $menus = $menuobj->menulists($id);
    	if($menu){
	    	return view('admin.menu.item', ['menus' => $menus, 'menu'=>$menu, 'title'=>'Menu Items']);
	    }
	    abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $menuobj = new Menu;
        $menuitem = MenuItem::find($id);
        $menucat = Menu::find($menuitem->menu_id);
        if ($menuitem->link_type==1) {
            $alllinks = array();
        }else{
           $alllinks = DB::table($menuitem->dbname)->get();
        }
        return view('admin.menu.form', ['menu'=>$menuitem, 'title' => 'Edit Menu', 'menucat'=>$menucat, 'parent_menus' => MenuItem::where('menu_id', $id)->get(), 'alllinks'=>$alllinks, 'parent_links'=>$menuobj->get_parent_links($menuitem->menu_id, $menuitem->parent_id, $id)]);
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
        $menuitem = MenuItem::findOrFail($id);
        $menuitem->fill($request->all())->save();

        session()->flash('success', 'Menu item updated successfully!');
        return redirect()->route('menu.edit', $menuitem->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        $menuitem = MenuItem::findOrFail($id);
        $menucat = $menuitem->menu_id;
        $menuitem->delete();

        session()->flash('success', 'Menu item deleted successfully!');
        return redirect()->route('menu.show', $menucat);
    }

    public function ajax(Request $request)
    {
        switch($request->action)
        {
            case 'list_menu':
            $results = DB::table($request->menu_from)->whereNull('deleted_at')->get();
            echo '<option value="">Select</option>';
            foreach($results as $result) {
                echo '<option value="'.$result->id.'">'.$result->title.'</option>';
            }
            break;

            case 'change_order':
                change_order(new MenuItem, $request->AMenuData);
            break;

            default:

            break;
        }
        die();
    }
}
