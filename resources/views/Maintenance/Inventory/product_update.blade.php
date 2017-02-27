@extends('layouts.maintenance')

@section('content')
	<!--Errors-->	
	@if($errors->any())
		<div class="ui small basic modal" style="text-align:center" id="error">
			<div class="ui icon header">
				<i class="remove icon"></i>
				Error
			</div>
			<div class="content">
				@foreach ($errors->all() as $error)
                	<li>{{ $error }}</li>
              	@endforeach
			</div>
		</div>
		<script type="text/javascript">
			$(document).ready(function (){
				$('#error').modal('show');
			});
		</script>
	@endif

	<h2>Maintenance - Update Product</h2>
	<hr><br>

	<div class="ui form">
		{!! Form::open(['action' => 'ProductController@update']) !!}
			<input type="hidden" name="editProductId" value="{{$product[0]->productId}}" readonly>
			<div class="inline fields">
				<div class="two wide field">
					<label>Brand<span>*</span></label>
				</div>
				<div class="six wide field">
					<div id="brand" class="ui search selection dropdown">
						<input type="hidden" name="editProductBrandId" value="{{$product[0]->productBrandId}}"><i class="dropdown icon"></i>
						<input class="search" autocomplete="off" tabindex="0">
						<div class="default text">Select Brand</div>
						<div class="menu" tabindex="-1">
							@foreach($brand as $brand)
								@if($brand->brandIsActive==1)
									<div class="item" data-value="{{ $brand->brandId }}">{{ $brand->brandName }}</div>
								@endif
							@endforeach
						</div>
					</div>
				</div>
				<div class="two wide field">
					<label>Type<span>*</span></label>
				</div>
				<div class="six wide field">
					<div id="type" class="ui search selection dropdown" title="{{$product[0]->productId}}" onchange="reload(this.title)">
						<input id="drop{{$product[0]->productId}}" type="hidden" name="editProductTypeId" value="{{$product[0]->productTypeId}}"><i class="dropdown icon"></i>
						<input class="search" autocomplete="off" tabindex="0">
						<div class="default text">Select Type</div>
						<div class="menu" tabindex="-1">
							@foreach($type as $type)
								@if($type->typeIsActive==1)
									<div class="item" data-value="{{ $type->typeId }}">{{ $type->typeName }}</div>
								@endif
							@endforeach
						</div>
					</div>
				</div>
			</div>
			<div class="inline fields">
				<div class="two wide field">
					<label>Product<span>*</span></label>
				</div>
				<div class="fourteen wide field">
					<input type="text" name="editProductName" value="{{$product[0]->productName}}" placeholder="Product">
				</div>
			</div>
			<div class="inline fields">
				<div class="two wide field">
					<label>Description</label>
				</div>
				<div class="fourteen wide field">
					<textarea type="text" name="editProductDesc" placeholder="Description" rows="3">{{$product[0]->productDesc}}</textarea>
				</div>
			</div>
			<div class="two fields">
				<div class="field">
					<label>Variances</label>
					<div id="add{{$product[0]->productId}}" style="width:100%" class="ui multiple add search selection dropdown">
						<input id="variances" type="hidden" name="editVariance"><i class="dropdown icon"></i>
						<input class="search" autocomplete="off" tabindex="0">
						<div class="default text">Select Variances</div>
						<div id="menu{{$product[0]->productId}}" class="menu" tabindex="-1">
							@foreach($tv as $tv)
								@if($tv->tvIsActive==1 && $tv->tvTypeId==$product[0]->productTypeId)
									<div class="item" data-value="{{$tv->tvVarianceId}}" id="{{$product[0]->productId}}" title="{{$product[0]->productId}}">{{$tv->variance->varianceSize}}|{{$tv->variance->unit->unitName}}</div>
								@endif
							@endforeach
						</div>
					</div>
				</div>
				<div id="cost{{$product[0]->productId}}" class="field">
					
				</div>
			</div>
			<hr>
			<i>Note:<br> All with <span>*</span> are required fields. <br>All variances that are removed will also be removed in packages and promos.<br>Items inside the transaction will not be deleted.</i>
			<div style="float:right">
				<a href="{{URL::to('/maintenance/product')}}" type="reset" class="ui negative button"><i class="arrow left icon"></i>Back</a>
				<button type="submit" class="ui positive button"><i class="plus icon"></i>Update</button>
			</div>
		{!! Form::close() !!}
	</div>
@stop

@section('scripts')
	<script type="text/javascript">
		$(document).ready(function(){
		    $('#brand.ui.dropdown').dropdown();
		    $('#type.ui.dropdown').dropdown();
		    $('.add.ui.dropdown').dropdown({
		    	onAdd: function(value,text,$addedChoice){
		    		var prod = $addedChoice.attr('title');
		    		$("#cost"+prod).append('<div id="'+value+'" class="inline fields"><div class="four wide field"><label id="'+value+'">'+text+'</label></div><div class="twelve wide field"><div class="ui labeled input"><div class="ui label">Php</div><input id="'+value+'" type="text" name="costs[]" onchange="change(this.id)"></div></div></div>');
		    		$("#cost"+prod).append('<input id="hidden'+value+'" type="hidden" name="'+value+'">');
		    	},
		    	onRemove: function(value, text, $removedChoice){
		    		var prod = $removedChoice.attr('title');
		    		$("#cost"+prod+" div[id="+value+"]").remove();
		    		$("#cost"+prod+" input[id=hidden"+value+"]").remove();
		    		// $("#cost"+prod+" label[id="+value+"]").remove();
		    	}
		    });
		    var variances = [
		    	@foreach($pv as $var)
		    		@if($var->pvIsActive==1)
		    			'{{$var->pvVarianceId}}',
		    		@endif
		    	@endforeach
		    ];
		    $('#add{{$product[0]->productId}}').dropdown('set selected',variances);
		    @foreach($pv as $var)
	    		@if($var->pvIsActive==1)
	    			$('#cost{{$product[0]->productId}} input[id={{$var->pvVarianceId}}]').val({{$var->pvCost}});
	    			$('input[id=hidden{{$var->pvVarianceId}}]').val({{$var->pvCost}});
	    		@endif
	    	@endforeach
		});
		function change(id){
			var value = $("input[id="+id+"]").val();
			$("input[id=hidden"+id+"]").val(value);
		}
		function reload(title){
			$(".item#"+title).remove();
			$("#cost"+title+" div").remove();
			$("#cost"+title+" input").remove();
			$("#cost"+title+" label").remove();
			$("#add"+title).dropdown('clear');
			var id = $("#drop"+title).val();
			$.ajaxSetup({
		        headers: {
		            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		        }
			});
			$.ajax({
				type: "POST",
				url: "{{url('maintenance/product/type')}}",
				data: {'id':id},
				dataType: "JSON",
				success:function(data){
					for(var x=0;x<data.data.length;x++){
						$("#menu"+title).append('<div class="item" data-value="'+data.data[x].variance["varianceId"]+'" id="'+title+'" title="'+title+'">'+data.data[x].variance["varianceSize"]+'|'+data.data[x].variance.unit["unitName"]+'</div>');
					}
				}
			});
		}
	</script>
@stop