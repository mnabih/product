@extends('site.layout.layout')

@section('content')

<div class="main">
    <div class="main-header">

        <h2>نسيت كلمة المرور</h2>
        <div class="burgerr">
            <a class="link" href="{{url('/')}}"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
        </div>
    </div>
    <div class="main-content">
        @if (session('status'))
            <div class="alert alert-success text-center">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->has('email'))
            <div class="alert alert-danger text-center">
                 {{ $errors->first('email') }}
            </div>
        @endif
        <div class="editt">
            <form action="{{ url('/password/email') }}" method="POST" role="form">
                 {{ csrf_field() }}
                <div class="form-group">
                    <input type="email" name="email" class="form-control" id="email" placeholder="البريد الإلكترونى" required>
                </div>
                <button type="submit" class="btn">إرسال</button>
            </form>
        </div>
    </div>
</div>
@endsection
