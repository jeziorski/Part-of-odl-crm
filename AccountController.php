<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Reminder;
use App\Models\User;
use App\Models\LeadContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    public function index(){
        $id = Auth::user()->id;
        $leads = Lead::get();
        $contracts = LeadContract::where('author','=',$id)->get();              
        $reminders = Reminder::where('author','=',$id)->orWhere('assigned_to','=',$id)->get();           
        $data['leads'] = $leads;
        $data['contracts'] = $contracts;        
        $data['reminders'] = $reminders;
        $data['session'] = session()->all();
        return view('account',['data' => $data]);
    }

    public function user($id){
        $leads = Lead::where('author','=',$id)->get();
        $contracts = LeadContract::where('author','=',$id)->get();  
        $reminders = Reminder::where('author','=',$id)->orWhere('assigned_to','=',$id)->get();
        $data['users'] = User::findOrFail($id);           
        $data['leads'] = $leads;        
        $data['contracts'] = $contracts;
        $data['reminders'] = $reminders;

        return view('account',['data' => $data]);
    }

    public function upuser(Request $req){
        $id = Auth::user()->id;
        $user = User::find($id);
        $req->validate([
            'new_password' => 'required'
        ]);
        $user->update(['password'=>Hash::make($req->new_password)]);
        return redirect('/account')->with('status', "Your password has been changed!");
    }
    public function personalize(Request $req){
        $id = Auth::user()->id;
        $user = User::find($id);
        $theme = $req->theme;
        if(isset($theme) AND $theme == 'dark'){}else{$theme='light';}
        $req->validate([
            'lang' => 'required'
        ]);
        $user->update(['theme'=>$theme,'lang'=>$req->lang]);
        return redirect('/account')->with('status', "Personalization updated!");
    }
}
