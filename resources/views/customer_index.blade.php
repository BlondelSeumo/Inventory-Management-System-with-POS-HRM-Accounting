@extends('layout.main')
@section('content')

@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div> 
@endif
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div> 
@endif
<div class="row">
  <div class="container-fluid">
    <div class="col-md-12">
      <div class="brand-text float-left mt-4">
          <h3>{{trans('file.welcome')}} <span>{{Auth::user()->name}}</span> </h3>
      </div>
    </div>
  </div>
</div>
<!-- Counts Section -->
<section class="dashboard-counts">
  
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          
          <ul class="nav nav-tabs mt-2" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" href="#sale-latest" role="tab" data-toggle="tab">{{trans('file.Sale')}}</a>
            </li>
          </ul>

          <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade show active" id="sale-latest">
                <div class="table-responsive">
                  <table id="sale-table" class="table">
                    <thead>
                      <tr>
                        <th class="not-exported"></th>
                        <th>{{trans('file.date')}}</th>
                        <th>{{trans('file.reference')}}</th>
                        <th>{{trans('file.Biller')}}</th>
                        <th>{{trans('file.Warehouse')}}</th>
                        <th>{{trans('file.Sale Status')}}</th>
                        <th>{{trans('file.Payment Status')}}</th>
                        <th>{{trans('file.grand total')}}</th>
                        <th>{{trans('file.Paid')}}</th>
                        <th>{{trans('file.Due')}}</th>
                        <th>{{trans('file.action')}}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($lims_sale_data as $key => $sale)
                        <?php 
                            $coupon = \App\Coupon::find($sale->coupon_id); 
                            if($coupon)
                              $coupon_code = $coupon->code;
                            else
                              $coupon_code = null;

                            if($sale->sale_status == 1)
                              $status = trans('file.Completed');
                            elseif($sale->sale_status == 2)
                              $status = trans('file.Pending');
                            else
                              $status = trans('file.Draft');

                            $sale_note = preg_replace('/\s+/S', " ", $sale->sale_note);
                            $staff_note = preg_replace('/\s+/S', " ", $sale->staff_note);
                        ?>
                        
                      <tr data-sale='["{{date($general_setting->date_format, strtotime($sale->created_at->toDateString()))}}", "{{$sale->reference_no}}", "{{$status}}", "{{$sale->biller->name}}", "{{$sale->biller->company_name}}", "{{$sale->biller->email}}", "{{$sale->biller->phone_number}}", "{{$sale->biller->address}}", "{{$sale->biller->city}}", "{{$sale->customer->name}}", "{{$sale->customer->phone_number}}", "{{$sale->customer->address}}", "{{$sale->customer->city}}", "{{$sale->id}}", "{{$sale->total_tax}}", "{{$sale->total_discount}}", "{{$sale->total_price}}", "{{$sale->order_tax}}", "{{$sale->order_tax_rate}}", "{{$sale->order_discount}}", "{{$sale->shipping_cost}}", "{{$sale->grand_total}}", "{{$sale->paid_amount}}", "{{$sale_note}}", "{{$staff_note}}", "{{$sale->user->name}}", "{{$sale->user->email}}", "{{$sale->warehouse->name}}", "{{$coupon_code}}", "{{$sale->coupon_discount}}"]'>
                        <td>{{$key}}</td>
                        <td>{{ date($general_setting->date_format, strtotime($sale->created_at->toDateString())) }}</td>
                        <td>{{$sale->reference_no}}</td>
                        <td>{{$sale->biller->name}}</td>
                        <td>{{$sale->warehouse->name}}</td>
                        @if($sale->sale_status == 1)
                        <td><div class="badge badge-success">{{$status}}</div></td>
                        @elseif($sale->sale_status == 2)
                        <td><div class="badge badge-danger">{{$status}}</div></td>
                        @else
                        <td><div class="badge badge-warning">{{$status}}</div></td>
                        @endif
                        @if($sale->payment_status == 1)
                        <td><div class="badge badge-danger">{{trans('file.Pending')}}</div></td>
                        @elseif($sale->payment_status == 2)
                        <td><div class="badge badge-danger">{{trans('file.Due')}}</div></td>
                        @elseif($sale->payment_status == 3)
                        <td><div class="badge badge-success">{{trans('file.Partial')}}</div></td>
                        @else
                        <td><div class="badge badge-success">{{trans('file.Paid')}}</div></td>
                        @endif
                        <td>{{number_format($sale->paid_amount, 2)}}</td>
                        <td>{{number_format($sale->grand_total - $sale->paid_amount, 2)}}</td>
                        <td>{{number_format($sale->grand_total, 2)}}</td>
                        <td><button type="button" class="btn btn-info btn-sm view-btn" title="{{trans('file.View')}}"><i class="dripicons-preview"></i></button></td>
                      </tr>
                      @endforeach
                    </tbody>
                    <tfoot class="tfoot active">
                      <tr>
                          <th></th>
                          <th>Total:</th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th>0.00</th>
                          <th>0.00</th>
                          <th>0.00</th>
                          <th></th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div id="sale-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
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
                        <i style="font-size: 15px;">{{trans('file.Sale Details')}}</i>
                    </div>
                </div>
            </div>
            <div id="sale-content" class="modal-body">
            </div>
            <br>
            <table class="table table-bordered product-sale-list">
                <thead>
                    <th>#</th>
                    <th>{{trans('file.product')}}</th>
                    <th>{{trans('file.Qty')}}</th>
                    <th>{{trans('file.Unit Price')}}</th>
                    <th>{{trans('file.Tax')}}</th>
                    <th>{{trans('file.Discount')}}</th>
                    <th>{{trans('file.Subtotal')}}</th>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div id="sale-footer" class="modal-body"></div>
        </div>
    </div>
