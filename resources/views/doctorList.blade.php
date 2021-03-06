@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Список Врачей</div>
                    <ul class="navbar-nav ml-auto">
                        @foreach($doctors as $user)
                            <li class="nav-item">
                                <a href="{{ route('doctorProfile', $user->id) }}">
                                    {{ $user->surname }}
                                    {{ $user->name }}
                                    {{ $user->patronymic }}
                                    {{ $user->date }}
                                    {{ $user->city }}
                                    @if($user->online)
                                        online
                                    @else
                                        offline
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>



                </div>
            </div>
        </div>
    </div>
</div>
@endsection
