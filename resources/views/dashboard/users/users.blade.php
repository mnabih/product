@extends('dashboard.layout.master')

<!-- style -->
@section('style')
	<style type="text/css">
		.modal .icon-camera
		{
			font-size: 100px;
			color: #797979
		}

		.modal input
		{
			margin-bottom: 4px
		}

		.reset
		{
			border:none;
			background: #fff;
			margin-right: 11px;
		}

		.icon-trash
		{
			margin-left: 8px;
			color: red;
		}

		.dropdown-menu
		{
			min-width: 88px;
		}

		#hidden
		{
			display: none;
		}
	</style>
@endsection
<!-- /style -->
@section('content')
	<div class="panel panel-flat">
		<div class="panel-heading">
			<h5 class="panel-title">قائمة الاعضاء</h5>
			<div class="heading-elements">
				<ul class="icons-list">
					{{--<li><a data-action="collapse"></a></li>--}}
					<li><a data-action="reload"></a></li>
					<!-- <li><a data-action="close"></a></li> -->
				</ul>
			</div>
		</div>

		<!-- buttons -->
		<div class="panel-body">
			<div class="row">
				<div class="col-xs-4">
					<button class="btn bg-blue btn-block btn-float btn-float-lg openAddModal" type="button" data-toggle="modal" data-target="#exampleModal"><i class="icon-plus3"></i> <span>اضافة عضو</span></button>
				</div>
				<div class="col-xs-4">
					<button class="btn bg-purple-300 btn-block btn-float btn-float-lg" type="button"><i class="icon-list-numbered"></i> <span>عدد الاعضاء : {{count($users)}} </span> </button></div>


				<div class="col-xs-4	">
					<a href="{{route('logout')}}" class="btn bg-warning-400 btn-block btn-float btn-float-lg" type="button"><i class="icon-switch"></i> <span>خروج</span></a>
				</div>
			</div>
		</div>
		<!-- /buttons -->

		<table class="table datatable-basic">
			<thead>
			<tr>
				<th>الصوره</th>
				<th>الاسم</th>
				<th>البريد</th>
				<th>الهاتف</th>
				<th>الوظيفة</th>
				<th>الحاله</th>
				<th>تاريخ الاضافه</th>
				<th>التحكم</th>
			</tr>
			</thead>
			<tbody>
			@foreach($users as $u)
				<tr>
					<td><img src="{{asset('public/dashboard/uploads/users/'.$u->avatar)}}" style="width:40px;height: 40px" class="img-circle" alt=""></td>
					<td>{{$u->name}}</td>
					<td>{{$u->email}}</td>
					<td>{{$u->phone}}</td>
					@if(is_null($u->Role))

						@if($u->is_provider == 1)
							<td>مندوب</td>
						@elseif($u->is_provider == 0)
							<td>عميل</td>
						@endif

					@else
						<td>{{$u->Role->role}}</td>
					@endif

					@if($u->active == 0)
						<td><span class="label label-danger">حظر</span></td>
					@else
						<td><span class="label label-success">نشط</span></td>
					@endif
					<td>{{$u->created_at->diffForHumans()}}</td>
					<td>
						<ul class="icons-list">
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<i class="icon-menu9"></i>
								</a>

								<ul class="dropdown-menu dropdown-menu-right">
									<!-- edit button -->
									<li>
										<a href="#" data-toggle="modal" data-target="#exampleModal2" class="openEditmodal"
										   data-id="{{$u->id}}"
										   data-phone="{{$u->phone}}"
										   data-name="{{$u->name}}"
										   data-email="{{$u->email}}"
										   data-photo="{{$u->avatar}}"
										   data-active="{{$u->active}}"
										   data-permission="{{$u->role}}"
										   data-country_id="{{$u->country_id}}"
										   data-is_provider="{{$u->is_provider}}"
										   data-city_id="{{$u->city_id}}">
											<i class="icon-pencil7"></i>تعديل
										</a>
									</li>
									<!-- send message button -->
								{{-- <li>
									<a href="#" data-toggle="modal" data-target="#exampleModal4" class="SendMessageUser"
									data-id="{{$u->id}}"
									data-name="{{$u->name}}"
									data-phone="{{$u->phone}}"
									data-device_id="{{$u->device_id}}"
									data-email="{{$u->email}}">
									<i class=" icon-bubble9"></i>مراسله
									</a>
								</li> --}}
								<!-- show orders -->
								@if(is_null($u->Role))
										<form action="{{route('orderUser')}}" method="POST">
											{{csrf_field()}}
											<input type="hidden" name="id" value="{{$u->id}}">
											<li><button type="submit" class="reset" style="margin-right: 9px;
    											margin-bottom: 6px;"><i class="icon-pencil7"></i>  الطلبات </button></li>
										</form>
								@endif
								<!-- delete button -->
									<form action="{{route('deleteuser')}}" method="POST">
										{{csrf_field()}}
										<input type="hidden" name="id" value="{{$u->id}}">
										<li><button type="submit" class="generalDelete reset"><i class="icon-trash"></i>حذف</button></li>
									</form>
								</ul>
							</li>
						</ul>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>

		<!-- Add user Modal -->
		<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">أضافة عضو جديد</h5>
					</div>
					<div class="modal-body">
						<div class="row">
							<form action="{{route('adduser')}}" method="POST" enctype="multipart/form-data">
								{{csrf_field()}}

								<div class="row">
									<div class="col-sm-3 text-center">
										<label style="margin-bottom: 0">اختيار صوره</label>
										<i class="icon-camera"  onclick="addChooseFile()" style="cursor: pointer;"></i>
										<div class="images-upload-block">
											<input type="file" name="avatar" class="image-uploader" id="hidden">
										</div>
									</div>
									<div class="col-sm-9" style="margin-top: 20px">
										<input type="text" name="name" class="form-control" placeholder="الاسم" style="margin-bottom: 10px">
										<input type="text" name="email" class="form-control" placeholder="البريد ">

										<select name="country_id" id="" class="form-control country" style="margin-bottom: 10px">
											<option value="">قم باختيار الدولة</option>
											@foreach($countries as $country)
												<option value="{{$country->id}}">{{$country->name_ar}}</option>
											@endforeach
										</select>

										<select name="city_id" id="" class="form-control city" style="margin-bottom: 10px">
											<option value="">قم باختيار المدينة</option>
											@foreach($cities as $city)
												<option value="{{$city->id}}">{{$city->name_ar}}</option>
											@endforeach
										</select>


									</div>
								</div>

								<div class="row">
									<div class="col-sm-6">
										<input type="number" name="phone" class="form-control" placeholder="الهاتف ">
										<input type="text" name="password" class="form-control" placeholder="الرقم السرى ">
									</div>

									<div class="col-sm-6">
										<select name="is_provider" class=" form-control" id="">
											<option value="0">عميل</option>
											<option value="1">مندوب</option>
										</select>
										<select name="role" class=" form-control" id="permissions" style="margin-top: 5px">
											<option value="">عضو</option>
											@foreach($roles as $role)
												<option value="{{$role->id}}">{{$role->role}}</option>
											@endforeach
										</select>
										<div style="margin-top: 13px">
											<label class="checkbox" style="margin-bottom: 0">
												<label style="padding-right: 0"> حظر</label>
												<input type="checkbox" name="active" value="0">
												<i class="icon-checkbox"></i>
											</label>
										</div>
									</div>
								</div>

								<div class="col-sm-12" style="margin-top: 10px">
									<button type="submit" class="btn btn-primary addCategory"">اضافه</button>
									<button type="button" class="btn btn-secondary" data-dismiss="modal">أغلاق</button>
								</div>

							</form>
						</div>
					</div>

				</div>
			</div>
		</div>
		<!-- /Add user Modal -->

		<!-- Edit user Modal -->
		<div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel"> تعديل عضو : <span class="userName"></span> </h5>
					</div>
					<div class="modal-body">
						<form action="{{route('updateuser')}}" method="post" enctype="multipart/form-data">

							<!-- token and user id -->
							{{csrf_field()}}
							<input type="hidden" name="id" value="">
							<!-- /token and user id -->
							<div class="row">
								<div class="col-sm-3 text-center">
									<label >اختيار صوره</label>
									<img src="" class="photo" style="width: 120px;height: 120px;cursor: pointer" onclick="ChooseFile()">
									<input type="file" name="edit_photo" style="display: none;">
								</div>
								<div class="col-sm-9" style="margin-top: 10px">
									<label>الاسم</label>
									<input type="text" name="edit_name" class="form-control">
									<label>البريد</label>
									<input type="text" name="edit_email" class="form-control">
									<label>الدولة</label>
									<select name="edit_country_id" id="" class="form-control country" style="margin-bottom: 10px">
										<option value="">قم باختيار الدولة</option>
										@foreach($countries as $country)
											<option value="{{$country->id}}">{{$country->name_ar}}</option>
										@endforeach
									</select>
									<label>المدينة</label>
									<select name="edit_city_id" id="" class="form-control city" style="margin-bottom: 10px">
										<option value="">قم باختيار المدينة</option>
										@foreach($cities as $city)
											<option value="{{$city->id}}">{{$city->name_ar}}</option>
										@endforeach
									</select>

								</div>
							</div>
							<div class="row">

								<div class="col-sm-6" style="margin-top: 5px">
									<label>الهاتف</label>
									<input type="number" name="edit_phone" class="form-control">
									<label>الرقم السرى</label>
									<input type="text" name="edit_password" class="form-control">
								</div>

								<div class="col-sm-6 " style="margin-top:9px">
									<select name="edit_is_provider" class=" form-control" id="">
										<option value="0">عميل</option>
										<option value="1">مندوب</option>
									</select>

									<select name="role" class="form-control" id="permissions" style="margin-top: 22px">
										<option value="">عضو</option>
										@foreach($roles as $role)
											<option value="{{$role->id}}">{{$role->role}}</option>
										@endforeach
									</select>
									<div style="margin-top: 30px">
										<label class="checkbox" style="margin-bottom: 0">
											<label style="padding-right: 0"> حظر</label>
											<input type="checkbox" name="active" id="editActive"  value="0">
											<i class="icon-checkbox"></i>
										</label>
									</div>
								</div>

								<div class="col-sm-12" style="margin-top: 10px">
									<button type="submit" class="btn btn-primary" >حفظ التعديلات</button>
									<button type="button" class="btn btn-secondary" data-dismiss="modal">أغلاق</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- /Edit user Modal -->

		<!-- correspondent for all users Modal -->
	{{-- <div class="modal fade" id="exampleModal3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
			    <h5 class="modal-title" id="exampleModalLabel">مراسلة جميع الاعضاء</span></h5>
			  </div>
			  <div class="modal-body">
			    <div class="row">
					<div class="tabbable">
						<ul class="nav nav-tabs bg-slate nav-tabs-component nav-justified">
							<!-- email -->
							<li class="active"><a href="#colored-rounded-justified-tab1" data-toggle="tab">ايميل</a></li>
							<!-- sms -->
							<li><a href="#colored-rounded-justified-tab2" data-toggle="tab">رساله SMS</a></li>
							<!-- notification -->
							<li><a href="#colored-rounded-justified-tab3" data-toggle="tab">اشعار</a></li>
						</ul>

						<div class="tab-content">
							<!-- email -->
							<div class="tab-pane active" id="colored-rounded-justified-tab1">
							    <div class="row">
							    	<form action="{{route('emailallusers')}}" method="POST" enctype="multipart/form-data">
							    		{{csrf_field()}}
							    		<div class="col-sm-12">
							    			<textarea rows="15" name="email_message" class="form-control" placeholder="نص رسالة الـ Email "></textarea>
							    		</div>

								        <div class="col-sm-12" style="margin-top: 10px">
									      	<button type="submit" class="btn btn-primary addCategory"">ارسال</button>
									        <button type="button" class="btn btn-secondary" data-dismiss="modal">أغلاق</button>
								        </div>
							    	</form>
							    </div>
							</div>

							<!-- sms -->
							<div class="tab-pane" id="colored-rounded-justified-tab2">
							    <div class="row">
							    	<form action="{{route('smsallusers')}}" method="POST" enctype="multipart/form-data">
							    		{{csrf_field()}}
							    		<div class="col-sm-12">
							    			<textarea rows="15" name="sms_message" class="form-control" placeholder="نص رسالة الـ SMS "></textarea>
							    		</div>

								        <div class="col-sm-12" style="margin-top: 10px">
									      	<button type="submit" class="btn btn-primary addCategory"">ارسال</button>
									        <button type="button" class="btn btn-secondary" data-dismiss="modal">أغلاق</button>
								        </div>
							    	</form>
							    </div>
							</div>

							<!-- noification -->
							<div class="tab-pane" id="colored-rounded-justified-tab3">
							    <div class="row">
							    	<form action="{{route('sendsms')}}" method="POST" enctype="multipart/form-data">
							    		{{csrf_field()}}
							    		<div class="col-sm-12">
							    			<textarea rows="15" name="sms_message" class="form-control" placeholder="نص رسالة الـ Notification "></textarea>
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
			  </div>

			</div>
		</div>
	</div> --}}
	<!-- /correspondent for all users Modal -->

		<!-- correspondent for one user Modal -->
	{{-- <div class="modal fade" id="exampleModal4" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
			    <h5 class="modal-title" id="exampleModalLabel">مراسلة :  <span class="reverName"></span></h5>
			  </div>
			  <div class="modal-body">
			    <div class="row">
					<div class="tabbable">
						<ul class="nav nav-tabs bg-slate nav-tabs-component nav-justified">
							<!-- email -->
							<li class="active"><a href="#colored-rounded-justified-tab10" data-toggle="tab">ايميل</a></li>
							<!-- sms -->
							<li><a href="#colored-rounded-justified-tab20" data-toggle="tab">رساله SMS</a></li>
							<!-- notification -->
							<li><a href="#colored-rounded-justified-tab30" data-toggle="tab">اشعار</a></li>
						</ul>

						<div class="tab-content">
							<!-- email -->
							<div class="tab-pane active" id="colored-rounded-justified-tab10">
							    <div class="row">
							    	<form action="{{route('sendcurrentemail')}}" method="POST" enctype="multipart/form-data">
							    		{{csrf_field()}}
							    		<input type="hidden" name="email" value="">
							    		<div class="col-sm-12">
							    			<textarea rows="15" name="email_message" class="form-control" placeholder="نص رسالة الـ Email "></textarea>
							    		</div>

								        <div class="col-sm-12" style="margin-top: 10px">
									      	<button type="submit" class="btn btn-primary addCategory"">ارسال</button>
									        <button type="button" class="btn btn-secondary" data-dismiss="modal">أغلاق</button>
								        </div>
							    	</form>
							    </div>
							</div>

							<!-- sms -->
							<div class="tab-pane" id="colored-rounded-justified-tab20">
							    <div class="row">
							    	<form action="{{route('sendcurrentsms')}}" method="POST" enctype="multipart/form-data">
							    		{{csrf_field()}}
							    		<input type="hidden" name="phone" value="">
							    		<div class="col-sm-12">
							    			<textarea rows="15" name="sms_message" class="form-control" placeholder="نص رسالة الـ SMS "></textarea>
							    		</div>

								        <div class="col-sm-12" style="margin-top: 10px">
									      	<button type="submit" class="btn btn-primary addCategory"">ارسال</button>
									        <button type="button" class="btn btn-secondary" data-dismiss="modal">أغلاق</button>
								        </div>
							    	</form>
							    </div>
							</div>

							<!-- notification -->
							<div class="tab-pane" id="colored-rounded-justified-tab30">
							    <div class="row">
							    	<form action="{{route('sendcurrentnotification')}}" method="POST" enctype="multipart/form-data">
							    		{{csrf_field()}}
							    		<input type="hidden" name="device_id" value="">
							    		<div class="col-sm-12">
							    			<textarea rows="15" name="sms_message" class="form-control" placeholder="نص رسالة الـ Notification "></textarea>
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
			  </div>

			</div>
		</div>
	</div> --}}
	<!-- /correspondent for one user Modal -->

	</div>

	<!-- javascript -->
