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
            @if($id == 1)
                <h5 class="panel-title">  الطلبات المنتهية </h5>
            @elseif($id == 0 && $id != null)
                <h5 class="panel-title">  الطلبات المتعلقة </h5>
            @else
                <h5 class="panel-title">  قائمة الطلبات  </h5>
            @endif
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
                <div class="col-xs-3">
                    <a href="{{route('orders')}}"class="btn bg-purple-300 btn-block btn-float btn-float-lg" type="button"><i class="icon-list-numbered"></i> <span>اجمالى الطلبات : {{count($allOrders)}} </span> </a></div>
                <div class="col-xs-3">
                    <a href="{{route('orders',['id'=>1])}}" class="btn bg-purple-300 btn-block btn-float btn-float-lg" type="button"><i class="icon-list-numbered"></i> <span> الطلبات  المنتهية : {{count($finishedOrders)}} </span> </a></div>
                <div class="col-xs-3">
                    <a href="{{route('orders',['id'=>0])}}" class="btn bg-purple-300 btn-block btn-float btn-float-lg" type="button"><i class="icon-list-numbered"></i> <span> الطلبات  المتعلقة : {{count($unfinishedOrders)}} </span> </a></div>
                <div class="col-xs-3">
                    <a href="{{route('logout')}}" class="btn bg-warning-400 btn-block btn-float btn-float-lg" type="button"><i class="icon-switch"></i> <span>خروج</span></a>
                </div>

            </div>
        </div>
        <!-- /buttons -->

        <table class="table datatable-basic">
            <thead>
            <tr>
                <th>رقم الطلب</th>
                <th>العميل</th>
                <th>اجمالى القيمة</th>
                <th>قيمة الشحن</th>
                <th>تاريخ الطلب</th>
                @if($id == 1)
                <th>تاريخ انتهاء</th>
                <th>المندوب</th>
                @endif
                <th>التحكم</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{$order->id}}</td>
                    <td>{{$order->user->name}}</td>
                    <td>{{$order->totalPrice}}</td>
                    <td>{{$order->charge}}</td>
                    <td>{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order->created_at)->format('Y-m-d')}}</td>
                    @if($id == 1)
                    <td>{{ $order->finish_date}}</td>
                    <td>{{$order->delivery->name}}</td>
                    @endif
                    <td>
                        <ul class="icons-list">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="icon-menu9"></i>
                                </a>

                                <ul class="dropdown-menu dropdown-menu-right">
                                    <!-- edit button -->
                                    <li>
                                        <a href="{{route('showOrder',['id'=>$order->id])}}">
                                            <i class="icon-pencil7"></i>تفاصيل الطلب
                                        </a>
                                    </li>
                                    <!-- delete button -->
                                    <form action="{{route('deleteOrder')}}" method="POST">
                                        {{csrf_field()}}
                                        <input type="hidden" name="id" value="{{$order->id}}">
                                        <li><button type="submit" class="generalDelete reset"><i class="icon-trash"></i>الغاء طلب</button></li>
                                    </form>


                                </ul>


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



<script type="text/javascript">



</script>

<!-- other code -->
<script type="text/javascript">


</script>
<!-- /other code -->

@endsection