<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Employee;
use App\HrmSetting;
use App\Attendance;
use Auth;
use DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AttendanceController extends Controller
{
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('attendance')) {
            $lims_employee_list = Employee::where('is_active', true)->get();
            $lims_hrm_setting_data = HrmSetting::latest()->first();
            $general_setting = DB::table('general_settings')->latest()->first();
            if(Auth::user()->role_id > 2 && $general_setting->staff_access == 'own')
                $lims_attendance_all = Attendance::orderBy('id', 'desc')->where('user_id', Auth::id())->get();
            else
                $lims_attendance_all = Attendance::orderBy('id', 'desc')->get();
            return view('attendance.index', compact('lims_employee_list', 'lims_hrm_setting_data', 'lims_attendance_all'));
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
        $employee_id =  $data['employee_id'];
        $lims_hrm_setting_data = HrmSetting::latest()->first();
        $checkin = $lims_hrm_setting_data->checkin;
        foreach ($employee_id as $id) {
            $data['date'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['date'])));
            $data['user_id'] = Auth::id();
            $lims_attendance_data = Attendance::whereDate('date', $data['date'])->where('employee_id', $id)->first();
            if(!$lims_attendance_data){
                $data['employee_id'] = $id;
                $diff = strtotime($checkin) - strtotime($data['checkin']);
                if($diff >= 0)
                    $data['status'] = 1;
                else
                    $data['status'] = 0;
                Attendance::create($data);
            }
        }
        return redirect()->back()->with('message', 'Attendance created successfully');
        //return date('h:i:s a', strtotime($data['from_time']));
    }

    
    public function show($id)
    {
        //
    }

    
    public function edit($id)
    {
        //
    }

    
    public function update(Request $request, $id)
    {
        //
    }

    public function deleteBySelection(Request $request)
    {
        $attendance_id = $request['attendanceIdArray'];
        foreach ($attendance_id as $id) {
            $lims_attendance_data = Attendance::find($id);
            $lims_attendance_data->delete();
        }
        return 'Attendance deleted successfully!';
    }
    
    public function destroy($id)
    {
        $lims_attendance_data = Attendance::find($id);
        $lims_attendance_data->delete();
        return redirect()->back()->with('not_permitted', 'Attendance deleted successfully');
    }
}
