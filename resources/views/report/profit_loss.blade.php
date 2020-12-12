@extends('layout.main')
@section('content')
<section>
	<h3 class="text-center">{{trans('file.Summary Report')}}</h3>
	{!! Form::open(['route' => 'report.profitLoss', 'method' => 'post']) !!}
	<div class="col-md-6 offset-md-3 mt-4">
        <div class="form-group row">
            <label class="d-tc mt-2"><strong>{{trans('file.Choose Your Date')}}</strong> &nbsp;</label>
            <div class="d-tc">
                <div class="input-group">
                    <input type="text" class="daterangepicker-field form-control" value="{{$start_date}} To {{$end_date}}" required />
                    <input type="hidden" name="start_date" value="{{$start_date}}" />
                    <input type="hidden" name="end_date" value="{{$end_date}}" />
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">{{trans('file.submit')}}</button>
                    </div>
                </div>
            </div>
        </div> 
    </div>
	{{Form::close()}}
	<div class="container-fluid">
		<div class="row mt-4">
			<div class="col-md-3">
				<div class="colored-box">
					<i class="fa fa-heart"></i>
					<h3>{{trans('file.Purchase')}}</h3>
					<hr>
					<div class="mt-3">
						<p class="mt-2">{{trans('file.Amount')}} <span class="float-right"> {{number_format((float)$purchase[0]->grand_total, 2, '.', '') }}</span></p>
						<p class="mt-2">{{trans('file.Purchase')}} <span class="float-right">{{$total_purchase}}</span></p>
						<p class="mt-2">{{trans('file.Paid')}} <span class="float-right">{{number_format((float)$purchase[0]->paid_amount, 2, '.', '')}}</span></p>
						<p class="mt-2">{{trans('file.Tax')}} <span class="float-right">{{number_format((float)$purchase[0]->tax, 2, '.', '')}}</span></p>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="colored-box">
					<i class="fa fa-shopping-cart"></i>
					<h3>{{trans('file.Sale')}}</h3>
					<hr>
					<div class="mt-3">
						<p class="mt-2">{{trans('file.Amount')}} <span class="float-right"> {{number_format((float)$sale[0]->grand_total, 2, '.', '') }}</span></p>
						<p class="mt-2">{{trans('file.Sale')}} <span class="float-right">{{$total_sale}}</span></p>
						<p class="mt-2">{{trans('file.Paid')}} <span class="float-right">{{number_format((float)$sale[0]->paid_amount, 2, '.', '')}}</span></p>
						<p class="mt-2">{{trans('file.Tax')}} <span class="float-right">{{number_format((float)$sale[0]->tax, 2, '.', '')}}</span></p>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="colored-box">
					<i class="fa fa-random "></i>
					<h3>{{trans('file.Sale Return')}}</h3>
					<hr>
					<div class="mt-3">
						<p class="mt-2">{{trans('file.Amount')}} <span class="float-right"> {{number_format((float)$return[0]->grand_total, 2, '.', '') }}</span></p>
						<p class="mt-2">{{trans('file.Return')}} <span class="float-right">{{$total_return}}</span></p>
						<p class="mt-2">{{trans('file.Tax')}} <span class="float-right">{{number_format((float)$return[0]->tax, 2, '.', '')}}</span></p>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="colored-box">
					<i class="fa fa-random "></i>
					<h3>{{trans('file.Purchase Return')}}</h3>
					<hr>
					<div class="mt-3">
						<p class="mt-2">{{trans('file.Amount')}} <span class="float-right"> {{number_format((float)$purchase_return[0]->grand_total, 2, '.', '') }}</span></p>
						<p class="mt-2">{{trans('file.Return')}} <span class="float-right">{{$total_purchase_return}}</span></p>
						<p class="mt-2">{{trans('file.Tax')}} <span class="float-right">{{number_format((float)$purchase_return[0]->tax, 2, '.', '')}}</span></p>
					</div>
				</div>
			</div>
		</div>
		<div class="row mt-2">
			<div class="col-md-4">
				<div class="colored-box">
					<i class="fa fa-money"></i>
					<h3>{{trans('file.profit')}} / {{trans('file.Loss')}}</h3>
					<hr>
					<div class="mt-3">
						<p class="mt-2">{{trans('file.Sale')}} <span class="float-right">{{number_format((float)$sale[0]->grand_total, 2, '.', '')}}</span></p>
						<p class="mt-2">{{trans('file.Purchase')}} <span class="float-right">- {{number_format((float)$purchase[0]->grand_total, 2, '.', '')}}</span></p>
						<p class="mt-2">{{trans('file.profit')}} <span class="float-right"> {{number_format((float)($sale[0]->grand_total - $purchase[0]->grand_total), 2, '.', '') }}</span></p>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="colored-box">
					<i class="fa fa-money"></i>
					<h3>{{trans('file.profit')}} / {{trans('file.Loss')}}</h3>
					<hr>
					<div class="mt-3">
						<p class="mt-2">{{trans('file.Sale')}} <span class="float-right">{{number_format((float)$sale[0]->grand_total, 2, '.', '')}}</span></p>
						<p class="mt-2">{{trans('file.Purchase')}} <span class="float-right">- {{number_format((float)$purchase[0]->grand_total, 2, '.', '')}}</span></p>
						<p class="mt-2">{{trans('file.Sale Return')}} <span class="float-right">- {{number_format((float)$return[0]->grand_total, 2, '.', '')}}</span></p>
						<p class="mt-2">{{trans('file.Purchase Return')}} <span class="float-right"> {{number_format((float)$purchase_return[0]->grand_total, 2, '.', '')}}</span></p>
						<p class="mt-2">{{trans('file.profit')}} <span class="float-right"> {{number_format((float)($sale[0]->grand_total - $purchase[0]->grand_total - $return[0]->grand_total + $purchase_return[0]->grand_total), 2, '.', '') }}</span></p>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="colored-box">
					<i class="fa fa-money "></i>
					<h3>{{trans('file.Net Profit')}} / {{trans('file.Net Loss')}}</h3>
					<hr>
					<h4 class="text-center">{{number_format((float)(($sale[0]->grand_total-$sale[0]->tax) - ($purchase[0]->grand_total-$purchase[0]->tax) - ($return[0]->grand_total-$return[0]->tax) + ($purchase_return[0]->grand_total-$purchase_return[0]->tax)), 2, '.', '') }}</h4>
					<p class="text-center">
						({{trans('file.Sale')}} {{number_format((float)($sale[0]->grand_total), 2, '.', '')}} - {{trans('file.Tax')}} {{number_format((float)($sale[0]->tax), 2, '.', '')}}) - ({{trans('file.Purchase')}} {{number_format((float)($purchase[0]->grand_total), 2, '.', '')}} - {{trans('file.Tax')}} {{number_format((float)($purchase[0]->tax), 2, '.', '')}}) - ({{trans('file.Return')}} {{number_format((float)($return[0]->grand_total), 2, '.', '')}} - {{trans('file.Tax')}} {{number_format((float)($return[0]->tax), 2, '.', '')}}) + ({{trans('file.Purchase Return')}} {{number_format((float)($purchase_return[0]->grand_total), 2, '.', '')}} - {{trans('file.Tax')}} {{number_format((float)($purchase_return[0]->tax), 2, '.', '')}})
					</p>
				</div>
			</div>
		</div>
		<div class="row mt-2">
			<div class="col-md-3">
				<div class="colored-box">
					<i class="fa fa-dollar"></i>
					<h3>{{trans('file.Payment Recieved')}}</h3>
					<hr>
					<div class="mt-3">
						<p class="mt-2">{{trans('file.Amount')}} <span class="float-right"> {{number_format((float)$payment_recieved, 2, '.', '') }}</span></p>
						<p class="mt-2">{{trans('file.Recieved')}} <span class="float-right">{{$payment_recieved_number}}</span></p>
						<p class="mt-2">Cash <span class="float-right">{{number_format((float)$cash_payment_sale, 2, '.', '')}}</span></p>
						<p class="mt-2">Cheque <span class="float-right">{{number_format((float)$cheque_payment_sale, 2, '.', '')}}</span></p>
						<p class="mt-2">Credit Card <span class="float-right">{{number_format((float)$credit_card_payment_sale, 2, '.', '')}}</span></p>
						<p class="mt-2">Gift Card <span class="float-right">{{number_format((float)$gift_card_payment_sale, 2, '.', '')}}</span></p>
						<p class="mt-2">Paypal <span class="float-right">{{number_format((float)$paypal_payment_sale, 2, '.', '')}}</span></p>
						<p class="mt-2">Deposit <span class="float-right">{{number_format((float)$deposit_payment_sale, 2, '.', '')}}</span></p>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="colored-box">
					<i class="fa fa-dollar"></i>
					<h3>{{trans('file.Payment Sent')}}</h3>
					<hr>
					<div class="mt-3">
						<p class="mt-2">{{trans('file.Amount')}} <span class="float-right"> {{number_format((float)$payment_sent, 2, '.', '') }}</span></p>
						<p class="mt-2">{{trans('file.Recieved')}} <span class="float-right">{{$payment_sent_number}}</span></p>
						<p class="mt-2">Cash <span class="float-right">{{number_format((float)$cash_payment_purchase, 2, '.', '')}}</span></p>
						<p class="mt-2">Cheque <span class="float-right">{{number_format((float)$cheque_payment_purchase, 2, '.', '')}}</span></p>
						<p class="mt-2">Credit Card <span class="float-right">{{number_format((float)$credit_card_payment_purchase, 2, '.', '')}}</span></p>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="colored-box">
					<i class="fa fa-dollar"></i>
					<h3>{{trans('file.Expense')}}</h3>
					<hr>
					<div class="mt-3">
						<p class="mt-2">{{trans('file.Amount')}} <span class="float-right"> {{number_format((float)$expense, 2, '.', '') }}</span></p>
						<p class="mt-2">{{trans('file.Expense')}} <span class="float-right">{{$total_expense}}</span></p>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="colored-box">
					<i class="fa fa-dollar"></i>
					<h3>{{trans('file.Payroll')}}</h3>
					<hr>
					<div class="mt-3">
						<p class="mt-2">{{trans('file.Amount')}} <span class="float-right"> {{number_format((float)$payroll, 2, '.', '') }}</span></p>
						<p class="mt-2">{{trans('file.Payroll')}} <span class="float-right">{{$total_payroll}}</span></p>
					</div>
				</div>
			</div>
		</div>
		<div class="row mt-2">
			<div class="col-md-4 offset-md-4">
				<div class="colored-box">
					<i class="fa fa-dollar"></i>
					<h3>{{trans('file.Cash in Hand')}}</h3>
					<hr>
					<div class="mt-3">
						<p class="mt-2">{{trans('file.Recieved')}} <span class="float-right"> {{number_format((float)($payment_recieved), 2, '.', '') }}</span></p>
						<p class="mt-2">{{trans('file.Sent')}} <span class="float-right">- {{number_format((float)($payment_sent), 2, '.', '') }}</span></p>
						<p class="mt-2">{{trans('file.Sale Return')}} <span class="float-right">- {{number_format((float)$return[0]->grand_total, 2, '.', '')}}</span></p>
						<p class="mt-2">{{trans('file.Purchase Return')}} <span class="float-right"> {{number_format((float)$purchase_return[0]->grand_total, 2, '.', '')}}</span></p>
						<p class="mt-2">{{trans('file.Expense')}} <span class="float-right">- {{number_format((float)$expense, 2, '.', '')}}</span></p>
						<p class="mt-2">{{trans('file.Payroll')}} <span class="float-right">- {{number_format((float)$payroll, 2, '.', '')}}</span></p>
						<p class="mt-2">{{trans('file.In Hand')}} <span class="float-right">{{number_format((float)($payment_recieved - $payment_sent - $return[0]->grand_total + $purchase_return[0]->grand_total - $expense - $payroll), 2, '.', '') }}</span></p>
					</div>
				</div>
			</div>
		</div>
		<div class="row mt-2">
			@foreach($warehouse_name as $key => $name)
				<div class="col-md-4">
					<div class="colored-box">
						<i class="fa fa-money"></i>
						<h3>{{$name}}</h3>
						<h4 class="text-center mt-3">{{number_format((float)($warehouse_sale[$key][0]->grand_total - $warehouse_purchase[$key][0]->grand_total - $warehouse_return[$key][0]->grand_total + $warehouse_purchase_return[$key][0]->grand_total), 2, '.', '') }}</h4>
						<p class="text-center">
							{{trans('file.Sale')}} {{number_format((float)($warehouse_sale[$key][0]->grand_total), 2, '.', '')}} - {{trans('file.Purchase')}} {{number_format((float)($warehouse_purchase[$key][0]->grand_total), 2, '.', '')}} - {{trans('file.Sale Return')}} {{number_format((float)($warehouse_return[$key][0]->grand_total), 2, '.', '')}} + {{trans('file.Purchase Return')}} {{number_format((float)($warehouse_purchase_return[$key][0]->grand_total), 2, '.', '')}}
						</p>
						<hr style="border-color: rgba(0, 0, 0, 0.2);">
						<h4 class="text-center">{{number_format((float)(($warehouse_sale[$key][0]->grand_total - $warehouse_sale[$key][0]->tax) - ($warehouse_purchase[$key][0]->grand_total - $warehouse_purchase[$key][0]->tax) - ($warehouse_return[$key][0]->grand_total - $warehouse_return[$key][0]->tax) + ($warehouse_purchase_return[$key][0]->grand_total - $warehouse_purchase_return[$key][0]->tax) ), 2, '.', '') }}</h4>
						<p class="text-center">
							 {{trans('file.Net Sale')}} {{number_format((float)($warehouse_sale[$key][0]->grand_total - $warehouse_sale[$key][0]->tax), 2, '.', '')}} -  {{trans('file.Net Purchase')}} {{number_format((float)($warehouse_purchase[$key][0]->grand_total - $warehouse_purchase[$key][0]->tax), 2, '.', '')}} - {{trans('file.Net Sale Return')}} {{number_format((float)($warehouse_return[$key][0]->grand_total - $warehouse_return[$key][0]->tax), 2, '.', '')}} + {{trans('file.Net Purchase Return')}} {{number_format((float)($warehouse_purchase_return[$key][0]->grand_total - $warehouse_purchase_return[$key][0]->tax), 2, '.', '')}}
						</p>
						<hr style="border-color: rgba(0, 0, 0, 0.2);">
						<h4 class="text-center">{{number_format((float)$warehouse_expense[$key], 2, '.', '') }}</h4>
						<p class="text-center">{{trans('file.Expense')}}</p>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</section>

<script type="text/javascript">

	$("ul#report").siblings('a').attr('aria-expanded','true');
    $("ul#report").addClass("show");
    $("ul#report #profit-loss-report-menu").addClass("active");

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