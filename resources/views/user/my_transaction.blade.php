@extends('layout.main')
@section('content')
<section>
	<h4 class="text-center">{{trans('file.My Transactions')}}</h4>
	<div class="table-responsive mt-3">
		<table class="table table-bordered" style="border-top: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
			<thead>
				<tr>
					<th><a href="{{url('my-transactions/'.$prev_year.'/'.$prev_month)}}"><i class="fa fa-arrow-left"></i> {{trans('file.Previous')}}</a></th>
			    	<th colspan="5" class="text-center">{{date("F", strtotime($year.'-'.$month.'-01')).' ' .$year}}</th>
			    	<th><a href="{{url('my-transactions/'.$next_year.'/'.$next_month)}}">{{trans('file.Next')}} <i class="fa fa-arrow-right"></i></a></th>
			    </tr>
			</thead>
		    <tbody>
			    <tr>
				    <td><strong>Sunday</strong></td>
				    <td><strong>Monday</strong></td>
				    <td><strong>Tuesday</strong></td>
				    <td><strong>Wednesday</strong></td>
				    <td><strong>Thrusday</strong></td>
				    <td><strong>Friday</strong></td>
				    <td><strong>Saturday</strong></td>
			    </tr>
			    <?php 
			    	$i = 1;
			    	$flag = 0;
			    	while ($i <= $number_of_day) {
			    		echo '<tr>';
			    		for($j=1 ; $j<=7 ; $j++){
			    			if($i > $number_of_day)
			    				break;

			    			if($flag){
			    				if($year.'-'.$month.'-'.$i == date('Y').'-'.date('m').'-'.(int)date('d'))
			    					echo '<td><p style="color:red"><strong>'.$i.'</strong></p>';
			    				else
			    					echo '<td><p><strong>'.$i.'</strong></p>';

			    				if($sale_generated[$i]) {
			    					echo '<strong>'.trans("file.Sale Generated").':</strong> '.$sale_generated[$i].'<br>';
			    				}
			    				if($sale_grand_total[$i]) {
			    					echo '<strong>'.trans("file.grand total").':</strong> '.$sale_grand_total[$i].'<br><br>';
			    				}
			    				if($purchase_generated[$i]) {
			    					echo '<strong>'.trans("file.Purchase Generated").':</strong> '.$purchase_generated[$i].'<br>';
			    				}
			    				if($purchase_grand_total[$i]) {
			    					echo '<strong>'.trans("file.grand total").':</strong> '.$purchase_grand_total[$i].'<br><br>';
			    				}
			    				if($quotation_generated[$i]) {
			    					echo '<strong>'.trans("file.Quotation Generated").':</strong> '.$quotation_generated[$i].'<br>';
			    				}
			    				if($quotation_grand_total[$i]) {
			    					echo '<strong>'.trans("file.grand total").':</strong> '.$quotation_grand_total[$i].'<br><br>';
			    				}
			    				echo '</td>';
			    				$i++;
			    			}
			    			elseif($j == $start_day){
			    				if($year.'-'.$month.'-'.$i == date('Y').'-'.date('m').'-'.(int)date('d'))
			    					echo '<td><p style="color:red"><strong>'.$i.'</strong></p>';
			    				else
			    					echo '<td><p><strong>'.$i.'</strong></p>';

			    				if($sale_generated[$i]) {
			    					echo '<strong>'.trans("file.Sale Generated").':</strong> '.$sale_generated[$i].'<br>';
			    				}
			    				if($sale_grand_total[$i]) {
			    					echo '<strong>'.trans("file.grand total").':</strong> '.$sale_grand_total[$i].'<br><br>';
			    				}
			    				if($purchase_generated[$i]) {
			    					echo '<strong>'.trans("file.Purchase Generated").':</strong> '.$purchase_generated[$i].'<br>';
			    				}
			    				if($purchase_grand_total[$i]) {
			    					echo '<strong>'.trans("file.grand total").':</strong> '.$purchase_grand_total[$i].'<br><br>';
			    				}
			    				if($quotation_generated[$i]) {
			    					echo '<strong>'.trans("file.Quotation Generated").':</strong> '.$quotation_generated[$i].'<br>';
			    				}
			    				if($quotation_grand_total[$i]) {
			    					echo '<strong>'.trans("file.grand total").':</strong> '.$quotation_grand_total[$i].'<br><br>';
			    				}
			    				echo '</td>';
			    				$flag = 1;
			    				$i++;
			    				continue;
			    			}
			    			else {
			    				echo '<td></td>';
			    			}
			    		}
			    	    echo '</tr>';
			    	}
			    ?>
		    </tbody>
		</table>
	</div>	
</section>

<script type="text/javascript">

</script>
@endsection