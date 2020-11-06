<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SearchReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Propiedades de Navegacion
        $branch='1';
        $type_menu='1';
        $user_system =  \Session::get('vc_user_system');
        $today = Carbon::now();
        $dt_current_day=  Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
        
         
        

        $resevation_training=DB::statement('CALL sp_create_tmp_resevation_training(?)', [$dt_current_day] );
        
            // Obtener las caracteristicas de los clientes.
            $schedule_resevation_training= DB::select('SELECT
            DATE(dt_schedule_resevation_training) AS  dt_schedule_resevation_training,
            DAY(dt_schedule_resevation_training) AS day_schedule_resevation_training,
            vc_day,
            vc_hour,
            dt_create,
            DATE(dt_next_payment) AS dt_next_payment,
            id,
            vc_name,
            vc_last_name,
            vc_sur_name,
            vc_nick_name,
            vc_email,
            vc_cellphone_number,
            i_status,
            vc_dashboard_status,
            i_pay_status,
            vc_pay_status,
            is_debtor,
            id_last_membership_model,
            vc_last_membership_model,
            id_last_workout_plan,
            vc_workout_plan_name
            FROM
            tmp_schedule_resevation_training');
           
            // Catalogo estados
        // return  $date ;

           return  view('resevation_training.index',compact('schedule_resevation_training', 'type_menu','user_system'));
     
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($id_client)
    {
           }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_client)
    {

       
          
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id_client)
    {
       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id_client)
    {
       
        


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
