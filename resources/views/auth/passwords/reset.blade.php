@extends('site.layout.layout')

@section('content')

<div class="main">
    <div class="main-header">

        <h2>الرقم السرى الجديد</h2>
        <div class="burgerr">
            <a class="link" href="{{url('/')}}"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
        </div>
    </div>
    <div class="main-content">
        @if ($errors->has('email'))
            <div class="alert alert-danger text-center">
                 {{ $errors->first('email') }}
            </div>
        @endif
        @if ($errors->has('password'))
            <div class="alert alert-danger text-center">
                 {{ $errors->first('password') }}
            </div>
        @endif
        @if ($errors->has('password_confirmation'))
            <div class="alert alert-success text-center">
                 {{ $errors->first('password_confirmation') }}
            </div>
        @endif
        <div class="editt">
            <form action="{{ url('/password/reset') }}" method="POST" role="form">
                 {{ csrf_field() }}
                 <input type="hidden" name="token" value="{{ $token }}">
                <div class="form-group">
                <input id="email" type="email" class="form-control" name="email" placeholder="البريد الالكترونى" required >
                <input id="password" type="password" class="form-control" name="password" placeholder="الرقم السرى الجديد" required>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="اعادة الرقم السرى" required>

                </div>
                <button type="submit" class="btn">حفظ</button>
            </form>
        </div>
    </div>
</div>
@endsection
