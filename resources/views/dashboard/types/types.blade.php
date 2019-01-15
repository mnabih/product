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
            <h5 class="panel-title">قائمة الانواع الرئيسية</h5>
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
                    <button class="btn bg-blue btn-block btn-float btn-float-lg openAddModal" type="button" data-toggle="modal" data-target="#exampleModal"><i class="icon-plus3"></i> <span>اضافة نوع</span></button>
                </div>
                <div class="col-xs-4">
                    <button class="btn bg-purple-300 btn-block btn-float btn-float-lg" type="button"><i class="icon-list-numbered"></i> <span>عدد الانواع الرئيسية : {{count($types)}} </span> </button></div>
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
                <th>النوع</th>
                <th>تاريخ الاضافه</th>
                <th>التحكم</th>
            </tr>
            </thead>
            <tbody>
            @foreach($types as $type)
                <tr>
                    <td>{{$type->id}}</td>
                    <td>{{$type->name_ar}}</td>

                    <td>{{$type->created_at != null?$type->created_at->diffForHumans() : ''}}</td>
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
                                           data-id="{{$type->id}}"
                                           data-name="{{$type->name_ar}}"
                                        >
                                            <i class="icon-pencil7"></i>تعديل
                                        </a>
                                    </li>

                                    <!-- delete button -->
                                    <form action="{{route('deleteType')}}" method="POST">
                                        {{csrf_field()}}
                                        <input type="hidden" name="id" value="{{$type->id}}">
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
                        <h5 class="modal-title" id="exampleModalLabel">أضافة نوع جديد</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <form action="{{route('addType')}}" method="POST" enctype="multipart/form-data">
                                {{csrf_field()}}

                                <div class="row">

                                    <div class="col-sm-9" style="margin-top: 20px">
                                        <input type="text" name="name_ar" class="form-control" placeholder="الاسم" style="margin-bottom: 10px">
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
        <!-- /Add user Modal -->

        <!-- Edit user Modal -->
        <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> تعديل نوع : <span class="userName"></span> </h5>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('updateType')}}" method="post" enctype="multipart/form-data">

                            <!-- token and user id -->
                            {{csrf_field()}}
                            <input type="hidden" name="id" value="">
                            <!-- /token and user id -->
                            <div class="row">

                                <div class="col-sm-9" style="margin-top: 10px">
                                    <label>الاسم</label>
                                    <input type="text" name="edit_name_ar" class="form-control">

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
        <!-- /Edit user Modal -->


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

        //set values in modal inputs
        $("input[name='id']")             .val(id)
        $("input[name='edit_name_ar']")      .val(name)



    })


</script>

<!-- other code -->
<script type="text/javascript">


</script>
<!-- /other code -->

@endsection