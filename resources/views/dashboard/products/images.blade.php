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
                <h5 class="panel-title">الصور الفرعية الخاصة بالمنتج  {{$product->name_ar}}</h5>

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
                <div class="col-xs-6">
                    <a href="#" class="btn bg-purple-300 btn-block btn-float btn-float-lg" type="button"><i class="icon-list-numbered"></i> <span>عدد  الصور : {{count($images)}} </span> </a></div>
                <div class="col-xs-6">
                    <a href="{{route('logout')}}" class="btn bg-warning-400 btn-block btn-float btn-float-lg" type="button"><i class="icon-switch"></i> <span>خروج</span></a>
                </div>

            </div>
        </div>
        <!-- /buttons -->

        <div class="col-md-12 well">
            <form action="{{route('deleteImage')}}" method="post">
                <div class="row">
                    <h3 style="border-bottom: 1px solid #eee;border-bottom: 1px solid #eee;width: 170px;padding-bottom: 8px;margin-top: 0;color: #484646;margin-right: 12px;">معرض الصور</h3>
                    @foreach($images as $image)
                        <div class="col-md-3">
                            <img style="width: 100%;height:200px;" src="{{ Request::root() }}/public/dashboard/uploads/products/{{$image->image}}" alt="" class="img-responsive">
                            <input  value="{{$image->id}}" name="id[]" type="checkbox" class="form-control" style="position: absolute;top: -6px;left: 15px;width: 19px;">
                        </div>
                    @endforeach
                    <input type="hidden" name="_token" value="{{ Session::token() }}" />
                    <button type="submit"  style="position: absolute;top: 20px;left: 20px;" class="btn btn-danger"><i class="fa fa-trash-o"></i> حذف الصور المحدده</button>
                </div>
            </form>
            <form action="{{route('addImage')}}" method="post" enctype="multipart/form-data">
                <div class="form-group {{$errors->has('image') ? ' has-error' : ''}}" style="padding-top: 20px">
                    <label class="control-label col-lg-2">اضافة صورة جديدة</label>
                    <div class="col-lg-10">
                        <div class="input-group">
                            <input name="image" type="file" class="form-control">
                            <span class="input-group-addon bg-primary"><i class="icon-help"></i></span>
                        </div>
                        <input type="hidden" name="product_id" value="{{$product->id}}">
                        <input type="hidden" name="_token" value="{{ Session::token() }}" />
                        <div style="margin-top: 20px;width: 200px;" class="input-group pull-right">
                            <button type="submit" class="btn btn-primary pull-right">اضافه</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
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