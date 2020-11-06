<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SearchSessionClientController extends Controller
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
        

        $statment_client_session=DB::statement('CALL sp_create_tmp_client_session()');
        
            // Obtener las caracteristicas de los clientes.
            $client_sessions = DB::select('SELECT 
            id,
            vc_name,
            vc_last_name,
            vc_sur_name,
            vc_nick_name,
            TIMESTAMPDIFF(HOUR, dt_created, ?) AS i_time_session,
            dt_created,
            dt_created_dw,
            DATE(dt_next_payment) AS dt_next_payment,
            i_downloads,
            vc_workout_day,
            vc_workout_description,
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
            tmp_client_session
        ORDER BY id',[$dt_current_day ]);
           
            // Catalogo estados
        // return  $date ;

           return  view('client_sessions.index',compact('client_sessions', 'type_menu','user_system'));
     
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
