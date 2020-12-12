<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Department;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    
    public function index()
    {
        $lims_department_all = Department::where('is_active', true)->get();
        return view('department.index', compact('lims_department_all'));
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => [
                'max:255',
                    Rule::unique('departments')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
        ]);

        $data = $request->all();
        $data['is_active'] = true;
        Department::create($data);
        return redirect('departments')->with('message', 'Department created successfully');
    }
    
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'name' => [
                'max:255',
                Rule::unique('departments')->ignore($request->department_id)->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
        ]);
        
        $data = $request->all();
        $lims_department_data = Department::find($data['department_id']);
        $lims_department_data->update($data);
        return redirect('departments')->with('message', 'Department updated successfully');
    }

    public function deleteBySelection(Request $request)
    {
        $department_id = $request['departmentIdArray'];
        foreach ($department_id as $id) {
            $lims_department_data = Department::find($id);
            $lims_department_data->is_active = false;
            $lims_department_data->save();
        }
        return 'Department deleted successfully!';
    }

    public function destroy($id)
    {
        $lims_department_data = Department::find($id);
        $lims_department_data->is_active = false;
        $lims_department_data->save();
        return redirect('departments')->with('message', 'Department deleted successfully');
    }
}
