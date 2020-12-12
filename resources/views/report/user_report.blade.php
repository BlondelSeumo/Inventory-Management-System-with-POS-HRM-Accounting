@extends('layout.main') @section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header mt-2">
                <h3 class="text-center">{{trans('file.User Report')}}</h3>
            </div>
            {!! Form::open(['route' => 'report.user', 'method' => 'post']) !!}
            <div class="row mb-3">
                <div class="col-md-4 offset-md-2 mt-3">
                    <div class="form-group row">
                        <label class="d-tc mt-2"><strong>{{trans('file.Choose Your Date')}}</strong> &nbsp;</label>
                        <div class="d-tc">
                            <div class="input-group">
                                <input type="text" class="daterangepicker-field form-control" value="{{$start_date}} To {{$end_date}}" required />
                                <input type="hidden" name="start_date" value="{{$start_date}}" />
                                <input type="hidden" name="end_date" value="{{$end_date}}" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-3">
                    <div class="form-group row">
                        <label class="d-tc mt-2"><strong>{{trans('file.Choose User')}}</strong> &nbsp;</label>
                        <div class="d-tc">
                            <input type="hidden" name="user_id_hidden" value="{{$user_id}}" />
                            <select id="user_id" name="user_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins">
                                @foreach($lims_user_list as $user)
                                <option value="{{$user->id}}">{{$user->name}} ({{$user->phone}})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mt-3">
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit">{{trans('file.submit')}}</button>
                    </div>
                </div>
            </div>
            <input type="hidden" name="user_id_hidden" value="{{$user_id}}" />
            {!! Form::close() !!}
        </div>
    </div>
    <ul class="nav nav-tabs ml-4 mt-3" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" href="#user-sale" role="tab" data-toggle="tab">{{trans('file.Sale')}}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#user-purchase" role="tab" data-toggle="tab">{{trans('file.Purchase')}}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#user-quotation" role="tab" data-toggle="tab">{{trans('file.Quotation')}}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#user-transfer" role="tab" data-toggle="tab">{{trans('file.Transfer')}}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#user-payments" role="tab" data-toggle="tab">{{trans('file.Payment')}}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#user-expense" role="tab" data-toggle="tab">{{trans('file.Expense')}}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#user-payroll" role="tab" data-toggle="tab">{{trans('file.Payroll')}}</a>
        </li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade show active" id="user-sale">
            <div class="table-responsive mb-4">
                <table id="sale-table" class="table table-hover">
                    <thead>
                        <tr>
                            <th class="not-exported-sale"></th>
                            <th>{{trans('file.Date')}}</th>
                            <th>{{trans('file.reference')}}</th>
                            <th>{{trans('file.customer')}}</th>
                            <th>{{trans('file.Warehouse')}}</th>
                            <th>{{trans('file.product')}} ({{trans('file.qty')}})</th>
                            <th>{{trans('file.grand total')}}</th>
                            <th>{{trans('file.Paid')}}</th>
                            <th>{{trans('file.Due')}}</th>
                            <th>{{trans('file.Status')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lims_sale_data as $key=>$sale)
                        <tr>
                            <td>{{$key}}</td>
                            <td>{{date($general_setting->date_format, strtotime($sale->created_at->toDateString())) . ' '. $sale->created_at->toTimeString()}}</td>
                            <td>{{$sale->reference_no}}</td>
                            <td>{{$sale->customer->name}}</td>
                            <td>{{$sale->warehouse->name}}</td>
                            <td>
                                @foreach($lims_product_sale_data[$key] as $product_sale_data)
                                <?php 
                                    $product = App\Product::select('name')->find($product_sale_data->product_id);
                                    if($product_sale_data->variant_id) {
                                        $variant = App\Variant::find($product_sale_data->variant_id);
                                        $product->name .= ' ['.$variant->name.']';
                                    }
                                    $unit = App\Unit::find($product_sale_data->sale_unit_id);
                                ?>
                                @if($unit)
                                    {{$product->name.' ('.$product_sale_data->qty.' '.$unit->unit_code.')'}}
                                @else
                                    {{$product->name.' ('.$product_sale_data->qty.')'}}
                                @endif
                                <br>
                                @endforeach
                            </td>
                            <td>{{number_format((float) $sale->grand_total, 2, '.', '')}}</td>
                            @if($sale->paid_amount)
                            <td class="paid-amount">{{number_format((float) $sale->paid_amount, 2, '.', '')}}</td>
                            @else
                            <td>0.00</td>
                            @endif
                            <td>{{number_format((float)($sale->grand_total - $sale->paid_amount), 2, '.', '')}}</td>
                            @if($sale->sale_status == 1)
                            <td><div class="badge badge-success">{{trans('file.Completed')}}</div></td>
                            @else
                            <td><div class="badge badge-danger">{{trans('file.Pending')}}</div></td>
                            @endif
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
                            <th>0.00</th>
                            <th>0.00</th>
                            <th>0.00</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane fade" id="user-purchase">
            <div class="table-responsive mb-4">
                <table id="purchase-table" class="table table-hover">
                    <thead>
                        <tr>
                            <th class="not-exported-purchase"></th>
                            <th>{{trans('file.Date')}}</th>
                            <th>{{trans('file.reference')}}</th>
                            <th>{{trans('file.Supplier')}}</th>
                            <th>{{trans('file.Warehouse')}}</th>
                            <th>{{trans('file.product')}} ({{trans('file.qty')}})</th>
                            <th>{{trans('file.grand total')}}</th>
                            <th>{{trans('file.Paid Amount')}}</th>
                            <th>{{trans('file.Due')}}</th>
                            <th>{{trans('file.Status')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lims_purchase_data as $key=>$purchase)
                        <tr>
                            <td>{{$key}}</td>
                            <?php 
                                $supplier = DB::table('suppliers')->find($purchase->supplier_id);
                            ?>
                            <td>{{date($general_setting->date_format, strtotime($purchase->created_at->toDateString())) . ' '. $purchase->created_at->toTimeString()}}</td>
                            <td>{{$purchase->reference_no}}</td>
                            @if($supplier)
                            <td>{{$supplier->name}}</td>
                            @else
                            <td>N/A</td>
                            @endif
                            <td>{{$purchase->warehouse->name}}</td>
                            <td>
                                @foreach($lims_product_purchase_data[$key] as $product_purchase_data)
                                <?php 
                                    $product = App\Product::select('name')->find($product_purchase_data->product_id);
                                    if($product_purchase_data->variant_id) {
                                        $variant = App\Variant::find($product_purchase_data->variant_id);
                                        $product->name .= ' ['.$variant->name.']';
                                    }
                                    $unit = App\Unit::find($product_purchase_data->purchase_unit_id);
                                ?>
                                @if($unit)
                                    {{$product->name.' ('.$product_purchase_data->qty.' '.$unit->unit_code.')'}}
                                @else
                                    {{$product->name.' ('.$product_purchase_data->qty.')'}}
                                @endif
                                <br>
                                @endforeach
                            </td>
                            <td>{{number_format((float)($purchase->grand_total), 2, '.', '')}}</td>
                            <td>{{number_format((float)($purchase->paid_amount), 2, '.', '')}}</td>
                            <td>{{number_format((float)($purchase->grand_total - $purchase->paid_amount), 2, '.', '')}}</td>
                            @if($purchase->status == 1)
                               <td><div class="badge badge-success">{{trans('file.Recieved')}}</div></td>
                            @elseif($purchase->status == 2)
                               <td><div class="badge badge-success">{{trans('file.Partial')}}</div></td>
                            @elseif($purchase->status == 3)
                                <td><div class="badge badge-danger">{{trans('file.Pending')}}</div></td>
                            @elseif($purchase->status == 4)
                                <td><div class="badge badge-danger">{{trans('file.Ordered')}}</div></td>
                            @endif
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
                            <th>0.00</th>
                            <th>0.00</th>
                            <th>0.00</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane fade" id="user-quotation">
            <div class="table-responsive mb-4">
                <table id="quotation-table" class="table table-hover">
                    <thead>
                        <tr>
                            <th class="not-exported-quotation"></th>
                            <th>{{trans('file.Date')}}</th>
                            <th>{{trans('file.reference')}}</th>
                            <th>{{trans('file.customer')}}</th>
                            <th>{{trans('file.Warehouse')}}</th>
                            <th>{{trans('file.product')}} ({{trans('file.qty')}})</th>
                            <th>{{trans('file.grand total')}}</th>
                            <th>{{trans('file.Status')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lims_quotation_data as $key=>$quotation)
                        <tr>
                            <td>{{$key}}</td>
                            <td>{{date($general_setting->date_format, strtotime($quotation->created_at->toDateString())) . ' '. $quotation->created_at->toTimeString()}}</td>
                            <td>{{$quotation->reference_no}}</td>
                            <td>{{$quotation->customer->name}}</td>
                            <td>{{$quotation->warehouse->name}}</td>
                            <td>
                                @foreach($lims_product_quotation_data[$key] as $product_quotation_data)
                                <?php 
                                    $product = App\Product::select('name')->find($product_quotation_data->product_id);
                                    if($product_quotation_data->variant_id) {
                                        $variant = App\Variant::find($product_quotation_data->variant_id);
                                        $product->name .= ' ['.$variant->name.']';
                                    }
                                    $unit = App\Unit::find($product_quotation_data->quotation_unit_id);
                                ?>
                                @if($unit)
                                    {{$product->name.' ('.$product_quotation_data->qty.' '.$unit->unit_code.')'}}
                                @else
                                    {{$product->name.' ('.$product_quotation_data->qty.')'}}
                                @endif
                                <br>
                                @endforeach
                            </td>
                            <td>{{number_format((float) $quotation->grand_total, 2, '.', '')}}</td>
                            
                            @if($quotation->quotation_status == 2)
                            <td><div class="badge badge-success">{{trans('file.Sent')}}</div></td>
                            @else
                            <td><div class="badge badge-danger">{{trans('file.Pending')}}</div></td>
                            @endif
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
                            <th>0.00</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane fade" id="user-transfer">
            <div class="table-responsive mb-4">
                <table id="transfer-table" class="table table-hover">
                    <thead>
                        <tr>
                            <th class="not-exported-transfer"></th>
                            <th>{{trans('file.Date')}}</th>
                            <th>{{trans('file.reference')}}</th>
                            <th>{{trans('file.From')}}</th>
                            <th>{{trans('file.To')}}</th>
                            <th>{{trans('file.product')}} ({{trans('file.qty')}})</th>
                            <th>{{trans('file.grand total')}}</th>
                            <th>{{trans('file.Status')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lims_transfer_data as $key=>$transfer)
                        <tr>
                            <td>{{$key}}</td>
                            <td>{{date($general_setting->date_format, strtotime($transfer->created_at->toDateString())) . ' '. $transfer->created_at->toTimeString()}}</td>
                            <td>{{$transfer->reference_no}}</td>
                            <td>{{$transfer->fromWarehouse->name}}</td>
                            <td>{{$transfer->toWarehouse->name}}</td>
                            <td>
                                @foreach($lims_product_transfer_data[$key] as $product_transfer_data)
                                <?php 
                                    $product = App\Product::find($product_transfer_data->product_id);
                                    if($product_transfer_data->variant_id) {
                                        $variant = App\Variant::find($product_transfer_data->variant_id);
                                        $product->name .= ' ['.$variant->name.']';
                                    }
                                    $unit = App\Unit::find($product_transfer_data->transfer_unit_id);
                                ?>
                                @if($unit)
                                    {{$product->name.' ('.$product_transfer_data->qty.' '.$unit->unit_code.')'}}
                                @else
                                    {{$product->name.' ('.$product_transfer_data->qty.')'}}
                                @endif
                                <br>
                                @endforeach
                            </td>
                            <td>{{number_format((float) $transfer->grand_total, 2, '.', '')}}</td>

                            @if($transfer->status == 1)
                            <td><div class="badge badge-success">{{trans('file.Completed')}}</div></td>
                            @elseif($transfer->status == 2)
                            <td><div class="badge badge-danger">{{trans('file.Pending')}}</div></td>
                            @else
                            <td><div class="badge badge-warning">{{trans('file.Sent')}}</div></td>
                            @endif
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
                            <th>0.00</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane fade" id="user-payments">
            <div class="table-responsive mb-4">
                <table id="payment-table" class="table table-hover">
                    <thead>
                        <tr>
                            <th class="not-exported-payment"></th>
                            <th>{{trans('file.Date')}}</th>
                            <th>{{trans('file.reference')}}</th>
                            <th>{{trans('file.Amount')}}</th>
                            <th>{{trans('file.Paid Method')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lims_payment_data as $key=>$payment)
                            <tr>
                                <td>{{$key}}</td>
                                <td>{{date($general_setting->date_format, strtotime($payment->created_at))}}</td>
                                <td>{{$payment->payment_reference}}</td>
                                <td>{{$payment->amount}}</td>
                                <td>{{$payment->paying_method}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="tfoot active">
                        <tr>
                            <th></th>
                            <th>Total:</th>
                            <th></th>
                            <th>0.00</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane fade" id="user-expense">
            <div class="table-responsive mb-4">
                <table id="expense-table" class="table table-hover">
                    <thead>
                        <tr>
                            <th class="not-exported-expense"></th>
                            <th>{{trans('file.Date')}}</th>
                            <th>{{trans('file.reference')}}</th>
                            <th>{{trans('file.Warehouse')}}</th>
                            <th>{{trans('file.category')}}</th>
                            <th>{{trans('file.Amount')}}</th>
                            <th>{{trans('file.Note')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lims_expense_data as $key=>$expense)
                            <tr>
                                <td>{{$key}}</td>
                                <td>{{date($general_setting->date_format, strtotime($expense->created_at))}}</td>
                                <td>{{$expense->reference_no}}</td>
                                <td>{{$expense->warehouse->name}}</td>
                                <td>{{$expense->expenseCategory->name}}</td>
                                <td>{{$expense->amount}}</td>
                                <td>{{$expense->note}}</td>
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
                            <th>0.00</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane fade" id="user-payroll">
            <div class="table-responsive mb-4">
                <table id="payroll-table" class="table table-hover">
                    <thead>
                        <tr>
                            <th class="not-exported-payroll"></th>
                            <th>{{trans('file.Date')}}</th>
                            <th>{{trans('file.reference')}}</th>
                            <th>{{trans('file.Employee')}}</th>
                            <th>{{trans('file.Amount')}}</th>
                            <th>{{trans('file.Method')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lims_payroll_data as $key=>$payroll)
                            <tr>
                                <td>{{$key}}</td>
                                <td>{{date($general_setting->date_format, strtotime($payroll->created_at))}}</td>
                                <td>{{$payroll->reference_no}}</td>
                                <td>{{$payroll->employee->name}}</td>
                                <td>{{$payroll->amount}}</td>
                                @if($payroll->paying_method == 0)
                                <td>Cash</td>
                                @elseif($payroll->paying_method == 1)
                                <td>Cheque</td>
                                @else
                                <td>Credit Card</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="tfoot active">
                        <tr>
                            <th></th>
                            <th>Total:</th>
                            <th></th>
                            <th></th>
                            <th>0.00</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</section>

<script type="text/javascript">
    $("ul#report").siblings('a').attr('aria-expanded','true');
    $("ul#report").addClass("show");
    $("ul#report #user-report-menu").addClass("active");

    $('#user_id').val($('input[name="user_id_hidden"]').val());
    $('.selectpicker').selectpicker('refresh');

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

            $( dt_selector.column( 6 ).footer() ).html(dt_selector.cells( rows, 6, { page: 'current' } ).data().sum().toFixed(2));
            $( dt_selector.column( 7 ).footer() ).html(dt_selector.cells( rows, 7, { page: 'current' } ).data().sum().toFixed(2));
            $( dt_selector.column( 8 ).footer() ).html(dt_selector.cells( rows, 8, { page: 'current' } ).data().sum().toFixed(2));
        }
        else {
            
            $( dt_selector.column( 6 ).footer() ).html(dt_selector.column( 6, {page:'current'} ).data().sum().toFixed(2));
            $( dt_selector.column( 7 ).footer() ).html(dt_selector.cells( rows, 7, { page: 'current' } ).data().sum().toFixed(2));
            $( dt_selector.column( 8 ).footer() ).html(dt_selector.cells( rows, 8, { page: 'current' } ).data().sum().toFixed(2));
        }
    }

    $('#purchase-table').DataTable( {
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
                    columns: ':visible:Not(.not-exported-quotation)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_purchase(dt, true);
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                    datatable_sum_purchase(dt, false);
                },
                footer:true
            },
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_purchase(dt, true);
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                    datatable_sum_purchase(dt, false);
                },
                footer:true
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_purchase(dt, true);
                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                    datatable_sum_purchase(dt, false);
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
            datatable_sum_purchase(api, false);
        }
    } );

    function datatable_sum_purchase(dt_selector, is_calling_first) {
        if (dt_selector.rows( '.selected' ).any() && is_calling_first) {
            var rows = dt_selector.rows( '.selected' ).indexes();

            $( dt_selector.column( 6 ).footer() ).html(dt_selector.cells( rows, 6, { page: 'current' } ).data().sum().toFixed(2));
            $( dt_selector.column( 7 ).footer() ).html(dt_selector.cells( rows, 7, { page: 'current' } ).data().sum().toFixed(2));
            $( dt_selector.column( 8 ).footer() ).html(dt_selector.cells( rows, 8, { page: 'current' } ).data().sum().toFixed(2));
        }
        else {
            $( dt_selector.column( 6 ).footer() ).html(dt_selector.column( 6, {page:'current'} ).data().sum().toFixed(2));
            $( dt_selector.column( 7 ).footer() ).html(dt_selector.column( 7, {page:'current'} ).data().sum().toFixed(2));
            $( dt_selector.column( 8 ).footer() ).html(dt_selector.column( 8, {page:'current'} ).data().sum().toFixed(2));
        }
    }

    $('#quotation-table').DataTable( {
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
                    columns: ':visible:Not(.not-exported-quotation)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_quotation(dt, true);
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                    datatable_sum_quotation(dt, false);
                },
                footer:true
            },
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':visible:Not(.not-exported-quotation)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_quotation(dt, true);
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                    datatable_sum_quotation(dt, false);
                },
                footer:true
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:Not(.not-exported-quotation)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_quotation(dt, true);
                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                    datatable_sum_quotation(dt, false);
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
            datatable_sum_quotation(api, false);
        }
    } );

    function datatable_sum_quotation(dt_selector, is_calling_first) {
        if (dt_selector.rows( '.selected' ).any() && is_calling_first) {
            var rows = dt_selector.rows( '.selected' ).indexes();

            $( dt_selector.column( 6 ).footer() ).html(dt_selector.cells( rows, 6, { page: 'current' } ).data().sum().toFixed(2));
        }
        else {
            
            $( dt_selector.column( 6 ).footer() ).html(dt_selector.column( 6, {page:'current'} ).data().sum().toFixed(2));
        }
    }

    $('#transfer-table').DataTable( {
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
                    columns: ':visible:Not(.not-exported-transfer)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_transfer(dt, true);
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                    datatable_sum_transfer(dt, false);
                },
                footer:true
            },
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':visible:Not(.not-exported-transfer)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_transfer(dt, true);
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                    datatable_sum_transfer(dt, false);
                },
                footer:true
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:Not(.not-exported-transfer)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_transfer(dt, true);
                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                    datatable_sum_transfer(dt, false);
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
            datatable_sum_transfer(api, false);
        }
    } );

    function datatable_sum_transfer(dt_selector, is_calling_first) {
        if (dt_selector.rows( '.selected' ).any() && is_calling_first) {
            var rows = dt_selector.rows( '.selected' ).indexes();

            $( dt_selector.column( 6 ).footer() ).html(dt_selector.cells( rows, 6, { page: 'current' } ).data().sum().toFixed(2));
        }
        else {
            
            $( dt_selector.column( 6 ).footer() ).html(dt_selector.column( 6, {page:'current'} ).data().sum().toFixed(2));
        }
    }

    $('#payment-table').DataTable( {
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
                    columns: ':visible:Not(.not-exported-payment)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_payment(dt, true);
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                    datatable_sum_payment(dt, false);
                },
                footer:true
            },
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_payment(dt, true);
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                    datatable_sum_payment(dt, false);
                },
                footer:true
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_payment(dt, true);
                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                    datatable_sum_payment(dt, false);
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
            datatable_sum_payment(api, false);
        }
    } );

    function datatable_sum_payment(dt_selector, is_calling_first) {
        if (dt_selector.rows( '.selected' ).any() && is_calling_first) {
            var rows = dt_selector.rows( '.selected' ).indexes();

            $( dt_selector.column( 3 ).footer() ).html(dt_selector.cells( rows, 3, { page: 'current' } ).data().sum().toFixed(2));
        }
        else {
            $( dt_selector.column( 3 ).footer() ).html(dt_selector.column( 3, {page:'current'} ).data().sum().toFixed(2));
        }
    }

    $('#expense-table').DataTable( {
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
                    columns: ':visible:Not(.not-exported-expense)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_expense(dt, true);
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                    datatable_sum_expense(dt, false);
                },
                footer:true
            },
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_expense(dt, true);
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                    datatable_sum_expense(dt, false);
                },
                footer:true
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_expense(dt, true);
                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                    datatable_sum_expense(dt, false);
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
            datatable_sum_expense(api, false);
        }
    } );

    function datatable_sum_expense(dt_selector, is_calling_first) {
        if (dt_selector.rows( '.selected' ).any() && is_calling_first) {
            var rows = dt_selector.rows( '.selected' ).indexes();

            $( dt_selector.column( 5 ).footer() ).html(dt_selector.cells( rows, 5, { page: 'current' } ).data().sum().toFixed(2));
        }
        else {
            $( dt_selector.column( 5 ).footer() ).html(dt_selector.column( 5, {page:'current'} ).data().sum().toFixed(2));
        }
    }

    $('#payroll-table').DataTable( {
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
                    columns: ':visible:Not(.not-exported-payroll)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_payroll(dt, true);
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                    datatable_sum_payroll(dt, false);
                },
                footer:true
            },
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_payroll(dt, true);
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                    datatable_sum_payroll(dt, false);
                },
                footer:true
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum_payroll(dt, true);
                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                    datatable_sum_payroll(dt, false);
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
            datatable_sum_payroll(api, false);
        }
    } );

    function datatable_sum_payroll(dt_selector, is_calling_first) {
        if (dt_selector.rows( '.selected' ).any() && is_calling_first) {
            var rows = dt_selector.rows( '.selected' ).indexes();

            $( dt_selector.column( 4 ).footer() ).html(dt_selector.cells( rows, 4, { page: 'current' } ).data().sum().toFixed(2));
        }
        else {
            $( dt_selector.column( 4 ).footer() ).html(dt_selector.column( 4, {page:'current'} ).data().sum().toFixed(2));
        }
    }

$(".daterangepicker-field").daterangepicker({
  callback: function(startDate, endDate, period){
    var start_date = startDate.format('YYYY-MM-DD');
    var end_date = endDate.format('YYYY-MM-DD');
    var title = start_date + ' To ' + end_date;
    $(this).val(title);
    $('input[name="start_date"]').val(start_date);
    $('input[name="end_date"]').val(end_date);
  }
});

</script>
@endsection