@extends('layout.main') @section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.Add Employee')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        {!! Form::open(['route' => 'employees.store', 'method' => 'post', 'files' => true]) !!}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.name')}} *</strong> </label>
                                    <input type="text" name="employee_name" required class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>{{trans('file.Image')}}</label>
                                    <input type="file" name="image" class="form-control">
                                    @if($errors->has('image'))
                                   <span>
                                       <strong>{{ $errors->first('image') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{trans('file.Department')}} *</label>
                                    <select class="form-control selectpicker" name="department_id" required>
                                        @foreach($lims_department_list as $department)
                                        <option value="{{$department->id}}">{{$department->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{trans('file.Email')}} *</label>
                                    <input type="email" name="email" placeholder="example@example.com" required class="form-control">
                                    @if($errors->has('email'))
                                   <span>
                                       <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>{{trans('file.Phone Number')}} *</label>
                                    <input type="text" name="phone_number" required class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>{{trans('file.Address')}}</label>
                                    <input type="text" name="address" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>{{trans('file.City')}}</label>
                                    <input type="text" name="city" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>{{trans('file.Country')}}</label>
                                    <input type="text" name="country" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mt-4">
                                    <label>{{trans('file.Add User')}}</label>
                                    <input type="checkbox" name="user" checked value="1" />
                                </div>
                                <div id="user-input" class="mt-4">
                                    <div class="form-group">
                                        <label>{{trans('file.UserName')}} *</label>
                                        <input type="text" name="name" required class="form-control">
                                        @if($errors->has('name'))
                                       <span>
                                           <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label>{{trans('file.Password')}} *</label>
                                        <input required type="text" name="password" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>{{trans('file.Role')}} *</label>
                                        <select name="role_id" class="selectpicker form-control">
                                            @foreach($lims_role_list as $role)
                                            <option value="{{$role->id}}">{{$role->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group" id="warehouse">
                                        <label>{{trans('file.Warehouse')}} *</label>
                                        <select name="warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Warehouse...">
                                            @foreach($lims_warehouse_list as $warehouse)
                                            <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group" id="biller">
                                        <label>{{trans('file.Biller')}} *</label>
                                        <select name="biller_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Biller...">
                                            @foreach($lims_biller_list as $biller)
                                            <option value="{{$biller->id}}">{{$biller->name}} ({{$biller->company_name}})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mt-4">
                                    <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    $("ul#hrm").siblings('a').attr('aria-expanded','true');
    $("ul#hrm").addClass("show");
    $("ul#hrm #employee-menu").addClass("active");

    $('#warehouse').hide();
    $('#biller').hide();

    $('input[name="user"]').on('change', function() {
        if ($(this).is(':checked')) {
            $('#user-input').show(400);
            $('input[name="name"]').prop('required',true);
            $('input[name="password"]').prop('required',true);
            $('select[name="role_id"]').prop('required',true);
        }
        else{
            $('#user-input').hide(400);
            $('input[name="name"]').prop('required',false);
            $('input[name="password"]').prop('required',false);
            $('select[name="role_id"]').prop('required',false);
            $('select[name="warehouse_id"]').prop('required',false);
            $('select[name="biller_id"]').prop('required',false);
        }
    });

    $('select[name="role_id"]').on('change', function() {
        if($(this).val() > 2){
            $('#warehouse').show(400);
            $('#biller').show(400);
            $('select[name="warehouse_id"]').prop('required',true);
            $('select[name="biller_id"]').prop('required',true);
        }
        else{
            $('#warehouse').hide(400);
            $('#biller').hide(400);
            $('select[name="warehouse_id"]').prop('required',false);
            $('select[name="biller_id"]').prop('required',false);
        }
    });
</script>
@endsection