@extends('layout.main') @section('content')
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div> 
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div> 
@endif

<section>
    <div class="container-fluid">
        @if(in_array("transfers-add", $all_permission))
            <a href="{{route('transfers.create')}}" class="btn btn-info"><i class="dripicons-plus"></i> {{trans('file.add')}} {{trans('file.Transfer')}}</a>
            <a href="{{url('transfers/transfer_by_csv')}}" class="btn btn-primary"><i class="dripicons-copy"></i> {{trans('file.import')}} {{trans('file.Transfer')}}</a>
        @endif
    </div>
    <div class="table-responsive">
        <table id="transfer-table" class="table transfer-list">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.Date')}}</th>
                    <th>{{trans('file.reference')}} No</th>
                    <th>{{trans('file.Warehouse')}}({{trans('file.From')}})</th>
                    <th>{{trans('file.Warehouse')}}({{trans('file.To')}})</th>
                    <th>{{trans('file.product')}} {{trans('file.Cost')}}</th>
                    <th>{{trans('file.product')}} {{trans('file.Tax')}}</th>
                    <th>{{trans('file.grand total')}}</th>
                    <th>{{trans("file.Status")}}</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lims_transfer_all as $key=>$transfer)
                <?php                     
                    if($transfer->status == 1)
                        $status = trans('file.Completed');
                    elseif($transfer->status == 2)
                        $status = trans('file.Pending');
                    elseif($transfer->status == 3)
                        $status = trans('file.Sent');
                ?>
                <tr class="transfer-link" data-transfer='["{{date($general_setting->date_format, strtotime($transfer->created_at->toDateString()))}}", "{{$transfer->reference_no}}", "{{$status}}", "{{$transfer->id}}", "{{$transfer->fromWarehouse->name}}", "{{$transfer->fromWarehouse->phone}}", "{{$transfer->fromWarehouse->address}}", "{{$transfer->toWarehouse->name}}", "{{$transfer->toWarehouse->phone}}", "{{$transfer->toWarehouse->address}}", "{{$transfer->total_tax}}", "{{$transfer->total_cost}}", "{{$transfer->shipping_cost}}", "{{$transfer->grand_total}}", "{{$transfer->note}}", "{{$transfer->user->name}}", "{{$transfer->user->email}}"]'>
                    <td>{{$key}}</td>
                    <td>{{ date($general_setting->date_format, strtotime($transfer->created_at->toDateString())) . ' '. $transfer->created_at->toTimeString() }}</td>
                    <td>{{ $transfer->reference_no }}</td>
                    <td>{{ $transfer->fromWarehouse->name }}</td>
                    <td>{{ $transfer->toWarehouse->name }}</td>
                    <td class="total-cost">{{ $transfer->total_cost }}</td>
                    <td class="total-tax">{{ $transfer->total_tax }}</td>
                    <td class="grand-total">{{ $transfer->grand_total }}</td>
                    @if($transfer->status == 1)
                        <td><div class="badge badge-success">{{$status}}</div></td>
                    @elseif($transfer->status == 2)
                        <td><div class="badge badge-danger">{{$status}}</div></td>
                    @else
                        <td><div class="badge badge-warning">{{$status}}</div></td>
                    @endif
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{trans('file.action')}}<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                <li>
                                    <button type="button" class="btn btn-link view"><i class="fa fa-eye"></i> {{trans('file.View')}}</button>
                                </li>
                                @if(in_array("transfers-edit", $all_permission))
                                <li>
                                    <a href="{{ route('transfers.edit', $transfer->id) }}" class="btn btn-link"><i class="dripicons-document-edit"></i> {{trans('file.edit')}}</a> 
                                </li>
                                @endif
                                <li class="divider"></li>
                                @if(in_array("transfers-delete", $all_permission))
                                {{ Form::open(['route' => ['transfers.destroy', $transfer->id], 'method' => 'DELETE'] ) }}
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
            <tfoot class="tfoot active">
                <th></th>
                <th>{{trans('file.Total')}}</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tfoot>
        </table>
    </div>
</section>

<div id="transfer-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <div class="container mt-3 pb-2 border-bottom">
            <div class="row">
                <div class="col-md-3">
                    <button id="print-btn" type="button" class="btn btn-default btn-sm d-print-none"><i class="dripicons-print"></i> {{trans('file.Print')}}</button>
                </div>
                <div class="col-md-6">
                    <h3 id="exampleModalLabel" class="modal-title text-center container-fluid">{{$general_setting->site_title}}</h3>
                </div>
                <div class="col-md-3">
                    <button type="button" id="close-btn" data-dismiss="modal" aria-label="Close" class="close d-print-none"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="col-md-12 text-center">
                    <i style="font-size: 15px;">{{trans('file.Transfer Details')}}</i>
                </div>
            </div>
        </div>
            <div id="transfer-content" class="modal-body">
            </div>
            <br>
            <table class="table table-bordered product-transfer-list">
                <thead>
                    <th>#</th>
                    <th>{{trans('file.product')}}</th>
                    <th>Qty</th>
                    <th>{{trans('file.Unit Cost')}}</th>
                    <th>{{trans('file.Tax')}}</th>
                    <th>{{trans('file.Subtotal')}}</th>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div id="transfer-footer" class="modal-body"></div>
      </div>
    </div>
</div>

