<?php

namespace App\Http\Controllers;

use Cache;
use Illuminate\Http\Request;

class AdminController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  private $defaultSettings = [
  	'showLatestVideos' => 'yes'
  ];

  public function index()
  {
  	$settings = $this->getSettings();

    return view('admin.index', [
    	'settings' => $settings
    ]);
  }

  public function postServerSettings(Request $request)
  {
  	$this->updateSettings($request);
  
  	return redirect('/admin/dash');
  }

  private function updateSettings($request)
  {
  	foreach($this->defaultSettings as $key => $default) {
  		$newValue = $request->input('settings_' . $key);
  		$cacheValue = Cache::get($key);

  		if($newValue !== $cacheValue) {

  			//only update on new value
  			Cache::forever($key, $newValue);
  		}
  	}
  }

  // Make sure cache is synced with default settings
  // does not overrite existings settings
  private function cacheSync($settings) {
  	$updated = [];
  	foreach($settings as $name => $value) {
  		$cacheValue = Cache::get($name);
  		if(!$cacheValue) {
  			//setting default not in cache
  			
  			//store admin settings forever
  			//dd("here");
  			Cache::forever($name, $value);
  			$updated[$name] = $value;
  		}else{
  			$updated[$name] = $cacheValue;
  		}
  	}

  	return $updated;
  }

  private function getSettings()
  {
  	$settings = $this->defaultSettings;

  	return $this->cacheSync($settings);
  }
}
