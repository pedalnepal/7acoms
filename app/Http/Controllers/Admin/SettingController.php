<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Setting;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.setting.site', array('title'=>'Site Configuration'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $configurations = array(
            'logo'=>'Logo',
            'dark_logo'=>'Dark Logo',
            'facebook'=>'Facebook URL',
            'tiktok'=>'Tiktok URL',
            'linkedin'=>'Linkedin URL',
            'instagram' =>'Instagram',
            'youtube'=>'Youtube',
            'mobile'    =>  'Mobile No.',
            'sphone'   =>  'Secondary Phone',
            'location'=>'Location',
            'contact'=>'Contact No.',
            'pemail'=>'Primary Email',
            'semail'=>'Secondary Email',
            'working_hours'=>'Working Hours',
        );
        foreach ($configurations as $key => $value) {
            $setting = Setting::firstOrNew(array('name' => $key));
            $setting->value = $request->$key;
            $setting->save();
        }
         return redirect()->back()->with('success_message', 'Settings has been saved.');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        return view('admin.setting.home', array('title'=>'Home Configuration'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function home_store(Request $request)
    {
        $configurations = array(
            'home_welcome',
            'meta_title',
            'meta_description',
            'meta_keyword',
            'footer_text',
            'home_video',
        );
        foreach ($configurations as $key => $value) {
            $setting = Setting::firstOrNew(array('name' => $value));
            $setting->value = $request->$value;
            $setting->save();
        }
         return redirect()->back()->with('success_message', 'Settings has been saved.');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function before_after()
    {
        return view('admin.setting.before_after', array('title'=>'Before After'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function before_after_store(Request $request)
    {
        $setting = Setting::firstOrNew(array('name' => 'before_after'));
        $setting->value = @serialize($request->before_after);
        $setting->save();

         return redirect()->back()->with('success_message', 'Image has been saved.');
    }

}
