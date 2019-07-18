<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class Message extends Model
{
    protected $fillable = [
        'user_id', 'ticket_id','text'
    ];

    public function ticket()
    {
        return $this->hasOne(Ticket::class, 'id', 'ticket_id' );
    }

    /**
     * возвращает id собеседника
     * @return mixed
     */
    public function collocutor(){
        $ticket = $this->ticket;
        if (Auth::user()->isPatient()){
            $this->collocutor_id = $ticket->doctor_id;
        }
        if (Auth::user()->isDoctor()){
            $this->collocutor_id = $ticket->patient_id;
        }
        return $this->collocutor_id;
    }


}
