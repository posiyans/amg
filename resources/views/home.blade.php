@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Система общения</div>

                <div class="card-body">
                    Добро пожаловать, {{ Auth::user()->name }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
