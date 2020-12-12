@extends('layout.main') @section('content')
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div> 
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div> 
@endif
@if($errors->has('account_no'))
<div class="alert alert-danger alert-dismissible text-center">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ $errors->first('account_no') }}</div>
@endif 

<section>
    <div class="container-fluid">
        <button class="btn btn-info" data-toggle="modal" data-target="#account-modal"><i class="dripicons-plus"></i> {{trans('file.Add Account')}}</button>
    </div>
    <div class="table-responsive">
        <table id="account-table" class="table">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.Account')}} No</th>
                    <th>{{trans('file.name')}}</th>
                    <th>{{trans('file.Initial Balance')}}</th>
                    <th>{{trans('file.Default')}}</th>
                    <th>{{trans('file.Note')}}</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lims_account_all as $key=>$account)
                <tr>
                    <td>{{$key}}</td>
                    <td>{{ $account->account_no }}</td>
                    <td>{{ $account->name }}</td>
                    @if($account->initial_balance)
                        <td>{{ number_format((float)$account->initial_balance, 2, '.', '') }}</td>
                    @else
                        <td>0.00</td>
                    @endif
                    <td>
                        @if($account->is_default)
                        <input type="checkbox" checked class="default" data-id="{{$account->id}}" data-toggle="toggle" data-onstyle="success" data-offstyle="danger">
                        @else
                        <input type="checkbox" class="default" data-id="{{$account->id}}" data-toggle="toggle"  data-onstyle="success" data-offstyle="danger">
                        @endif
                    </td>
                    <td>{{ $account->note }}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{trans('file.action')}}
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                <li><button type="button" data-id="{{$account->id}}" data-account_no="{{$account->account_no}}" data-name="{{$account->name}}"  data-initial_balance="{{$account->initial_balance}}" data-note="{{$account->note}}" class="edit-btn btn btn-link" data-toggle="modal" data-target="#editModal"><i class="dripicons-document-edit"></i> {{trans('file.edit')}}</button></li>
                                <li class="divider"></li>
                                {{ Form::open(['route' => ['accounts.destroy', $account->id], 'method' => 'DELETE'] ) }}
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
            <tfoot class="tfoot active">
                <th></th>
                <th>{{trans('file.Total')}}</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tfoot>
        </table>
    </div>
</section>

<div id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Update Account')}}</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
              <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                {!! Form::open(['route' => ['accounts.update', 1], 'method' => 'put']) !!}
                    <div class="form-group">
                        <label>{{trans('file.Account')}} No *</label>
                        <input type="text" name="account_no" required class="form-control">
                        <input type="hidden" name="account_id">
                    </div>
                    <div class="form-group">
                        <label>{{trans('file.name')}} *</label>
                        <input type="text" name="name" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>{{trans('file.Initial Balance')}}</label>
                        <input type="number" name="initial_balance" step="any" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>{{trans('file.Note')}}</label>
                        <textarea name="note" rows="3" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">{{trans('file.update')}}</button>
                    </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    $("ul#account").siblings('a').attr('aria-expanded','true');
    $("ul#account").addClass("show");
    $("ul#account #account-list-menu").addClass("active");

    $('.edit-btn').on('click', function() {
        $("#editModal input[name='account_no']").val( $(this).data('account_no') );
        $("#editModal input[name='name']").val( $(this).data('name') );
        $("#editModal input[name='initial_balance']").val( $(this).data('initial_balance') );
        $("#editModal input[name='account_id']").val( $(this).data('id') );
        $("#editModal textarea[name='note']").val( $(this).data('note') );
    });

    $('.default').on('change', function() {
        //off to on
        if ($(this).parent().hasClass("btn-success")) {
            var id = $(this).data('id');
            $('.default').not($(this)).parent().removeClass('btn-success');
            $('.default').not($(this)).parent().addClass('btn-danger off');
            $('.default').not($(this)).prop('checked', false);
            $(this).prop('checked', true);
            $.get('accounts/make-default/' + id, function(data) {
                alert(data);
            });
        }
        //on to off
        else {
            $(this).parent().removeClass('btn-danger off');
            $(this).parent().addClass('btn-success');
            $(this).prop('checked', true);
            alert('Please make another account default first!');
        }
    });

function confirmDelete() {
    if (confirm("Are you sure want to delete?")) {
        return true;
    }
    return false;
}
    var table = $('#account-table').DataTable( {
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
                'targets': [0, 6]
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
                    rows: ':visible'
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
                    rows: ':visible'
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
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
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
            $( dt_selector.column( 3 ).footer() ).html(dt_selector.cells( rows, 3, { page: 'current' } ).data().sum().toFixed(2));
        }
        else {
            $( dt_selector.column( 3 ).footer() ).html(dt_selector.cells( rows, 3, { page: 'current' } ).data().sum().toFixed(2));
        }
    }

</script>
@endsection