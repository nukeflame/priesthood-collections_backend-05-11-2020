<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Http\Resources\Setting\SettingCollection;
use App\Http\Resources\Setting\Setting as SettingResource;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = Setting::all();
        return new SettingCollection($settings);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->hasFile('siteLogoUrl')) {
            $setting = Setting::where('option_name', 'siteLogoUrl')->first();
            if (!empty($setting)) {
                $storage_path = 'public/assets/logo';
                //delete previous
                $exp  = explode('/storage/assets/logo', $setting->option_value);
                $dp_exp = $exp[1];
                Storage::delete($storage_path . $dp_exp);
                $path = $request->file('siteLogoUrl')->store($storage_path);
                $pathstorage = Storage::url($path);
                $url = env('APP_URL') . $pathstorage;
                $setting->option_value = $url;
                $setting->update();
                return  new SettingResource($setting);
            }
        }
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $r, $id)
    {
        $setting = new Setting();
        $optionVals = [];
        if ($r->siteUrl) {
            $setting = Setting::where('option_name', 'siteUrl')->first();
            $setting->option_value = $r->siteUrl;
            $setting->update();
            $optionVals[] = new SettingResource($setting);
        }
        if ($r->siteTitle) {
            $setting = Setting::where('option_name', 'siteTitle')->first();
            $setting->option_value = $r->siteTitle;
            $setting->update();
            $optionVals[] = new SettingResource($setting);
        }
        if ($r->siteTelephone) {
            $setting = Setting::where('option_name', 'siteTelephone')->first();
            $setting->option_value = $r->siteTelephone;
            $setting->update();
            $optionVals[] = new SettingResource($setting);
        }
        if ($r->siteEmail) {
            $setting = Setting::where('option_name', 'siteEmail')->first();
            $setting->option_value = $r->siteEmail;
            $setting->update();
            $optionVals[] = new SettingResource($setting);
        }
        if ($r->miniLogoUrl) {
            $setting = Setting::where('option_name', 'miniLogoUrl')->first();
            $setting->option_value = $r->miniLogoUrl;
            $setting->update();
            $optionVals[] = new SettingResource($setting);
        }
      
        if ($r->siteDescription) {
            $setting = Setting::where('option_name', 'siteDescription')->first();
            $setting->option_value = $r->siteDescription;
            $setting->update();
            $optionVals[] = new SettingResource($setting);
        }
        if ($r->adminEmail) {
            $setting = Setting::where('option_name', 'adminEmail')->first();
            $setting->option_value = $r->adminEmail;
            $setting->update();
            $optionVals[] = new SettingResource($setting);
        }
        if ($r->facebookUrl) {
            $setting = Setting::where('option_name', 'facebookUrl')->first();
            $setting->option_value = $r->facebookUrl;
            $setting->update();
            $optionVals[] = new SettingResource($setting);
        }
        if ($r->footerDescription) {
            $setting = Setting::where('option_name', 'footerDescription')->first();
            $setting->option_value = $r->footerDescription;
            $setting->update();
            $optionVals[] = new SettingResource($setting);
        }
        if ($r->pinterestUrl) {
            $setting = Setting::where('option_name', 'pinterestUrl')->first();
            $setting->option_value = $r->pinterestUrl;
            $setting->update();
            $optionVals[] = new SettingResource($setting);
        }
        if ($r->instagramUrl) {
            $setting = Setting::where('option_name', 'instagramUrl')->first();
            $setting->option_value = $r->instagramUrl;
            $setting->update();
            $optionVals[] = new SettingResource($setting);
        }

        return  response()->json($optionVals);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
