<h1>Delivery Details</h1>
<h3>Dear {{$customer}},</h3>
@if($status == 2)
	<p>Your Product is Delivering.</p>
@else
	<p>Your Product is Delivered.</p>
@endif
<p><strong>Sale Reference: </strong>{{$sale_reference}}</p>
<p><strong>Delivery Reference: </strong>{{$delivery_reference}}</p>
<p><strong>Destination: </strong>{{$address}}</p>
@if($delivered_by)
<p><strong>Delivered By: </strong>{{$delivered_by}}</p>
@endif
<p>Thank You</p>