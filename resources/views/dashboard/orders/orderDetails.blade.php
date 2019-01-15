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
                <h5 class="panel-title">  تفاصيل الطلب رقم   {{$id}} </h5>

            <div class="heading-elements">
                <ul class="icons-list">
                    {{--<li><a data-action="collapse"></a></li>--}}
                    <li><a data-action="reload"></a></li>
                    <!-- <li><a data-action="close"></a></li> -->
                </ul>
            </div>
        </div>


        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>اسم المنتج</th>
                    <th>العدد</th>
                    <th>السعر</th>
                    <th>الاجمالى</th>
                </tr>
            </thead>
            <tbody>
            <?php $total = 0 ?>
                @foreach($orderDetails as $product)
                    <tr>
                        <td>{{$product->product->name_ar}}</td>
                        <td>{{$product->quantity}}</td>
                        <td>{{$product->price}}</td>
                        <td>{{$product->price * $product->quantity}}</td>

                    </tr>
                    <?php $total += $product->price * $product->quantity ?>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>الاجمالى (بدون الشحن)</th>
                    <th></th>
                    <th></th>
                    <th>{{$total}}</th>
                </tr>
            </tfoot>
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