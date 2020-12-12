<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Holiday;
use Auth;
use User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Mail;

class HolidayController extends Controller
{
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('holiday')) {
            $approve_permission = true;
            $lims_holiday_list = Holiday::orderBy('id', 'desc')->get();
        }
        else {
            $approve_permission = false;
            $lims_holiday_list = Holiday::where('user_id', Auth::id())->orderBy('id', 'desc')->get();
        }

        return view('holiday.index', compact('lims_holiday_list', 'approve_permission'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = [
            'from_date'   => date("Y-m-d", strtotime(str_replace("/", "-", $request->input('from_date')))),
            'to_date'     => date("Y-m-d", strtotime(str_replace("/", "-", $request->input('to_date')))),
            'user_id'     => Auth::id(),
            'note'        => $request->input('note')
        ];
        
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('holiday')) {
            $data['is_approved'] = true;
        }
        else{
            $data['is_approved'] = false;
        }
        Holiday::create($data);
        return redirect()->back()->with('message', "Holiday created successfully");
    }

    public function show($id)
    {
        //
    }

    public function approveHoliday($id)
    {
        $holiday = Holiday::find($id);
        $holiday->is_approved = true;
        $holiday->save();
        
        $mail_data['name'] = $holiday->user->name;
        $mail_data['email'] = $holiday->user->email;
        
        try {
            Mail::send( 'mail.holiday_approve', $mail_data, function( $message ) use ($mail_data)
            {
                $message->to( $mail_data['email'] )->subject( 'Holiday Approved' );
            });
        }
        catch(\Exception $e) {
            return 'Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
        }
    }

    public function myHoliday($year, $month)
    {
        $start = 1;
        $number_of_day = cal_days_in_month(CAL_GREGORIAN,$month,$year);
        while($start <= $number_of_day)
        {
            if($start < 10)
                $date = $year.'-'.$month.'-0'.$start;
            else
                $date = $year.'-'.$month.'-'.$start;
            $holiday_found = Holiday::whereDate('from_date','<=', $date)
                ->whereDate('to_date','>=', $date)
                ->where([
                    ['is_approved', true],
                    ['user_id', Auth::id()]
                ])->first();
            if($holiday_found) {
                $general_setting = \App\GeneralSetting::select('date_format')->latest()->first();
                $holidays[$start] = date($general_setting->date_format, strtotime($holiday_found->from_date)).' '.trans("file.To").' '.date($general_setting->date_format, strtotime($holiday_found->to_date));
            }
            else {
                $holidays[$start] = false;
            }
            $start++;
        }
        //return dd($holidays);
        $start_day = date('w', strtotime($year.'-'.$month.'-01')) + 1;
        $prev_year = date('Y', strtotime('-1 month', strtotime($year.'-'.$month.'-01')));
        $prev_month = date('m', strtotime('-1 month', strtotime($year.'-'.$month.'-01')));
        $next_year = date('Y', strtotime('+1 month', strtotime($year.'-'.$month.'-01')));
        $next_month = date('m', strtotime('+1 month', strtotime($year.'-'.$month.'-01')));
        return view('holiday.my_holiday', compact('start_day', 'year', 'month', 'number_of_day', 'prev_year', 'prev_month', 'next_year', 'next_month', 'holidays'));
    }

    public function update(Request $request, $id)
    {
        $holiday_data = Holiday::find($request->input('id'));
        $data = [
            'from_date'   => date("Y-m-d", strtotime(str_replace("/", "-", $request->input('from_date')))),
            'to_date'     => date("Y-m-d", strtotime(str_replace("/", "-", $request->input('to_date')))),
            'note'        => $request->input('note')
        ];
        $holiday_data->update($data);
        return redirect()->back()->with('message', "Holiday updated successfully");
    }

    public function deleteBySelection(Request $request)
    {
        $holiday_id = $request['holidayIdArray'];
        foreach ($holiday_id as $id) {
            $lims_holiday_data = Holiday::find($id);
            $lims_holiday_data->delete();
        }
        return 'Holiday deleted successfully!';
    }

    public function destroy($id)
    {
        Holiday::find($id)->delete();
        return redirect()->back()->with('not_prmitted', "Holiday deleted successfully");
    }
}
