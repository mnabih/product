
<div class="main log ">
    <form action="{{url('login')}}" method="POST" role="form">
      {{csrf_field()}}
        <div class="form-group">
            <input type="email" name="email" class="form-control" placeholder="الايميل" required="">
        </div>
        <div class="form-group">
            <input type="password" name="password" class="form-control" placeholder="الرقم السرى">
        </div>
        <button type="submit" class="btn">تسجيل الدخول</button>
        <a href="{{url('password/reset')}}" class="forget-password">هل نسيت الرقم السرى ؟</a>
        <a href="{{url('register')}}" class="new-user-2">تسجيل جديد</a>
    </form>

    @if(count($errors) > 0)
      @foreach($errors->all() as $error)
      <div class="alert alert-danger text-center" style="margin-top:10px">
         {{$error}}
      </div>
      @endforeach
    @endif
</div>

