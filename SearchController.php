<?php

namespace App\Http\Controllers;
use App\Models\Reminder;
use App\Models\User;
use App\Models\Lead;
use App\Models\LeadContract;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(){        
        return view('search');
    } 
    public function search(Request $req){
        $req->validate(
            [
                'search'=>'required '                             
            ]
            );
        $query = $req->input('search');
        $data['users'] = User::where( 'name', 'LIKE', '%' . $query . '%')->orWhere ('email', 'LIKE', '%' . $query . '%')->get();
        $data['leads'] = Lead::where( 'name', 'LIKE', '%' . $query . '%')->orWhere ('email', 'LIKE', '%' . $query . '%')->orWhere ('notes', 'LIKE', '%' . $query . '%')->get();
        $data['contracts'] = LeadContract::where('name', 'LIKE', '%' . $query . '%')->orWhere ('notes', 'LIKE', '%' . $query . '%')->get();
        $data['reminders'] = Reminder::where('name', 'LIKE', '%' . $query . '%')->orWhere ('notes', 'LIKE', '%' . $query . '%')->get();
        return view('search',['data' => $data]);
    }    
}
