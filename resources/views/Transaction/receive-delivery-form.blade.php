@extends('layouts.master')

@section('content')
	<!--Update Failed-->
	@if(Session::has('error_message'))
		<div class="ui small basic modal" style="text-align:center" id="error_message">
			<div class="ui icon header">
				<i class="remove icon"></i>
				Failed
			</div>
			<div class="content">
				<em>{!! session('error_message') !!}</em>
			</div>
		</div>
		<script type="text/javascript">
			$(document).ready(function (){
				$('#error_message').modal('show');
			});
		</script>
	@endif

	<h2>Transaction - New Purchase Delivery</h2>
	<hr><br>

	<div class="ui form">
		{!! Form::open(['action' => 'ReceiveDeliveryController@create']) !!}
			<input type="hidden" name="deliveryId" value="{{$newId}}" readonly>
			<div class="field">
				<label style="font-weight: bold">No. {{$newId}}</label>
			</div>
			<div class="inline fields">
				<div class="two wide field">
					<label>Supplier<span>*</span></label>
				</div>
				<div class="six wide field">
					<div style="width:100%" id="supplier" class="ui search selection dropdown" onchange="supplier()">
						<input id="supplierId" type="hidden" name="deliverySupplierId"><i class="dropdown icon"></i>
						<input class="search" autocomplete="off" tabindex="0">
						<div class="default text">Select Supplier</div>
						<div class="menu" tabindex="-1">
							@foreach($supplier as $supplier)
								<div class="item" data-value="{{ $supplier->supplierId }}">{{ $supplier->supplierName }}</div>
							@endforeach
						</div>
					</div>
				</div>
				<div class="four wide field"></div>
				<div class="two wide field">
					<label>Date</label>
				</div>
				<div class="two wide field">
					<?php echo date("F d, Y"); ?>
				</div>
			</div>
            <div class="inline fields">
                <div class="two wide field">
					<label>Purchase Orders<span>*</span></label>
				</div>
				<div class="fourteen wide field">
					<div style="width:100%" id="deliveryOrder" class="ui multiple search selection dropdown">
						<input id="orderId" type="hidden" name="deliveryOrderId"><i class="dropdown icon"></i>
						<input class="search" autocomplete="off" tabindex="0">
						<div class="default text">Select Orders</div>
						<div id="menuPO" class="menu" tabindex="-1">
						</div>
					</div>
				</div>
            </div>
			<table id="list" class="ui celled four column table">
				<thead>
					<tr>
						<th>Quantity Ordered</th>
						<th>Product</th>
						<th>Description</th>
						<th>Quantity Received</th>
					</tr>
				</thead>
				<tbody id="tableInsert"></tbody>
			</table>
			<!-- <div class="inline fields">
				<div class="two wide field">
					<label>Total cost: PhP</label>
				</div>
				<div class="eight wide field">
					<input id="totalCost" style="border:none;font-weight: bold" type="text" name="totalCost" value="0.00" readonly>
					<input id="totalCosts" style="border:none;font-weight: bold" type="hidden" name="totalCosts" value="0" readonly>
				</div>
			</div> -->
			<br>
			<hr>
			<i>Note: All with <span>*</span> are required fields</i>
			<div style="float:right">
				<a href="{{URL::to('/transaction/receive-delivery')}}" type="reset" class="ui negative button"><i class="arrow left icon"></i>Back</a>
				<button type="submit" class="ui primary button"><i class="plus icon"></i>Save</button>
			</div>
		{!! Form::close() !!}
	</div>
@stop

