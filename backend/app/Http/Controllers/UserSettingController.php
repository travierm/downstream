<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Theme;
use App\UserSpotifyToken;
use Illuminate\Http\Request;

class UserSettingController extends Controller
{ 
    private $tabs = [];
    public function __construct()
    {
        $user = Auth::user();
        $this->tabs[] = new GeneralTab();
        $this->tabs[] = new ThemesTab();
        $this->tabs[] = new SpotifyTab();
        $this->tabs[] = new PrivacyTab();
    }

    public function getSettingsPage(Request $request)
    {
        $tab = "general";
        if($request->input('tab')) {
            $tab = $request->input('tab');
        }

        $tab = $this->getTab($tab);

        if(method_exists($tab, 'getData')) {
            $data = $tab->getData(Auth::user());
        }else{
            $data = $tab->data;
        }
        
        $default = $tab->default;

        return view('user.settings', [
            'tab' => $tab->name,
            'data' => $data,
            'default' => $default
        ]);
    }

    public function postSettings(Request $request)
    {
        $tab = $this->getTab($request->tab);

        if(!$tab) {
            $tab = $this->getTab("general");
        }

        return $tab->process($request);

        //return redirect()->back();
    }

    private function getTab($name)
    {
        foreach($this->tabs as $tab) {
            if($tab->name == $name) {
                return $tab;
            }
        }

        return false;
    }
}

class Tab {
    //name of tab
    public $name;
    //default form values
    public $default;
    //data needed for page such as theme data;
    public $data = [
    ];

    public $user;
}

class GeneralTab extends Tab {
    public $name = "general";

    public $default = [
        'theme' => 'downstream-purple'
    ];

    public $data = [
    ];

    public function process(Request $request)
    {
        $user = $request->user();

        $message = "";
        if($request->input("user_display_name")) {
            $user->display_name = $request->input("user_display_name");
            $user->save();

            $message = "Changed name to " . $request->input("user_display_name");
        }

        if($request->input("user_password")) {
            $password = $request->input("user_password");
            $confirm = $request->input("user_password_confirm");

            if($password === $confirm) {
                $user->updatePassword($password);
                return redirect()->back()->with('success', "Password changed successfully!");
            }else{
                return redirect()->back()->with('error', "Password do not match!");
            }
        }
        
        return redirect()->back()->with('success', $message);
    }
}

class ThemesTab extends Tab {
    public $name = "themes";

    public $default = [
        'theme' => 'downstream_default'
    ];

    public $data = [
        'themes' => []
       
    ];

    public function __construct()
    {
        $this->data['themes'] = Theme::all();
    }

    public function process(Request $request)
    {
        if($request->theme) {
            $request->user()->setSetting('theme', $request->theme);
        }

        return redirect()->back();
    }
}

class SpotifyTab extends Tab {
    public $name = "spotify";

    public $default = [
    ];

    public $data = [
    ];
}


class PrivacyTab extends Tab {
    public $name = "privacy";

    public $default = [
        'theme' => 'downstream-purple'
    ];

    public $data = [
    ];

    public function getData($user)
    {
        $this->data['account_open'] = (@$user->private ? "no" : "yes");

        return $this->data;
    }

    public function process(Request $request)
    {
        $private = $request->input("privacy_private_account");

        if($private == "no") {
            $request->user()->private = true;
        }else{
            $request->user()->private = false;
        }

        $request->user()->save();
        
        $private = ucfirst($private);

        return redirect()->back()->with('success', "Your privacy option has been updated to $private");
    }
}