</div>
      
<script type="text/javascript">
    $(".view-btn").on("click", function() {
        var sale = $(this).parent().parent().data('sale');
        saleDetails(sale);
    });

    $("#print-btn").on("click", function(){
      var divToPrint=document.getElementById('sale-details');
      var newWin=window.open('','Print-Window');
      newWin.document.open();
      newWin.document.write('<link rel="stylesheet" href="<?php echo asset('public/vendor/bootstrap/css/bootstrap.min.css') ?>" type="text/css"><style type="text/css">@media print {.modal-dialog { max-width: 1000px;} }</style><body onload="window.print()">'+divToPrint.innerHTML+'</body>');
      newWin.document.close();
      setTimeout(function(){newWin.close();},10);
    });

    function saleDetails(sale){
        var htmltext = '<strong>{{trans("file.Date")}}: </strong>'+sale[0]+'<br><strong>{{trans("file.reference")}}: </strong>'+sale[1]+'<br><strong>{{trans("file.Warehouse")}}: </strong>'+sale[27]+'<br><strong>{{trans("file.Sale Status")}}: </strong>'+sale[2]+'<br><br><div class="row"><div class="col-md-6"><strong>{{trans("file.From")}}:</strong><br>'+sale[3]+'<br>'+sale[4]+'<br>'+sale[5]+'<br>'+sale[6]+'<br>'+sale[7]+'<br>'+sale[8]+'</div><div class="col-md-6"><div class="float-right"><strong>{{trans("file.To")}}:</strong><br>'+sale[9]+'<br>'+sale[10]+'<br>'+sale[11]+'<br>'+sale[12]+'</div></div></div>';
        $.get('sales/product_sale/' + sale[13], function(data){
            $(".product-sale-list tbody").remove();
            var name_code = data[0];
            var qty = data[1];
            var unit_code = data[2];
            var tax = data[3];
            var tax_rate = data[4];
            var discount = data[5];
            var subtotal = data[6];
            var newBody = $("<tbody>");
            $.each(name_code, function(index){
                var newRow = $("<tr>");
                var cols = '';
                cols += '<td><strong>' + (index+1) + '</strong></td>';
                cols += '<td>' + name_code[index] + '</td>';
                cols += '<td>' + qty[index] + ' ' + unit_code[index] + '</td>';
                cols += '<td>' + parseFloat(subtotal[index] / qty[index]).toFixed(2) + '</td>';
                cols += '<td>' + tax[index] + '(' + tax_rate[index] + '%)' + '</td>';
                cols += '<td>' + discount[index] + '</td>';
                cols += '<td>' + subtotal[index] + '</td>';
                newRow.append(cols);
                newBody.append(newRow);
            });

            var newRow = $("<tr>");
            cols = '';
            cols += '<td colspan=4><strong>{{trans("file.Total")}}:</strong></td>';
            cols += '<td>' + sale[14] + '</td>';
            cols += '<td>' + sale[15] + '</td>';
            cols += '<td>' + sale[16] + '</td>';
            newRow.append(cols);
            newBody.append(newRow);

            var newRow = $("<tr>");
            cols = '';
            cols += '<td colspan=6><strong>{{trans("file.Order Tax")}}:</strong></td>';
            cols += '<td>' + sale[17] + '(' + sale[18] + '%)' + '</td>';
            newRow.append(cols);
            newBody.append(newRow);

            var newRow = $("<tr>");
            cols = '';
            cols += '<td colspan=6><strong>{{trans("file.Order Discount")}}:</strong></td>';
            cols += '<td>' + sale[19] + '</td>';
            newRow.append(cols);
            newBody.append(newRow);
            if(sale[28]) {
                var newRow = $("<tr>");
                cols = '';
                cols += '<td colspan=6><strong>{{trans("file.Coupon Discount")}} ['+sale[28]+']:</strong></td>';
                cols += '<td>' + sale[29] + '</td>';
                newRow.append(cols);
                newBody.append(newRow);
            }

            var newRow = $("<tr>");
            cols = '';
            cols += '<td colspan=6><strong>{{trans("file.Shipping Cost")}}:</strong></td>';
            cols += '<td>' + sale[20] + '</td>';
            newRow.append(cols);
            newBody.append(newRow);

            var newRow = $("<tr>");
            cols = '';
            cols += '<td colspan=6><strong>{{trans("file.grand total")}}:</strong></td>';
            cols += '<td>' + sale[21] + '</td>';
            newRow.append(cols);
            newBody.append(newRow);

            var newRow = $("<tr>");
            cols = '';
            cols += '<td colspan=6><strong>{{trans("file.Paid Amount")}}:</strong></td>';
            cols += '<td>' + sale[22] + '</td>';
            newRow.append(cols);
            newBody.append(newRow);

            var newRow = $("<tr>");
            cols = '';
            cols += '<td colspan=6><strong>{{trans("file.Due")}}:</strong></td>';
            cols += '<td>' + parseFloat(sale[21] - sale[22]).toFixed(2) + '</td>';
            newRow.append(cols);
            newBody.append(newRow);

            $("table.product-sale-list").append(newBody);
        });
        var htmlfooter = '<p><strong>{{trans("file.Sale Note")}}:</strong> '+sale[23]+'</p><p><strong>{{trans("file.Staff Note")}}:</strong> '+sale[24];
        $('#sale-content').html(htmltext);
        $('#sale-footer').html(htmlfooter);
        $('#sale-details').modal('show');
    }

    $('#sale-table').DataTable( {
        "order": [],
        'columnDefs': [
            {
                "orderable": false,
                'targets': 0
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
                exportOptions: {
                    columns: ':visible:Not(.not-exported-sale)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_sale(dt, true);
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                    datatable_sum_sale(dt, false);
                },
                footer:true
            },
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':visible:Not(.not-exported-sale)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_sale(dt, true);
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                    datatable_sum_sale(dt, false);
                },
                footer:true
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:Not(.not-exported-sale)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_sale(dt, true);
                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                    datatable_sum_sale(dt, false);
                },
                footer:true
            },
            {
                extend: 'colvis',
                columns: ':gt(0)'
            }
        ],
        drawCallback: function () {
            var api = this.api();
            datatable_sum_sale(api, false);
        }
    } );

    function datatable_sum_sale(dt_selector, is_calling_first) {
        if (dt_selector.rows( '.selected' ).any() && is_calling_first) {
            var rows = dt_selector.rows( '.selected' ).indexes();

            $( dt_selector.column( 7 ).footer() ).html(dt_selector.cells( rows, 7, { page: 'current' } ).data().sum().toFixed(2));
            $( dt_selector.column( 8 ).footer() ).html(dt_selector.cells( rows, 8, { page: 'current' } ).data().sum().toFixed(2));
            $( dt_selector.column( 9 ).footer() ).html(dt_selector.cells( rows, 9, { page: 'current' } ).data().sum().toFixed(2));
        }
        else {
            $( dt_selector.column( 7 ).footer() ).html(dt_selector.cells( rows, 7, { page: 'current' } ).data().sum().toFixed(2));
            $( dt_selector.column( 8 ).footer() ).html(dt_selector.column( 8, {page:'current'} ).data().sum().toFixed(2));
            $( dt_selector.column( 9 ).footer() ).html(dt_selector.column( 9, {page:'current'} ).data().sum().toFixed(2));
        }
    }
</script>
@endsection

