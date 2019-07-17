@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Список Заявок</div>
                    <ul class="navbar-nav ml-auto">
                        @foreach($ticketList as $ticket)
                            <li class="nav-item">
                                    {{ $ticket->created_at }}
                                    {{ $ticket->specialty->name }}
                            </li>
                        @endforeach
                    </ul>



                </div>
            </div>
        </div>
    </div>
</div>
@endsection
