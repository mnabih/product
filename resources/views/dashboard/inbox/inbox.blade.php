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
    			background: none
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
@section('title','الرسائل')
@section('content')
<div class="panel panel-flat">
	<div class="panel-heading">
		<h5 class="panel-title">قائمة الرسائل</h5>
		<div class="heading-elements">
			<ul class="icons-list">
        		<li><a data-action="collapse"></a></li>
        		<li><a data-action="reload"></a></li>
        	</ul>
    	</div>
	</div>


	<table class="table datatable-basic">
		<thead>
			<tr>
				<th>الاسم</th>
				<th>البريد</th>
				<th>الهاتف</th>
				<th>الرساله</th>
				<th>التاريخ</th>
				<th>التحكم</th>
			</tr>
		</thead>
		<tbody>
			@foreach($messages as $m)
				
				<tr @if($m->showOrNow == 0) style="background: #e0d0d0" @endif >
					<td><a href="{{route('showmessage',$m->id)}}">{{$m->name}}</a></td>
					<td>{{$m->email}}</td>
					<td>{{$m->phone}}</td>
					<td><a href="{{route('showmessage',$m->id)}}">{{str_limit($m->message,15)}}</a></td>
					<td>{{$m->created_at->diffForHumans()}}</td>
					<td>
					<ul class="icons-list">
						<li>
							<form action="{{route('deletemessage')}}" method="POST">
								{{csrf_field()}}
								<input type="hidden" name="id" value="{{$m->id}}">
								<li><button type="submit" class="generalDelete reset" title="حذف"><i class="icon-trash"></i></button></li>
							</form>
						</li>
					</ul>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>

</div>
	
<!-- javascript -->
@section('script')
<script type="text/javascript" src="{{asset('dashboard/js/plugins/tables/datatables/datatables.min.js')}}"></script>
<script type="text/javascript" src="{{asset('dashboard/js/plugins/forms/selects/select2.min.js')}}"></script>
<script type="text/javascript" src="{{asset('dashboard/js/pages/datatables_basic.js')}}"></script>
@endsection


@endsection