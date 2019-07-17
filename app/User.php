<?php

namespace App;

use App\Ticket;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'surname', 'name','patronymic','date', 'city', 'role', 'specialty_id', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function patient_tickets()
    {
        return $this->hasMany(Ticket::class, 'patient_id', 'id');
    }

    public function patient_open_tickets()
    {
        return $this->hasMany(Ticket::class, 'patient_id', 'id')->whereNull('doctor_id');
    }
    public function patient_tickets_in_work()
    {
        return $this->hasMany(Ticket::class, 'patient_id', 'id')->whereNotNull('doctor_id');
    }

    public function doctor_tickets()
    {
        return $this->hasMany(Ticket::class, 'doctor_id', 'id');
    }

    public function doctor_open_tickets()
    {
        $ticket= Ticket::where('doctor_id', null)->where('specialty_id', $this->specialty_id)->get();
        return $ticket;
    }

    public function doctor_tickets_in_work()
    {
        $ticket= Ticket::where('doctor_id', $this->id)->get();
        return $ticket;
    }

    public function specialty()
    {
        return $this->hasOne('App\Specialty' , 'id', 'specialty_id');
    }
    public static function getAllDoctor(){
        return User::where('role', 2)->get();
    }

    public function getDoctorForPatient()
    {

        $tickets = $this->hasMany(Ticket::class, 'patient_id', 'id')
            ->whereNotNull('doctor_id')
            ->distinct('doctor_id')
            ->get();
        $doctors = [];
        foreach ($tickets as $ticket) {
            $doctors[] = $ticket->doctor;
        }
        return $doctors;
    }

    public function getOpenTicket(){
        if ($this->isPatient()){
            return $this->patient_open_tickets;
        }
        if ($this->isDoctor()){
            return $this->doctor_open_tickets();
        }
    }


    public function getInWorkTicket(){
        if ($this->isPatient()){
            return $this->patient_tickets_in_work;
        }
        if ($this->isDoctor()){
            return $this->doctor_tickets_in_work();
        }
    }


    public function isPatient(){
        return ($this->role == 1) ? True : False;
    }

    public function isDoctor(){
        return ($this->role == 2) ? True : False;
    }

    public function getProfile($id){
        if ($this->isPatient()) {
            $doctor = Ticket::where('patient_id', $this->id)->where('doctor_id', $id)->first();
            if (!$doctor) {
                return false;
            }
        }
        return User::findOrFail($id);
    }

}