@section('scripts')
	<script type="text/javascript">
		$(document).ready(function(){
			var t = $('#list').DataTable({
				pageLength: 100,
				paging: false,
				info: false,
				ordering: false,
			});
			$('#tTitle').attr('class','title header active');
$('#tContent').attr('class','content active');
$('#stTitle').attr('class','title header active');
$('#stContent').attr('class','content active');
$('#tiTitle').attr('class','title active');
			$('#tiContent').attr('class','content active');
			$('#stiTitle').attr('class','title active');
			$('#stiContent').attr('class','content active');
			$('#supplier.ui.dropdown').dropdown();
            $('#deliveryOrder.ui.dropdown').dropdown({
            	onAdd:function(value,text,$addedChoice){
					$.ajaxSetup({
				        headers: {
				            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				        }
					});
					$.ajax({
						type: "POST",
						url: "{{url('transaction/receive-delivery/order')}}",
						data: {'id':value},
						dataType: "JSON",
						success:function(data){
							console.log(data);
							for(var x=0;x<data.data.length;x++){
								if(data.data[x].purchaseDQty!=data.data[x].purchaseDeliveredQty){
									var qty = eval(data.data[x].purchaseDQty+"-"+data.data[x].purchaseDeliveredQty);
									t.row.add( [
							            '<div title="'+value+'" class="ui fluid right labeled input"><div></div><input title="'+data.data[x].variance.pvId+'" style="border:none;text-align:right" type="text" value="'+qty+'" readonly><div class="ui label">pcs.</div></div><input type="hidden" name="variances[]" value="'+data.data[x].variance.pvId+'">',
							            data.data[x].variance.product.brand.brandName+' - '+data.data[x].variance.product.productName+' | '+data.data[x].variance.variance.varianceSize+' - '+data.data[x].variance.variance.unit.unitName+'| '+data.data[x].variance.product.types.typeName,
							            data.data[x].purchaseDRemarks+" | "+value,
							            '<div class="ui fluid right labeled input"><input data="'+data.data[x].variance.pvId+'" id="'+value+data.data[x].variance.pvId+'" style="text-align:right" value="0" name="qty'+value+'[]" onkeypress="return validate(event,this.id)" type="number" min="0" max="'+qty+'" maxlength="3" data-content="Only numerical values are allowed" required><div class="ui label">pcs.</div></div>',
							        ] ).draw( false );
								}
								// var counter = $("input[title="+data.data[x].variance.pvId+"]").val();
								// if(counter==null || counter==''){
									
								// }
								// else{
								// 	counter = eval(counter+"+"+data.data[x].purchaseDQty);
								// 	$("input[title="+data.data[x].variance.pvId+"]").val(counter);
								// 	$("input[data="+data.data[x].variance.pvId+"]").attr("max",counter);
								// }
							}
						}
					});
				},
				onRemove:function(value,text,$removedChoice){
					t.row( $('div[title='+value+']').parents('tr') ).remove().draw();
				}
            });
			$('.ui.form').form({
			    fields: {
			    	deliverySupplierId: 'empty',
			    	deliveryProductId: 'empty',
			  	}
			});
		});
		function supplier(){
			$(".item.choice").remove();
			$("#deliveryOrder").dropdown('clear');
 			var table = $('#list').DataTable();
			table.clear().draw();
 			var id = $("#supplierId").val();
 			$.ajaxSetup({
		        headers: {
		            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		        }
			});
			$.ajax({
				type: "POST",
				url: "{{url('transaction/receive-delivery/supplier')}}",
				data: {'id':id},
				dataType: "JSON",
				success:function(data){
					for(var x=0;x<data.data.length;x++){
						$("#menuPO").append('<div class="item choice" data-value="'+data.data[x].purchaseHId+'">'+data.data[x].purchaseHId+'</div>');
					}
				}
			});
		}
		function addRow(value,text,cost){
			var t = $('#list').DataTable();
			t.row.add( [
	            '<div class="ui fluid input"><input id="'+value+'" name="qty[]" onchange="compute(this.value,this.id)" onkeypress="return validate(event,this.id)" type="text" maxlength="3" data-content="Only numerical values are allowed" required></div>',
	            text,
	            '<div class="ui fluid input"><input name="desc[]" type="text"></div>',
	            // '<div class="ui fluid input"><input id="cost'+value+'" style="border:none" type="text" value="'+cost+'" readonly></div>',
	            // '<div class="ui fluid input"><input id="total'+value+'" style="border:none" type="text" value="0" readonly></div>',
	            '<span id="'+value+'" onclick="removeRowd(this.id)" class="ui circular icon negative button deleteRow"><i class="ui remove icon"></i></span>',
	        ] ).draw( false );
		}
		function compute(value,idx){
			var cost = $('input[id=cost'+idx+']').val();
			var computed = cost*value;
			var minus = $('input[id=total'+idx+']').val();
			$('input[id=total'+idx+']').val(computed);
			var total = eval($('#totalCosts').val()+"+"+computed+"-"+minus).toFixed(2);
			$('#totalCost').val(total.toLocaleString('en_PH'));
			$('#totalCosts').val(total);
		}
		function removeRow(value){
			var totalCost = $('#totalCosts').val();
			var cost = $('input[id=total'+value+']').val();
			totalCost = eval(totalCost+"-"+cost).toFixed(2);
			$('#totalCost').val(totalCost);
			$('#totalCosts').val(totalCost);
			var t = $('#list').DataTable();
		    t.row( $('input#'+value).parents('tr') ).remove().draw();
		}
		function removeRowd(value){
			var array = [value,"hello"];
			$('#product').dropdown('remove selected',array);
		}
		function validate(event, idx){
			var char = String.fromCharCode(event.which);
            var patt = /\d/;
            var res = patt.test(char);
            if (!res) {
                $("input[id="+idx+"]").popup('show');
                return false;
            }
            else {
                $("input[id="+idx+"]").popup('hide');
            }
		}
	</script>
@stop