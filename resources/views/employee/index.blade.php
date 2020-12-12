@extends('layout.main') @section('content')
@if($errors->has('name'))
<div class="alert alert-danger alert-dismissible text-center">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ $errors->first('name') }}</div>
@endif
@if($errors->has('image'))
<div class="alert alert-danger alert-dismissible text-center">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ $errors->first('image') }}</div>
@endif
@if($errors->has('email'))
<div class="alert alert-danger alert-dismissible text-center">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ $errors->first('email') }}</div>
@endif 
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{!! session()->get('message') !!}</div> 
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div> 
@endif
<section>
    @if(in_array("employees-add", $all_permission))
    <div class="container-fluid">
        <a href="{{route('employees.create')}}" class="btn btn-info"><i class="dripicons-plus"></i> {{trans('file.Add Employee')}}</a>
    </div>
    @endif
    <div class="table-responsive">
        <table id="employee-table" class="table">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.Image')}}</th>
                    <th>{{trans('file.name')}}</th>
                    <th>{{trans('file.Email')}}</th>
                    <th>{{trans('file.Phone Number')}}</th>
                    <th>{{trans('file.Department')}}</th>
                    <th>{{trans('file.Address')}}</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lims_employee_all as $key=>$employee)
                @php $department = \App\Department::find($employee->department_id); @endphp
                <tr data-id="{{$employee->id}}">
                    <td>{{$key}}</td>
                    @if($employee->image)
                    <td> <img src="{{url('public/images/employee',$employee->image)}}" height="80" width="80">
                    </td>
                    @else
                    <td>No Image</td>
                    @endif
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->email}}</td>
                    <td>{{ $employee->phone_number}}</td>
                    <td>{{ $department->name }}</td>
                    <td>{{ $employee->address}}
                            @if($employee->city){{ ', '.$employee->city}}@endif
                            @if($employee->state){{ ', '.$employee->state}}@endif
                            @if($employee->postal_code){{ ', '.$employee->postal_code}}@endif
                            @if($employee->country){{ ', '.$employee->country}}@endif</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{trans('file.action')}}
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                @if(in_array("employees-edit", $all_permission))
                                <li>
                                    <button type="button" data-id="{{$employee->id}}" data-name="{{$employee->name}}" data-email="{{$employee->email}}" data-phone_number="{{$employee->phone_number}}" data-department_id="{{$employee->department_id}}" data-address="{{$employee->address}}" data-city="{{$employee->city}}" data-country="{{$employee->country}}" class="edit-btn btn btn-link" data-toggle="modal" data-target="#editModal"><i class="dripicons-document-edit"></i> {{trans('file.edit')}}</button> 
                                </li>
                                @endif
                                <li class="divider"></li>
                                @if(in_array("employees-delete", $all_permission))
                                {{ Form::open(['route' => ['employees.destroy', $employee->id], 'method' => 'DELETE'] ) }}
                                <li>
                                    <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> {{trans('file.delete')}}</button>
                                </li>
                                {{ Form::close() }}
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<div id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Update Employee')}}</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
              <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                {!! Form::open(['route' => ['employees.update', 1], 'method' => 'put', 'files' => true]) !!}
                <div class="row">
                    <div class="col-md-6 form-group">
                        <input type="hidden" name="employee_id" />
                        <label>{{trans('file.name')}} *</label>
                        <input type="text" name="name" required class="form-control">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>{{trans('file.Image')}}</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>{{trans('file.Department')}} *</label>
                        <select class="form-control selectpicker" name="department_id" required>
                            @foreach($lims_department_list as $department)
                            <option value="{{$department->id}}">{{$department->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>{{trans('file.Email')}} *</label>
                        <input type="email" name="email" required class="form-control">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>{{trans('file.Phone Number')}} *</label>
                        <input type="text" name="phone_number" required class="form-control">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>{{trans('file.Address')}}</label>
                        <input type="text" name="address" class="form-control">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>{{trans('file.City')}}</label>
                        <input type="text" name="city" class="form-control">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>{{trans('file.Country')}}</label>
                        <input type="text" name="country" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">{{trans('file.submit')}}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    $("ul#hrm").siblings('a').attr('aria-expanded','true');
    $("ul#hrm").addClass("show");
    $("ul#hrm #employee-menu").addClass("active");

    var employee_id = [];
    var user_verified = <?php echo json_encode(env('USER_VERIFIED')) ?>;
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function confirmDelete() {
        if (confirm("Are you sure want to delete?")) {
            return true;
        }
        return false;
    }

    $('.edit-btn').on('click', function() {
        $("#editModal input[name='employee_id']").val( $(this).data('id') );
        $("#editModal input[name='name']").val( $(this).data('name') );
        $("#editModal select[name='department_id']").val( $(this).data('department_id') );
        $("#editModal input[name='email']").val( $(this).data('email') );
        $("#editModal input[name='phone_number']").val( $(this).data('phone_number') );
        $("#editModal input[name='address']").val( $(this).data('address') );
        $("#editModal input[name='city']").val( $(this).data('city') );
        $("#editModal input[name='country']").val( $(this).data('country') );
        $('.selectpicker').selectpicker('refresh');
    });

    $('#employee-table').DataTable( {
        "order": [],
        'language': {
            'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
             "info":      '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search":  '{{trans("file.Search")}}',
            'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 1, 6]
            },
            {
                'render': function(data, type, row, meta){
                    if(type === 'display'){
                        data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
                    }

                   return data;
                },
                'checkboxes': {
                   'selectRow': true,
                   'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
                },
                'targets': [0]
            }
        ],
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'pdf',
                text: '{{trans("file.PDF")}}',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible',
                    stripHtml: false
                },
                customize: function(doc) {
                    for (var i = 1; i < doc.content[1].table.body.length; i++) {
                        if (doc.content[1].table.body[i][0].text.indexOf('<img src=') !== -1) {
                            var imagehtml = doc.content[1].table.body[i][0].text;
                            var regex = /<img.*?src=['"](.*?)['"]/;
                            var src = regex.exec(imagehtml)[1];
                            var tempImage = new Image();
                            tempImage.src = src;
                            var canvas = document.createElement("canvas");
                            canvas.width = tempImage.width;
                            canvas.height = tempImage.height;
                            var ctx = canvas.getContext("2d");
                            ctx.drawImage(tempImage, 0, 0);
                            var imagedata = canvas.toDataURL("image/png");
                            delete doc.content[1].table.body[i][0].text;
                            doc.content[1].table.body[i][0].image = imagedata;
                            doc.content[1].table.body[i][0].fit = [30, 30];
                        }
                    }
                },
            },
            {
                extend: 'csv',
                text: '{{trans("file.CSV")}}',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible',
                    format: {
                        body: function ( data, row, column, node ) {
                            if (column === 0 && (data.indexOf('<img src=') != -1)) {
                                var regex = /<img.*?src=['"](.*?)['"]/;
                                data = regex.exec(data)[1];                 
                            }
                            return data;
                        }
                    }
                },
            },
            {
                extend: 'print',
                text: '{{trans("file.Print")}}',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible',
                    stripHtml: false
                },
            },
            {
                text: '{{trans("file.delete")}}',
                className: 'buttons-delete',
                action: function ( e, dt, node, config ) {
                    if(user_verified == '1') {
                        employee_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                employee_id[i-1] = $(this).closest('tr').data('id');
                            }
                        });
                        if(employee_id.length && confirm("Are you sure want to delete?")) {
                            $.ajax({
                                type:'POST',
                                url:'employees/deletebyselection',
                                data:{
                                    employeeIdArray: employee_id
                                },
                                success:function(data){
                                    alert(data);
                                }
                            });
                            dt.rows({ page: 'current', selected: true }).remove().draw(false);
                        }
                        else if(!employee_id.length)
                            alert('No employee is selected!');
                    }
                    else
                        alert('This feature is disable for demo!');
                }
            },
            {
                extend: 'colvis',
                text: '{{trans("file.Column visibility")}}',
                columns: ':gt(0)'
            },
        ],
    } );
</script>
@endsection