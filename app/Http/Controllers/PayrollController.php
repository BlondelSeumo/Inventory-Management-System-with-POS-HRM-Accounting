<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Account;
use App\Employee;
use App\Payroll;
use Auth;
use DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Mail\UserNotification;
use Illuminate\Support\Facades\Mail;


class PayrollController extends Controller
{
    
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('payroll')){
            $lims_account_list = Account::where('is_active', true)->get();
            $lims_employee_list = Employee::where('is_active', true)->get();
            $general_setting = DB::table('general_settings')->latest()->first();
            if(Auth::user()->role_id > 2 && $general_setting->staff_access == 'own')
                $lims_payroll_all = Payroll::orderBy('id', 'desc')->where('user_id', Auth::id())->get();
            else
                $lims_payroll_all = Payroll::orderBy('id', 'desc')->get();

            return view('payroll.index', compact('lims_account_list', 'lims_employee_list', 'lims_payroll_all'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['reference_no'] = 'payroll-' . date("Ymd") . '-'. date("his");
        $data['user_id'] = Auth::id();
        Payroll::create($data);
        $message = 'Payroll creared succesfully';
        //collecting mail data
        $lims_employee_data = Employee::find($data['employee_id']);
        $mail_data['reference_no'] = $data['reference_no'];
        $mail_data['amount'] = $data['amount'];
        $mail_data['name'] = $lims_employee_data->name;
        $mail_data['email'] = $lims_employee_data->email;
        try{
            Mail::send( 'mail.payroll_details', $mail_data, function( $message ) use ($mail_data)
            {
                $message->to( $mail_data['email'] )->subject( 'Payroll Details' );
            });
        }
        catch(\Exception $e){
            $message = ' Payroll created successfully. Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
        }

        return redirect('payroll')->with('message', $message);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $lims_payroll_data = Payroll::find($data['payroll_id']);
        $lims_payroll_data->update($data);
        return redirect('payroll')->with('message', 'Payroll updated succesfully');
    }

    public function deleteBySelection(Request $request)
    {
        $payroll_id = $request['payrollIdArray'];
        foreach ($payroll_id as $id) {
            $lims_payroll_data = Payroll::find($id);
            $lims_payroll_data->delete();
        }
        return 'Payroll deleted successfully!';
    }

    public function destroy($id)
    {
        $lims_payroll_data = Payroll::find($id);
        $lims_payroll_data->delete();
        return redirect('payroll')->with('not_permitted', 'Payroll deleted succesfully');
    }
}
