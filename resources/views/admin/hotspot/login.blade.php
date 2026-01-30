@extends('adminlte::page')

@section('content')
    <div class="login-box">
        <div class="card">
            <div class="card-header text-center">
                <b>Acceso a Internet</b>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('admin.hotspot.auth') }}">
                    @csrf

                    @foreach ($__data as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    <button>Conectar</button>
                </form>

            </div>
        </div>
    </div>
@endsection
