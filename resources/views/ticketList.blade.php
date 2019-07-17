@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Список Пациентов</div>
                    <ul class="navbar-nav ml-auto">
                        @foreach($ticketList as $ticket)
                            <li class="nav-item">
                                <a href="{{ route('patientProfile', $ticket->patient_id ) }}">
                                    {{ $ticket->specialty->name }}
                                    {{ $ticket->patient->surname }}
                                    {{ $ticket->patient->name }}
                                    {{ $ticket->patient->patronymic }}
                                    {{ $ticket->patient->date }}
                                    {{ $ticket->patient->city }}
                                    ____________
                                    <a href="{{route('chat', $ticket->id)}}">Начать чат</a>
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
