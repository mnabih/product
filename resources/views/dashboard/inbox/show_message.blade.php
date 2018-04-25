@extends('dashboard.layout.master')
	
	<!-- style -->
	@section('style')

	@endsection
	<!-- /style -->
@section('title','عرض رساله '.$message->name)
@section('content')
<div class="panel panel-flat">
	<div class="panel-heading">
		<h5 class="panel-title">عرض رساله :{{$message->name}}</h5>
		<div class="heading-elements">
			<ul class="icons-list">
        		<li><a data-action="collapse"></a></li>
        		<li><a data-action="reload"></a></li>
        	</ul>
    	</div>
	</div>
	<div class="panel panel-flat">
		<div class="panel-body">
			<div class="row text-center">
				<div class="col-sm-12 alert alert-success">
					<div class="col-sm-3">اسم الراسل : {{$message->name}} </div>
					<div class="col-sm-3">البريد : {{$message->email}}</div>
					<div class="col-sm-3">الهاتف : {{$message->phone}}</div>
					<div class="col-sm-3">التاريخ : {{$message->created_at->diffForHumans()}}</div>
				</div>
				
				<br>
				<div class="col-sm-12" style="margin-top: 20px;margin-bottom: 25px">
					{{$message->message}}
				</div>
				<div class="col-sm-12" style="margin-top:20px" >
					<div class="btn btn-danger col-sm-3">حذف <i style="color: #fff" class=" icon-trash"></i> </div>

					<div class="btn btn-primary col-sm-3 SMS" 
						data-toggle="modal" 
						data-target="#exampleModalSMS" 
						data-phone="{{$message->phone}}" 
						data-email="{{$message->email}}"
						data-name="{{$message->name}}">
						رد برساله SMS <i class="icon-mobile2"></i>
					</div>

					<div class="btn btn-success col-sm-3 EMAIL"
						data-toggle="modal"
						data-target="#exampleModalEmail"
						data-phone="{{$message->phone}}"
						data-email="{{$message->email}}" 
						data-name="{{$message->name}}">
						رد برساله Email <i class="icon-mail5"></i>
					</div>

					<div class="btn btn-warning col-sm-3"><a style="color: #fff" href="{{route('inbox')}}">عوده لصندوق الوارد <i class="icon-enter5"></i> </a></div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- SMS Modal -->
<div class="modal fade" id="exampleModalSMS" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
		    <h5 class="modal-title" id="exampleModalLabel">ارسال رساله SMS لـ<span class="reverName"></span></h5>
		  </div>
		  <div class="modal-body">
		    <div class="row">
		    	<form action="{{route('sendsms')}}" method="POST" enctype="multipart/form-data">
		    		{{csrf_field()}}
		    		<input type="hidden" name="phone" value="">
		    		<div class="col-sm-12">
		    			<textarea rows="15" name="sms_message" class="form-control" placeholder="نص الرساله "></textarea>
		    		</div>

			        <div class="col-sm-12" style="margin-top: 10px">
				      	<button type="submit" class="btn btn-primary addCategory"">ارسال</button>
				        <button type="button" class="btn btn-secondary" data-dismiss="modal">أغلاق</button>
			        </div>

		    	</form>
		    </div>
		  </div>

		</div>
	</div>
</div>
<!-- /SMS Modal -->

<!-- Email Modal -->
<div class="modal fade" id="exampleModalEmail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
		    <h5 class="modal-title" id="exampleModalLabel">ارسال رساله Email لـ<span class="reverName"></span></h5>
		  </div>
		  <div class="modal-body">
		    <div class="row">
		    	<form action="{{route('sendemail')}}" method="POST" enctype="multipart/form-data">
		    		{{csrf_field()}}
		    		<input type="hidden" name="email">
		    		<input type="hidden" name="name">
		    		<div class="col-sm-12">
		    			<textarea rows="15" name="email_message" class="form-control" placeholder="نص الرساله "></textarea>
		    		</div>

			        <div class="col-sm-12" style="margin-top: 10px">
				      	<button type="submit" class="btn btn-primary addCategory"">ارسال</button>
				        <button type="button" class="btn btn-secondary" data-dismiss="modal">أغلاق</button>
			        </div>

		    	</form>
		    </div>
		  </div>

		</div>
	</div>
</div>
<!-- /Email Modal -->
	
<!-- javascript -->
@section('script')
<script>
	//put phone in the modal
	$(document).on('click','.SMS',function(){
		$('input[name="phone"]').val($(this).data('phone'));
		$('.reverName').text($(this).data('name'))
	});

	//put email in the modal
	$(document).on('click','.EMAIL',function(){
		$('input[name="email"]').val($(this).data('email'));
		$('input[name="name"]').val($(this).data('name'));
		$('.reverName').text($(this).data('name'))
	});
</script>
@endsection


@endsection