@section('script')
	<script type="text/javascript" src="{{asset('dashboard/js/plugins/tables/datatables/datatables.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('dashboard/js/plugins/forms/selects/select2.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('dashboard/js/pages/datatables_basic.js')}}"></script>
@endsection



<script type="text/javascript">

	$('.openEditmodal').on('click',function(){
		//get valus
		var id         = $(this).data('id')
		var name       = $(this).data('name')
		var photo      = $(this).data('photo')
		var phone      = $(this).data('phone')
		var email      = $(this).data('email')
		var permission = $(this).data('permission')
		var active     = $(this).data('active')
		var country_id = $(this).data('country_id')
		var is_provider= $(this).data('is_provider')
		var city_id    = $(this).data('city_id')

		//set values in modal inputs
		$("input[name='id']")             .val(id)
		$("input[name='edit_name']")      .val(name)
		$("input[name='edit_phone']")     .val(phone)
		$("input[name='edit_email']")     .val(email)

		$("select[name='edit_country_id']")      .val(country_id)
		$("select[name='edit_is_provider']")     .val(is_provider)
		$("select[name='edit_city_id']")     	.val(city_id)

		var link = "{{url('public/dashboard/uploads/users/')}}" +'/'+ photo
		$(".photo").attr('src',link)
		$('.userName').text(name)


		//select role
		$('#permissions option').each(function(){
			if($(this).val() == permission)
			{
				$(this).attr('selected','')
			}
		});

		//block input check
		if(active == 0)
		{
			$('#editActive').attr('checked','')
		}


	})

	//open send message modal
	// $('.SendMessageUser').on('click',function(){

	// 	var name     = $(this).data('name');
	// 	var phone    = $(this).data('phone');
	// 	var email    = $(this).data('email');
	// 	var device_id= $(this).data('device_id');
	// 	$('.reverName').html(name);
	// 	$('input[name="phone"]').val(phone);
	// 	$('input[name="email"]').val(email);
	// 	$('input[name="device_id"]').val(device_id);
	// })


</script>

<!-- other code -->
<script type="text/javascript">

	function ChooseFile(){$("input[name='edit_photo']").click()}
	function addChooseFile(){$("input[name='avatar']").click()}

	//stay in current tab after reload
	$(function() {
		// for bootstrap 3 use 'shown.bs.tab', for bootstrap 2 use 'shown' in the next line
		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			// save the latest tab; use cookies if you like 'em better:
			localStorage.setItem('lastTab', $(this).attr('href'));
		});

		// go to the latest tab, if it exists:
		var lastTab = localStorage.getItem('lastTab');
		if (lastTab) {
			$('[href="' + lastTab + '"]').tab('show');
		}
	});


	$(function() {
		$('.country').change(function() {

			var url = '{{ url('country') }}' +'/'+ $(this).val() + '/cities/';

			$.get(url, function(data) {
				var select = $('form .city');

				select.empty();

				$.each(data,function(key, value) {
					select.append('<option value=' + value.id + '>' + value.name_ar + '</option>');
				});
			});
		});
	});



</script>
<!-- /other code -->

@endsection