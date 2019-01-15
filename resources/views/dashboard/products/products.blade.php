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

        .datepicker,
        .table-condensed {
            font-size: x-small;
        }

    </style>
@endsection
<!-- /style -->
@section('content')
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h5 class="panel-title">قائمة المنتجات </h5>
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
                    <button class="btn bg-blue btn-block btn-float btn-float-lg openAddModal" type="button" data-toggle="modal" data-target="#exampleModal"><i class="icon-plus3"></i> <span>اضافة منتج</span></button>
                </div>
                <div class="col-xs-4">
                    <button class="btn bg-purple-300 btn-block btn-float btn-float-lg" type="button"><i class="icon-list-numbered"></i> <span>عدد المنتجات : {{count($products)}} </span> </button></div>
                <div class="col-xs-4">
                    <a href="{{route('logout')}}" class="btn bg-warning-400 btn-block btn-float btn-float-lg" type="button"><i class="icon-switch"></i> <span>خروج</span></a>
                </div>
            </div>
        </div>
        <!-- /buttons -->

        <table class="table datatable-basic">
            <thead>
            <tr>
                <th>كود</th>
                <th>الصورة الرئيسية</th>
                <th> اسم المنتج</th>
                <th> النوع</th>
                <th> سعر المنتج</th>
                <th> المخزوز</th>
                <th> المبيعات</th>
                <th> العروض النشطه</th>
                <th>تاريخ الاضافه</th>
                <th>التحكم</th>
            </tr>
            </thead>
            <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{$product->id}}</td>
                    <td>
                        <a href="{{url('public/dashboard/uploads/products/'.$product->image)}}">
                            <img src="{{url('public/dashboard/uploads/products/'.$product->image)}}" style="width:40px;height: 40px" class="img-circle" alt="">
                        </a>
                    </td>
                    <td>{{$product->name_ar}}</td>
                    <td>{{$product->type->name_ar}}</td>
                    <td>{{$product->price}}</td>
                    <td>{{$product->stock}}</td>
                    <td>{{$product->sale_counter}}</td>
                    <td>
                        @if($product->has_offer == 1)
                            <span class="label label-danger" style="width: 40px;
                                                                    height: 20px;
                                                                    text-align: center;
                                                                    font-weight: bolder;
                                                                    font-size: inherit;"
                            > {{hasActiveOffer($product)}} %</span>
                        @endif
                    </td>
                    <td>{{$product->created_at->diffForHumans()}}</td>
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
                                           data-id="{{$product->id}}"
                                           data-name="{{$product->name_ar}}"
                                           data-price="{{$product->price}}"
                                           data-stock="{{$product->stock}}"
                                           data-type_id="{{$product->type->id}}"
                                           data-photo="{{$product->image}}"
                                        >
                                            <i class="icon-pencil7"></i>تعديل
                                        </a>
                                    </li>

                                    <!-- offer button -->
                                    <li>
                                        <a href="#" data-toggle="modal" data-target="#exampleModalOffer" class="openOffermodal"
                                           data-product_id="{{$product->id}}"
                                           data-product_offer="{{$product->offers->first()}}"
                                        >
                                            <i class="icon-pencil7"></i>العروض
                                        </a>
                                    </li>


                                    <li>
                                        <a href="{{url('admin/images/'. $product->id)}}" class="reset" style="margin-right: 0;
                                                                                                        margin-bottom: 10px;">
                                            <i class="icon-pencil7"></i> الصور </a>
                                    </li>

                                    <!-- delete button -->
                                    <form action="{{route('deleteProduct')}}" method="POST">
                                        {{csrf_field()}}
                                        <input type="hidden" name="id" value="{{$product->id}}">
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

        <!-- Add  Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">أضافة منتج جديد</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <form action="{{route('addProduct')}}" method="POST" enctype="multipart/form-data">
                                {{csrf_field()}}

                                <div class="row">
                                    <div class="col-sm-9" style="margin-top: 20px">
                                        <select name="type_id" id="" class="form-control" style="margin-bottom: 10px">
                                            @foreach($types as $type)
                                                <option value="{{$type->id}}">{{$type->name_ar}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-9" style="margin-top: 20px">
                                        <input type="text" name="name_ar" class="form-control" placeholder="الاسم" style="margin-bottom: 10px">
                                        <input type="text" name="price" class="form-control" placeholder="السعر" style="margin-bottom: 10px">
                                        <input type="text" name="stock" class="form-control" placeholder="المخزون" style="margin-bottom: 10px">
                                    </div>
                                    <div class="col-sm-3 text-center">
                                        <label style="margin-bottom: 0">اختيار الصورة الرئيسية</label>
                                        <i class="icon-camera"  onclick="addChooseFile()" style="cursor: pointer;"></i>
                                        <div class="images-upload-block">
                                            <input type="file" name="image" class="image-uploader" id="hidden">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12" style="margin-top: 10px">
                                    <button type="submit" class="btn btn-primary addCategory">اضافة</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">أغلاق</button>
                                </div>

                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- /Add  Modal -->


        <!-- offer Modal -->
        <div class="modal fade" id="exampleModalOffer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">اضافة / تعديل العرض </h5>
                    </div>
                    <div class="modal-body">
                        
                        <div class="row">
                            <div class="col-md-3" style="width: 20%">
                                <h6 style="color: crimson; font-weight: bold">العرض السابق</h6>
                            </div>
                            <div class="col-md-3" style="width: 20%">
                                <label for="percentage">نسبة الخصم</label>
                                <input type="text" name="product_offer_percentage" class="form-control" readonly>
                            </div>
                            <div class="col-md-3" style="width: 20%">
                                <label for="percentage">تاريخ البداية</label>
                                <input type="text" name="product_offer_start" class="form-control" readonly >
                            </div>
                            <div class="col-md-3" style="width: 20%">
                                <label for="percentage">تاريخ النهاية</label>
                                <input type="text" name="product_offer_end" class="form-control" readonly>
                            </div>

                        </div>
                        <div class="row">
                            <form action="{{route('addOffer')}}" method="POST" enctype="multipart/form-data">
                                {{csrf_field()}}

                                <div class="row">
                                    <div class="col-sm-9" style="margin-top: 20px">
                                        <input type="text" hidden name="product_id">
                                        <label for="percentage">نسبة الخصم</label>
                                        <input type="text" name="percentage" class="form-control" placeholder="نسبة الخصم" style="margin-bottom: 10px" min=".01">
                                        @if ($errors->has('percentage'))
                                            <span class="help-block help-block-error">
                                             <strong style="color: #e73d4a">{{ $errors->first('percentage') }}</strong>
                                        </span>
                                        @endif

                                        <label for="start_date">تاريخ البدء</label>
                                        <input type="text" name="start_date" class="form-control datepicker" autocomplete="off" placeholder="تاريخ بداية الخصم" style="margin-bottom: 10px">
                                        @if ($errors->has('start_date'))
                                            <span class="help-block help-block-error">
                                             <strong style="color: #e73d4a">{{ $errors->first('start_date') }}</strong>
                                        </span>
                                        @endif

                                        <label for="end_date">تاريخ الانتهاء</label>
                                        <input type="text" name="end_date" class="form-control datepicker" autocomplete="off" placeholder="تاريخ نهاية الخصم" style="margin-bottom: 10px">
                                        @if ($errors->has('end_date'))
                                            <span class="help-block help-block-error">
                                             <strong style="color: #e73d4a">{{ $errors->first('end_date') }}</strong>
                                        </span>
                                        @endif

                                        @if ($errors->has('percentage') || $errors->has('start_date') || $errors->has('end_date'))
                                            <script>
                                                $( document ).ready(function() {
                                                    $('#exampleModalOffer').modal('show');
                                                });
                                            </script>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-12" style="margin-top: 10px">
                                    <button type="submit" class="btn btn-primary addCategory">اضافة</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">أغلاق</button>
                                </div>

                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- /offer Modal -->

        <!-- Edit  Modal -->
        <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> تعديل منتج : <span class="userName"></span> </h5>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('updateProduct')}}" method="post" enctype="multipart/form-data">

                            <!-- token and user id -->
                            {{csrf_field()}}
                            <input type="hidden" name="id" value="">
                            <!-- /token and user id -->
                            <div class="row">
                                <div class="col-sm-9" style="margin-top: 20px">
                                    <label>النوع الرئيسي</label>
                                    <select name="edit_type_id" id="" class="form-control" style="margin-bottom: 10px">
                                        @foreach($types as $type)
                                            <option value="{{$type->id}}">{{$type->name_ar}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-9" style="margin-top: 10px">
                                    <label>الاسم</label>
                                    <input type="text" name="edit_name_ar" class="form-control">
                                    <label>السعر</label>
                                    <input type="text" name="edit_price" class="form-control">
                                    <label>المخزون</label>
                                    <input type="text" name="edit_stock" class="form-control">
                                </div>

                                <div class="col-sm-3 text-center">
                                    <label >اختيار صوره</label>
                                    <img src="" class="photo" style="width: 120px;height: 120px;cursor: pointer" onclick="ChooseFile()">
                                    <input type="file" name="edit_image" style="display: none;">
                                </div>
                            </div>
                            <div class="row">


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
        <!-- /Edit  Modal -->


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
        var id               = $(this).data('id')
        var name             = $(this).data('name')
        var stock             = $(this).data('stock')
        var price             = $(this).data('price')
        var type_id       = $(this).data('type_id')
        var photo      = $(this).data('photo')

        //set values in modal inputs
        $("input[name='id']")               .val(id)
        $("input[name='edit_name_ar']")     .val(name)
        $("input[name='edit_stock']")       .val(stock)
        $("input[name='edit_price']")       .val(price)
        $("select[name='edit_type_id']")    .val(type_id)

        var link = "{{asset('public/dashboard/uploads/products/')}}" +'/'+ photo
        $(".photo").attr('src',link)
        $('.userName').text(name)



    })

    $('.openOffermodal').on('click',function(){
        //get valus
        var product_id               = $(this).data('product_id')
        var product_offer               = $(this).data('product_offer')


        //set values in modal inputs
        $("input[name='product_id']")               .val(product_id)
        $("input[name='product_offer_percentage']")               .val(product_offer.percentage)
        $("input[name='product_offer_start']")               .val(product_offer.start_date)
        $("input[name='product_offer_end']")               .val(product_offer.end_date)

    })


</script>

<!-- other code -->
<script type="text/javascript">
    function ChooseFile(){$("input[name='edit_image']").click()}
    function addChooseFile(){$("input[name='image']").click()}

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

    $('.datepicker').datepicker({
        rtl:'true',
        language: 'ar',
        format:'yyyy-mm-dd',
        autoclose:false,
        todayBtn:true,
        clearBtn:true,
        todayHighlight:false,
        startDate: '+1d'
    });


</script>

<!-- /other code -->

@endsection