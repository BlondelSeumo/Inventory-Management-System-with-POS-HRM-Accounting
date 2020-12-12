@extends('layout.main') @section('content')
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{!! session()->get('message') !!}</div> 
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div> 
@endif
<section>
    <div class="container-fluid">
        <button class="btn btn-info" data-toggle="modal" data-target="#createModal"><i class="dripicons-plus"></i> {{trans('file.Add Payroll')}} </button>
    </div>
    <div class="table-responsive">
        <table id="payroll-table" class="table">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.date')}}</th>
                    <th>{{trans('file.reference')}}</th>
                    <th>{{trans('file.Employee')}}</th>
                    <th>{{trans('file.Account')}}</th>
                    <th>{{trans('file.Amount')}}</th>
                    <th>{{trans('file.Method')}}</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lims_payroll_all as $key=>$payroll)
                @php 
                    $employee = \App\Employee::find($payroll->employee_id);
                    $account = \App\Account::find($payroll->account_id);
                @endphp
                <tr data-id="{{$payroll->id}}">
                    <td>{{$key}}</td>
                    <td>{{date($general_setting->date_format, strtotime($payroll->created_at->toDateString())) }}</td>
                    <td>{{ $payroll->reference_no }}</td>
                    <td>{{ $employee->name}}</td>
                    <td>{{ $account->name}}</td>
                    <td>{{ number_format((float)$payroll->amount, 2, '.', '') }}</td>
                    @if($payroll->paying_method == 0)
                        <td>Cash</td>
                    @elseif($payroll->paying_method == 1)
                        <td>Cheque</td>
                    @else
                        <td>Credit Card</td>
                    @endif
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{trans('file.action')}}
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                <li>
                                    <button type="button" data-id="{{$payroll->id}}" data-reference="{{$payroll->reference_no}}" data-employee="{{$payroll->employee_id}}" data-account="{{$payroll->account_id}}" data-amount="{{$payroll->amount}}" data-note="{{$payroll->note}}" data-paying_method="{{$payroll->paying_method}}" class="edit-btn btn btn-link" data-toggle="modal" data-target="#editModal"><i class="dripicons-document-edit"></i> {{trans('file.edit')}}</button> 
                                </li>
                                <li class="divider"></li>
                                {{ Form::open(['route' => ['payroll.destroy', $payroll->id], 'method' => 'DELETE'] ) }}
                                <li>
                                    <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> {{trans('file.delete')}}</button>
                                </li>
                                {{ Form::close() }}
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th></th>
                    <th>Total:</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</section>

<div id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Add Payroll')}}</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
              <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                {!! Form::open(['route' => 'payroll.store', 'method' => 'post', 'files' => true]) !!}
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>{{trans('file.Employee')}} *</label>
                        <select class="form-control selectpicker" name="employee_id" required data-live-search="true" data-live-search-style="begins" title="Select Employee...">
                            @foreach($lims_employee_list as $employee)
                            <option value="{{$employee->id}}">{{$employee->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label> {{trans('file.Account')}} *</label>
                        <select class="form-control selectpicker" name="account_id">
                        @foreach($lims_account_list as $account)
                            @if($account->is_default)
                            <option selected value="{{$account->id}}">{{$account->name}} [{{$account->account_no}}]</option>
                            @else
                            <option value="{{$account->id}}">{{$account->name}} [{{$account->account_no}}]</option>
                            @endif
                        @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>{{trans('file.Amount')}} *</label>
                        <input type="number" step="any" name="amount" class="form-control" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>{{trans('file.Method')}} *</label>
                        <select class="form-control selectpicker" name="paying_method" required>
                            <option value="0">Cash</option>
                            <option value="1">Cheque</option>
                            <option value="2">Credit Card</option>
                        </select>
                    </div>
                    <div class="col-md-12 form-group">
                        <label>{{trans('file.Note')}}</label>
                        <textarea name="note" rows="3" class="form-control"></textarea>
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

<div id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Update Payroll')}}</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
              <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                {!! Form::open(['route' => ['payroll.update', 1], 'method' => 'put', 'files' => true]) !!}
                <div class="row">
                    <div class="col-md-6 form-group">
                        <input type="hidden" name="payroll_id">
                        <label>{{trans('file.Employee')}} *</label>
                        <select class="form-control selectpicker" name="employee_id" required data-live-search="true" data-live-search-style="begins" title="Select Employee...">
                            @foreach($lims_employee_list as $employee)
                            <option value="{{$employee->id}}">{{$employee->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label> {{trans('file.Account')}} *</label>
                        <select class="form-control selectpicker" name="account_id">
                        @foreach($lims_account_list as $account)
                            @if($account->is_default)
                            <option selected value="{{$account->id}}">{{$account->name}} [{{$account->account_no}}]</option>
                            @else
                            <option value="{{$account->id}}">{{$account->name}} [{{$account->account_no}}]</option>
                            @endif
                        @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>{{trans('file.Amount')}} *</label>
                        <input type="number" step="any" name="amount" class="form-control" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>{{trans('file.Method')}} *</label>
                        <select class="form-control selectpicker" name="paying_method" required>
                            <option value="0">Cash</option>
                            <option value="1">Cheque</option>
                            <option value="2">Credit Card</option>
                        </select>
                    </div>
                    <div class="col-md-12 form-group">
                        <label>{{trans('file.Note')}}</label>
                        <textarea name="note" rows="3" class="form-control"></textarea>
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
    $("ul#hrm #payroll-menu").addClass("active");

    var payroll_id = [];
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
        $("#editModal input[name='payroll_id']").val( $(this).data('id') );
        $("#editModal select[name='employee_id']").val( $(this).data('employee') );
        $("#editModal select[name='account_id']").val( $(this).data('account') );
        $("#editModal input[name='amount']").val( $(this).data('amount') );
        $("#editModal select[name='paying_method']").val( $(this).data('paying_method') );
        $("#editModal textarea[name='note']").val( $(this).data('note') );
        $('.selectpicker').selectpicker('refresh');
    });

    $('#payroll-table').DataTable( {
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
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'csv',
                text: '{{trans("file.CSV")}}',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible',
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'print',
                text: '{{trans("file.Print")}}',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible',
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                text: '{{trans("file.delete")}}',
                className: 'buttons-delete',
                action: function ( e, dt, node, config ) {
                    if(user_verified == '1') {
                        payroll_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                payroll_id[i-1] = $(this).closest('tr').data('id');
                            }
                        });
                        if(payroll_id.length && confirm("Are you sure want to delete?")) {
                            $.ajax({
                                type:'POST',
                                url:'payroll/deletebyselection',
                                data:{
                                    payrollIdArray: payroll_id
                                },
                                success:function(data){
                                    alert(data);
                                }
                            });
                            dt.rows({ page: 'current', selected: true }).remove().draw(false);
                        }
                        else if(!payroll_id.length)
                            alert('No payroll is selected!');
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
        drawCallback: function () {
            var api = this.api();
            datatable_sum(api, false);
        }
    } );

    function datatable_sum(dt_selector, is_calling_first) {
        if (dt_selector.rows( '.selected' ).any() && is_calling_first) {
            var rows = dt_selector.rows( '.selected' ).indexes();

            $( dt_selector.column( 5 ).footer() ).html(dt_selector.cells( rows, 5, { page: 'current' } ).data().sum().toFixed(2));
        }
        else {
            $( dt_selector.column( 5 ).footer() ).html(dt_selector.cells( rows, 5, { page: 'current' } ).data().sum().toFixed(2));
        }
    }
</script>
@endsection