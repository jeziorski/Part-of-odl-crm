<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadContract;
use App\Models\LeadStatus;
use App\Models\User;
use App\Models\Reminder;
use App\Exports\UserExport;
use App\Exports\LeadExport;
use App\Exports\LeadContractExport;
use App\Exports\ReminderExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Exports\AllExport;


class StatsController extends Controller
{
    public function index(){
        $contracts = LeadContract::join('services','leadcontracts.service_id','=','services.id')->select('leadcontracts.*','services.service_name');
        $data['cstats'] = LeadContract::join('services','leadcontracts.service_id','=','services.id')->groupBy('service_name','tax_value')->join('taxes','services.tax_id','=','taxes.id')->selectRaw('services.service_name,taxes.tax_value,sum(leadcontracts.contract_value) as sum,sum(leadcontracts.costs) as sum_costs, min(leadcontracts.contract_value) as min, max(leadcontracts.contract_value) as max, avg(leadcontracts.contract_value) as avg, taxes.tax_value, count(leadcontracts.id) as cnt ')->orderByDesc('sum')->get();
        $data['contracts_by_year'] = LeadContract::select(DB::raw("(count(id)) as total_contracts"),DB::raw("(sum(contract_value)) as contracts_value"),DB::raw("(sum(costs)) as costs"), DB::raw("(DATE_FORMAT(created_at, '%Y')) as year"))
        ->orderBy('created_at')->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y')"))->get();
        $data['contracts_by_month'] = LeadContract::where( DB::raw('YEAR(created_at)'), '>=', date('Y')-1)->select(DB::raw("(count(id)) as total_contracts"),DB::raw("(sum(contract_value)) as contracts_value"), DB::raw("(sum(costs)) as costs"), DB::raw("(DATE_FORMAT(created_at, '%m-%Y')) as month_year"))
        ->orderBy('created_at')->groupBy(DB::raw("DATE_FORMAT(created_at, '%m-%Y')"))->get();
        $users = User::get();
        $reminders = Reminder::get();
        $data['leads'] = Lead::join('leadstatus','lead.lead_status_id', '=', 'leadstatus.id')->select('lead.*','leadstatus.lead_status_name')->get();
        $data['leads_by_year'] = Lead::select(DB::raw("(count(id)) as total_leads"),DB::raw("(DATE_FORMAT(created_at, '%Y')) as year"))->orderBy('created_at')->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y')"))->get();
        $data['leads_by_month'] = Lead::where( DB::raw('YEAR(created_at)'), '>=', date('Y')-1)->select(DB::raw("(count(id)) as total_leads"), DB::raw("(DATE_FORMAT(created_at, '%m-%Y')) as month_year"))->orderBy('created_at')->groupBy(DB::raw("DATE_FORMAT(created_at, '%m-%Y')"))->get();
        $data['contracts'] = $contracts;
        $data['users'] = $users;
        $data['reminders'] = $reminders;
        return view('stats',['data' => $data]);        
    }
    public function export($model){
        switch($model){
            case('user'):
                return Excel::download(new UserExport, 'users-export-'.date('Y-m-d H-i-s').'.xlsx');
                break;
            case('lead'):
                return Excel::download(new LeadExport, 'leads-export-'.date('Y-m-d H-i-s').'.xlsx');
                break;
            case('leadcontract'):
                return Excel::download(new LeadContractExport, 'contracts-export-'.date('Y-m-d H-i-s').'.xlsx');
                break;
            case('reminder'):
                return Excel::download(new ReminderExport, 'reminders-export-'.date('Y-m-d H-i-s').'.xlsx');
                break;
            case('all'):
                return Excel::download(new AllExport, 'All-export-'.date('Y-m-d H-i-s').'.xlsx');
                break;
            default:
                abort(404); 
            break;
        }
        
    }
}


