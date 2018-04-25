<div class="sidebar sidebar-main">
    <div class="sidebar-content">

        <!-- User menu -->
        <div class="sidebar-user">
            <div class="category-content">
                @if(Auth::check())
                <div class="media">
                    <a href="#" class="media-left"><img src="{{asset('dashboard/uploads/users/'.Auth::user()->photo)}}" class="img-circle img-sm" alt=""></a>
                    <div class="media-body">
                        <span class="media-heading text-semibold">{{Auth::user()->name}}</span>
                        <div class="text-size-mini text-muted">
                            <i class="icon-pin text-size-small"></i> {{Role()}}
                        </div>
                    </div>

                    <div class="media-right media-middle">
                        <ul class="icons-list">
                            <li>
                                <a href="{{route('logout')}}"><i class=" icon-switch"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
                @endif
            </div>
        </div>
        <!-- /user menu -->

        <!-- Main navigation -->
        <div class="sidebar-category sidebar-category-visible">
            <div class="category-content no-padding">
                <ul class="navigation navigation-main navigation-accordion">
                    {{menu()}}
                </ul>
            </div>
        </div>
        <!-- /main navigation -->

    </div>
</div>