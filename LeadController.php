<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadContract;
use App\Models\LeadStatus;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    public function index(){
        $data['leads'] = Lead::join('leadstatus','lead.lead_status_id', '=', 'leadstatus.id')->select('lead.*','leadstatus.lead_status_name')->orderBy('id')->paginate(10);
        $data['status'] = LeadStatus::get();
        return view('leads',['data' => $data]);
    }
    public function leadsByStatus($statusfilter){
        $data['leads'] = Lead::join('leadstatus','lead.lead_status_id', '=', 'leadstatus.id')->select('lead.*','leadstatus.lead_status_name')->where('lead_status_name',$statusfilter)->orderBy('lead_status_name')->paginate(10);
        $data['status'] = LeadStatus::get();
        $data['filter'] = $statusfilter;
        return view('leads',['data' => $data]);
    }

    public function newLeadIndex(){
        $data['leadstatus'] = LeadStatus::get();
        return view('newLead', ['data' => $data]);
    }

    public function newLead(Request $req){
        
       $req->validate(
           [
               'name'=>'required | string ',
               'phone'=>'required | min:9 | unique:lead',
               'email'=>'email | unique:lead',     
               'leadstatus'=>'required | integer'          
           ]
           );

           $lead = new Lead;
           $lead->name = $req->input('name');
           $lead->phone = $req->input('phone');
           $lead->email = $req->input('email');
           $lead->notes = $req->input('notes');
           $lead->lead_status_id = $req->input('leadstatus');
           $lead->author = Auth::user()->id;
           
           if($lead->save()){
            return redirect('/leads')->with('status', "New lead added!");
           }
           else{
            return back()->withInput()->with('status', "Something bad happened!");
           }
        
    }

    public function lead($id){
        $data['leads'] = Lead::findOrFail($id);
        $data['contracts'] = LeadContract::where('lead_id','=',$id)->get();
        $data['reminders'] = Reminder::where('lead_id','=',$id)->get();
        $data['leadstatus'] = LeadStatus::get();
        return view('lead', ['data' => $data]);
    }

    public function upLead(Request $req){
        $id = $req->input('id');
        Lead::where('id', $id)->update(['name'=>$req->input('name'), 'phone'=>$req->input('phone'), 'email'=>$req->input('email'),'notes'=>$req->input('notes'), 'lead_status_id'=>$req->input('leadstatus')]);
        return redirect('/lead/'.$id)->with('status', "Lead updated!");
    }

    public function delLead(Request $req){
        $id = $req->input('id');
        $lead = Lead::find($id);
        $lead->delete();        
        return redirect('/leads')->with('status', "Lead deleted succesfully! Contracts and reminders are still available in the app.");
    }
}
