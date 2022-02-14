<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Reminder;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReminderController extends Controller
{
    public function index(){
        $reminders = Reminder::where('author','=',Auth::user()->id)->orwhere('assigned_to','=',Auth::user()->id)->orderByDesc('status')->paginate(10);
        $data['reminders'] = $reminders;
        return view('reminders',['data' => $data]);
    }
    public function remindersByStatus($reminderstatus){
        $reminders = Reminder::where([
            ['author','=',Auth::user()->id],
            ['status','=',$reminderstatus]
            ])->orwhere([
                ['assigned_to','=',Auth::user()->id],
                ['status','=',$reminderstatus]
            ])->paginate(10);
        $data['reminders'] = $reminders;
        $data['filter'] = $reminderstatus;
        return view('reminders',['data' => $data]);
    }

    public function newReminder(){
        $data['leads'] = Lead::get();
        $data['users'] = User::get();
        return view('newReminder',['data' => $data]);
    }

    public function reminder($id){
        $data['reminders'] = Reminder::findOrFail($id);
        $data['leads'] = Lead::get();
        $data['users'] = User::get();
        return view('newReminder', ['data' => $data]);
    }
    
    public function addReminder(Request $req){
        $req->validate(
            [
                'name'=>'required ',
                'date'=>'required',
                'time'=>'required',
                'notes'=>'required',
                'lead_id' => 'integer | nullable | '               
            ]
            );
        
            $reminder = new Reminder();
            $reminder->name = $req->input('name');
            $reminder->date = $req->input('date');            
            $reminder->time = $req->input('time');            
            $reminder->notes = $req->input('notes');
            $reminder->lead_id = $req->input('lead_id');
            $reminder->assigned_to = $req->input('assigned_to');
            $reminder->status = 'new';
            $reminder->author = Auth::user()->id;
            
            if($reminder->save()){
                return redirect('/reminders')->with('status', "New reminder added!");
            }
            else{
                return back()->withInput()->with('status', "Something bad happened!");
            }
        
    }
    public function upReminder(Request $req){
        $req->validate(
            [
                'name'=>'required ',
                'date'=>'required',
                'status' => 'required',Rule::in(['inprogress', 'done','new']),
                'time'=>'required',
                'notes'=>'required',
                'lead_id' => 'nullable | integer '               
            ]
            );
        Reminder::where('id', $req->input('id'))->update([
            'name'=>$req->input('name'),
            'notes'=>$req->input('notes'),
            'date'=>$req->input('date'),
            'lead_id'=>$req->input('lead_id'),
            'status'=>$req->input('status'),
            'assigned_to'=>$req->input('assigned_to'),
            'time' =>$req->input('time')]);
        return redirect('/reminders')->with('status', "Reminder updated!");
    }
    public function reminderStatus(Request $req){
        $req->validate(
            [
                'id' => 'required',
                'status' => 'required',Rule::in(['inprogress', 'done'])
            ]
            );
        Reminder::where('id', $req->input('id'))->update(['status'=>$req->input('status')]);
        return redirect('/reminders')->with('status', "Reminder updated!");
    }
    public function delReminder(Request $req){
        $id = $req->input('id');
        $contract = Reminder::findOrFail($id);
        $contract->delete();        
        return redirect('/reminders')->with('status', "Reminder deleted succesfully!");
    }
}
