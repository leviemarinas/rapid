@extends('layouts.master')

@section('content')	
	<!--New-->	
	@if(Session::has('flash_message'))
		<div class="ui small basic modal" style="text-align:center" id="flash_message">
			<div class="ui icon header">
				<i class="check icon"></i>
				Success
			</div>
			<div class="content">
				<em>{!! session('flash_message') !!}</em>
			</div>
		</div>
		<script type="text/javascript">
			$(document).ready(function (){
				$('#flash_message').modal('show');
				setTimeout(function () {
                    $("#flash_message").modal('hide');
                }, 2000);
			});
		</script>
	@endif

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

	@if(Session::has('new_error'))
		<script type="text/javascript">
			$(document).ready(function (){
				$('#modalNew').modal('show');
			});
		</script>
	@endif

	@if(Session::has('update_error'))
		<script type="text/javascript">
			$(document).ready(function (){
				$("#edit{!! session('update_error') !!}").modal('show');
			});
		</script>
	@endif

	<h2>Maintenance - Discount</h2>
	<hr><br>
	<button class="ui primary button" name="modalNew" onclick="modal(this.name)"><i class="plus icon"></i>New Discount</button>
	<br><br>
	<table id="list" class="ui celled three column table">
		<thead>
			<tr>
				<th>Discount</th>
				<th>Rate</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			@foreach($discount as $discount)
				@if($discount->discountIsActive==1)
					<tr>
						<td>{{$discount->discountName}}</td>
						<td>{{$discount->discountRate}}%</td>
						<td>
							<button class="ui primary basic circular icon button" data-tooltip="Update Record" data-inverted="" name="edit{{ $discount->discountId }}" onclick="modal(this.name)"><i class="write icon"></i></button>
							<button class="ui red basic circular icon button" data-tooltip="Deactivate Record" data-inverted="" name="del{{ $discount->discountId }}" onclick="modal(this.name)"><i class="trash icon"></i></button>
						</td>
						<!--Modal for Update-->
						<div class="ui small modal" id="edit{{ $discount->discountId }}">
							<div class="header">Update Discount</div>
							{!! Form::open(['action' => 'DiscountController@update']) !!}	
								<div class="content">
									<div class="description">
										<div class="ui form">
											@if(Session::has('update_error'))
												@if($errors->any())
													<div class="ui negative message">
														<h3>Something went wrong!</h3>
														{!! implode('', $errors->all(
															'<li>:message</li>'
														)) !!}
													</div>
												@endif
											@endif
											@if(Session::has('update_unique'))
												<div class="ui negative message">
													<h3>Something went wrong!</h3>
													<li>Discount already exists. Update failed.</li>
												</div>
											@endif									
											<div class="sixteen wide field">
				        						<input type="hidden" name="editDiscountId" value="{{ $discount->discountId }}">
				        					</div>
					        				<div class="inline fields">
					        					<div class="two wide field">
					        						<label>Discount<span>*</span></label>
					        					</div>
					        					<div class="fourteen wide field">
					        						<input maxlength="140" type="text" name="editDiscountName" value="{{ $discount->discountName }}" placeholder="Product Type">
					        					</div>
					        				</div>
					        				<div class="inline fields">
					        					<div class="two wide field">
					        						<label>Rate<span>*</span></label>
					        					</div>
					        					<div class="fourteen wide field">
					        						<input maxlength="3" type="text" name="editDiscountRate" value="{{ $discount->discountRate }}" placeholder="Rate">
					        					</div>
					        				</div>
										</div>
									</div>
								</div>
								<div class="actions">
									<i>Note: All with <span>(*)</span> are required fields</i>
		        					<button type="reset" class="ui negative button"><i class="remove icon"></i>Clear</button>
		        					<button type="submit" class="ui primary button"><i class="write icon"></i>Update</button>
		        				</div>
	        				{!! Form::close() !!}
						</div>
						<!--Modal for Deactivate-->
						<div class="ui small basic modal" id="del{{ $discount->discountId }}" style="text-align:center">
							<div class="ui icon header">
								<i class="trash icon"></i>
								Deactivate
							</div>
							{!! Form::open(['action' => 'DiscountController@destroy']) !!}
								<div class="content">
									<div class="description">
										<p>
											<input type="hidden" name="delDiscountId" value="{{ $discount->discountId }}">
											<p>
												<label>Discount: {{$discount->discountName}}</label><br>
												<label>Description: {{$discount->discountRate}}</label><br>
											</p>
										</p>
									</div>									
								</div>
								<div class="actions">
			        				<button type="submit" class="ui negative button"><i class="trash icon"></i>Deactivate</button>
			        				<button type="reset" class="ui positive button"><i class="arrow left icon"></i>Cancel</button>
			        			</div>
							{!! Form::close() !!}
						</div>
					</tr>
				@endif
			@endforeach
		</tbody>
	</table>
	
	<!--New Modal-->
	<div class="ui small modal" id="modalNew">
		<div class="header">New Discount</div>
		<div class="content">
			<div class="description">
				<div class="ui form">
					{!! Form::open(['action' => 'DiscountController@create']) !!}
						<div class="ui error message"></div>
						@if($errors->any())
							<div class="ui negative message">
								<h3>Something went wrong!</h3>
								{!! implode('', $errors->all(
									'<li>:message</li>'
								)) !!}
							</div>
						@endif
						<input type="hidden" name="discountId" value="{{$newId}}" readonly>
	    				<div class="inline fields">
	    					<div class="two wide field">
	    						<label>Discount<span>*</span></label>
	    					</div>
	    					<div class="fourteen wide field">
	    						<input maxlength="140" type="text" name="discountName" placeholder="Discount" value="{{old('discountName')}}">
	    					</div>
	    				</div>
	    				<div class="inline fields">
	    					<div class="two wide field">
	    						<label>Rate<span>*</span></label>
	    					</div>
	    					<div class="fourteen wide field">
	    						<input maxlength="3" type="text" name="discountRate" placeholder="Rate" value="{{old('discountRate')}}">
	    					</div>
	    				</div>
	    				<div class="actions">
	    					<i>Note: All with <span>(*)</span> are required fields</i>
	    					<button type="reset" class="ui negative button"><i class="remove icon"></i>Clear</button>
	    					<button type="submit" class="ui primary button"><i class="plus icon"></i>Save</button>
	    				</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
@stop


@section('scripts')
	<script type="text/javascript">
		$(document).ready(function(){
			$('#mTitle').attr('class','title header active');
			$('#mContent').attr('class','content active');
			$('#smTitle').attr('class','title header active');
			$('#smContent').attr('class','content active');
		    $('#list').DataTable();
		    $('.ui.form').form({
			    fields: {
			    	discountName: 'empty',
			    	discountRate: 'empty',
			  	}
			});
			$('.ui.small.modal').form({
			    fields: {
			    	editDiscountName: 'empty',
			    	editDiscountRate: 'empty',
			  	}
			});
		});
		function modal(open){
			$('#' + open + '').modal('show').modal({
				closable: false
			});
		}
	</script>
@stop