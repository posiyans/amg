<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Ticket extends Model
{
    //

    public function patient()
    {
        return $this->hasOne('App\User', 'id', 'patient_id');
    }

    public function specialty()
    {
        return $this->hasOne('App\Specialty' , 'id', 'specialty_id');
    }

    public function doctor()
    {
        return $this->hasOne('App\User', 'id', 'doctor_id');
    }

    public function message()
    {
        return $this->hasMany(Message::class, 'ticket_id', 'id');
    }

    /**
     * проверка на доступ к заявке
     * @return $this|bool
     */
    public function checkAccess(){
        $user=Auth::user();
        if($this->patient_id == $user->id or $this->doctor_id  == $user->id){
            return $this;
        }
        return false;
    }
}