<script type="text/javascript">
    $("ul#transfer").siblings('a').attr('aria-expanded','true');
    $("ul#transfer").addClass("show");
    $("ul#transfer #transfer-list-menu").addClass("active");

    var all_permission = <?php echo json_encode($all_permission) ?>;
    var transfer_id = [];
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

    $("tr.transfer-link td:not(:first-child, :last-child)").on("click", function(){
        var transfer = $(this).parent().data('transfer');
        transferDetails(transfer);
    });

    $(".view").on("click", function(){
        var transfer = $(this).parent().parent().parent().parent().parent().data('transfer');
        transferDetails(transfer);
    });

    $("#print-btn").on("click", function(){
          var divToPrint=document.getElementById('transfer-details');
          var newWin=window.open('','Print-Window');
          newWin.document.open();
          newWin.document.write('<link rel="stylesheet" href="<?php echo asset('public/vendor/bootstrap/css/bootstrap.min.css') ?>" type="text/css"><style type="text/css">@media print {.modal-dialog { max-width: 1000px;} }</style><body onload="window.print()">'+divToPrint.innerHTML+'</body>');
          newWin.document.close();
          setTimeout(function(){newWin.close();},10);
    });

    $('#transfer-table').DataTable( {
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
                'targets': [0, 9]
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
                text: '{{trans("file.delete")}}',
                className: 'buttons-delete',
                action: function ( e, dt, node, config ) {
                    if(user_verified == '1') {
                        transfer_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                var transfer = $(this).closest('tr').data('transfer');
                                transfer_id[i-1] = transfer[3];
                            }
                        });
                        if(transfer_id.length && confirm("Are you sure want to delete?")) {
                            $.ajax({
                                type:'POST',
                                url:'transfers/deletebyselection',
                                data:{
                                    transferIdArray: transfer_id
                                },
                                success:function(data){
                                    alert(data);
                                }
                            });
                            dt.rows({ page: 'current', selected: true }).remove().draw(false);
                        }
                        else if(!transfer_id.length)
                            alert('Nothing is selected!');
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
            $( dt_selector.column( 6 ).footer() ).html(dt_selector.cells( rows, 6, { page: 'current' } ).data().sum().toFixed(2));
            $( dt_selector.column( 7 ).footer() ).html(dt_selector.cells( rows, 7, { page: 'current' } ).data().sum().toFixed(2));
        }
        else {
            $( dt_selector.column( 5 ).footer() ).html(dt_selector.cells( rows, 5, { page: 'current' } ).data().sum().toFixed(2));
            $( dt_selector.column( 6 ).footer() ).html(dt_selector.cells( rows, 6, { page: 'current' } ).data().sum().toFixed(2));
            $( dt_selector.column( 7 ).footer() ).html(dt_selector.cells( rows, 7, { page: 'current' } ).data().sum().toFixed(2));
        }
    }

    function transferDetails(transfer) {
        var htmltext = '<strong>{{trans("file.Date")}}: </strong>'+transfer[0]+'<br><strong>{{trans("file.reference")}}: </strong>'+transfer[1]+'<br><strong> {{trans("file.Transfer")}} {{trans("file.Status")}}: </strong>'+transfer[2]+'<br><br><div class="row"><div class="col-md-6"><strong>{{trans("file.From")}}:</strong><br>'+transfer[4]+'<br>'+transfer[5]+'<br>'+transfer[6]+'</div><div class="col-md-6"><div class="float-right"><strong>{{trans("file.To")}}:</strong><br>'+transfer[7]+'<br>'+transfer[8]+'<br>'+transfer[9]+'</div></div></div>';

        $.get('transfers/product_transfer/' + transfer[3], function(data) {
            $(".product-transfer-list tbody").remove();
            var name_code = data[0];
            var qty = data[1];
            var unit_code = data[2];
            var tax = data[3];
            var tax_rate = data[4];
            var subtotal = data[5];
            var newBody = $("<tbody>");
            $.each(name_code, function(index) {
                var newRow = $("<tr>");
                var cols = '';
                cols += '<td><strong>' + (index+1) + '</strong></td>';
                cols += '<td>' + name_code[index] + '</td>';
                cols += '<td>' + qty[index] + ' ' + unit_code[index] + '</td>';
                cols += '<td>' + (subtotal[index] / qty[index]) + '</td>';
                cols += '<td>' + tax[index] + '(' + tax_rate[index] + '%)' + '</td>';
                cols += '<td>' + subtotal[index] + '</td>';
                newRow.append(cols);
                newBody.append(newRow);
            });

            var newRow = $("<tr>");
            cols = '';
            cols += '<td colspan=4><strong>{{trans("file.Total")}}:</strong></td>';
            cols += '<td>' + transfer[10] + '</td>';
            cols += '<td>' + transfer[11] + '</td>';
            newRow.append(cols);
            newBody.append(newRow);

            var newRow = $("<tr>");
            cols = '';
            cols += '<td colspan=5><strong>{{trans("file.Shipping Cost")}}:</strong></td>';
            cols += '<td>' + transfer[12] + '</td>';
            newRow.append(cols);
            newBody.append(newRow);

            var newRow = $("<tr>");
            cols = '';
            cols += '<td colspan=5><strong>{{trans("file.grand total")}}:</strong></td>';
            cols += '<td>' + transfer[13] + '</td>';
            newRow.append(cols);
            newBody.append(newRow);

             $("table.product-transfer-list").append(newBody);
        });

        var htmlfooter = '<p><strong>{{trans("file.Note")}}:</strong> '+transfer[14]+'</p><strong>{{trans("file.Created By")}}:</strong><br>'+transfer[15]+'<br>'+transfer[16];

        $('#transfer-content').html(htmltext);
        $('#transfer-footer').html(htmlfooter);
        $('#transfer-details').modal('show');
    }

    if(all_permission.indexOf("transfers-delete") == -1)
        $('.buttons-delete').addClass('d-none');
</script>
@endsection