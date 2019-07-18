<?php

namespace App;

use App\Ticket;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

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
        'online' => 'boolean'
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

    /**
     * возвращает всех врачей
     * @return mixed
     */
    public static function getAllDoctor(){
        Auth::User()->setOnline();
        return User::where('role', 2)->get();
    }

    /**
     * возврщвет врачей для пациента
     * @return array
     */
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

    /**
     * возвращает открытые заявки
     * @return mixed
     */
    public function getOpenTicket(){
        if ($this->isPatient()){
            return $this->patient_open_tickets;
        }
        if ($this->isDoctor()){
            return $this->doctor_open_tickets();
        }
    }


    /**
     * возврящает завки в работе
     * @return mixed
     */
    public function getInWorkTicket(){
        if ($this->isPatient()){
            return $this->patient_tickets_in_work;
        }
        if ($this->isDoctor()){
            return $this->doctor_tickets_in_work();
        }
    }

    /**
     * проверка на роль пациента
     * @return bool
     */
    public function isPatient(){
        return ($this->role == 1) ? True : False;
    }

    /**
     * проверка на роль врача
     * @return bool
     */
    public function isDoctor(){
        return ($this->role == 2) ? True : False;
    }

    /**
     * возврящает профиль
     * @param $id
     * @return bool
     */
    public function getProfile($id){
        if ($this->isPatient()) {
            $doctor = Ticket::where('patient_id', $this->id)->where('doctor_id', $id)->first();
            if (!$doctor) {
                return false;
            }
        }
        return User::findOrFail($id);
    }

    /**
     * проверка на онлайн
     * @param bool $id
     * @return mixed
     */
    public function isOnline($id = false){
        if ($id){
            $user = User::find($id);
        }else{
            $user = $this;
        }
        return $user->online;
    }

    /**
     * устновить статус онлайн
     * @param bool $id
     * @return mixed
     */
    public function setOnline($id = false){
        return $this->changeStatus($id, true);
    }

    /**
     * установить статус офлайн
     * @param bool $id
     * @return mixed
     */
    public function setOffline($id = false){
        return $this->changeStatus($id, false);
    }

    /**
     * сменить статус
     * @param $id
     * @param $status
     * @return mixed
     */
    public function changeStatus($id, $status){
        if ($id){
            $user = User::find($id);
        }else{
            $user = $this;
        }
        $user->online = $status;
        $user->save();
        return $user->online;
    }
}
