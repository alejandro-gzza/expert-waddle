<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RegistryController extends Controller
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
        $type_menu='5';
        $user_system =  \Session::get('vc_user_system');
        
    
// $dia->setTimezone('America/Monterrey');

       // $today->setTimezone('Europe/Stockholm');
        // $dt_registry= new Carbon( $today, 'Europe/Stockholm');
       

        //Carga de Catalogos

            //Canal de Adquisicion 
            $acquisition_channel = DB::select('SELECT id_acquisition_channel, vc_acquisition_channel from cat_acquisition_channel  where b_status = 1 order by 1 asc');        

            // Catalogo estado civil

            $civil_status = DB::select('SELECT i_civil_status, vc_civil_status FROM cat_civil_status   ORDER BY i_civil_status  ASC');
               
            // Catalogo estados

            $states= DB::select('SELECT id_state, vc_state FROM cat_states   ORDER BY vc_state ASC');

             // Catalogo ciudades

            $cities_local= DB::select('SELECT id_city, vc_city FROM cat_cities   WHERE id_state="19" ORDER BY vc_city ASC');
            
            // Catalogo de sucursales

            $employments= DB::select('SELECT id_employment, vc_employment FROM cat_employments  ORDER BY id_employment ASC');
         // Catalogo de sucursales

            $terms_conditions= DB::select('SELECT vc_terms_conditions FROM cat_branches  WHERE  id_branch=?', [ $branch] );

            
            // catalogo de niveles de entrenamiento
            $workout_plan= DB::select('SELECT id_workout_plan, vc_workout_plan_alias from cat_workout_plan  where b_status = 1 order by 1 asc');


            //Experiencia
             $previous_experience = DB::select('SELECT id_previous_experience, vc_previous_experience from cat_previous_experience  where b_status = 1 order by id_previous_experience asc');

             //Metas del prospecto
             $customer_goals = DB::select('SELECT id_customer_goal, vc_customer_goal from cat_customer_goals  where b_status = 1 order by 1 asc');

            //Coach de la prueba
            $coach = DB::select('SELECT id_team_member, vc_team_member from cat_team_member  where b_status = 1 AND i_type_team_member in(2,3) order by 1 desc');


        // return  $date ;

           return view('registry.index',compact('civil_status','states', 'cities_local','employments','terms_conditions','workout_plan','previous_experience', 'customer_goals', 'acquisition_channel','coach','type_menu','user_system'));
     
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
    public function store(Request $request)
    {
        $branch='1';
        $type_menu='5';
        $user_system =  \Session::get('vc_user_system');


         //$current = Carbon::now('America/Monterrey')->format('n-d-Y');
        //$current = new Carbon();
        //$today = Carbon::today();
        //$carbon = new Carbon('YYYY-MM-DD HH:II:SS', 'America/Los_Angeles');

        $today = Carbon::now();
        $dt_registry=  Carbon::createFromFormat('Y-m-d H:i:s', $today, 'America/Mexico_City');
   


         //Validaciones 

        $validatedData = $request->validate([           
            'vc_name' => 'required|max:255|regex:/^([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            'vc_last_name' => 'required|max:255|regex:/^([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            'vc_sur_name' => 'required|max:255|regex:/^([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            'dt_born_year' => 'required|numeric',
            'dt_born_month' => 'required|numeric',
            'dt_born_day' => 'required|numeric',
            'i_civil_status' => 'required|numeric',
            'i_gender' => 'required|numeric|between:1,2',
            'id_origin_state' => 'required|numeric',
            'id_employment' => 'required|numeric',
            'vc_email' => 'required|max:255|email',
            'vc_cellphone_number' => 'required|numeric',
            'vc_adress_street' => 'required|max:255|regex:/^([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            'vc_adress_number' => 'required|max:255|regex:/^([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            'vc_adress_apartment' => 'nullable|max:255|regex:/^([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            'id_adress_city' => 'required|numeric',
            'vc_adress_suburb' => 'required|max:255|regex:/^([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            'vc_emergency_contact_name' => 'required|max:255|regex:/^([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            'vc_emergency_contact_last_name' => 'required|max:255|regex:/^([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            'vc_emergency_contact_sur_name' => 'nullable|max:255|regex:/^([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-])+((\s*)+([:,.;0-9a-zA-ZñÑáéíóúÁÉÍÓÚ_-]*)*)+$/',
            'vc_emergency_contact_email' => 'nullable|max:255|email',
            'vc_emergency_contact_cell_number' => 'required|numeric',
            'id_previous_experience' => 'required|numeric',
            'id_customer_goal' => 'required|numeric',
            'id_workout_plan' => 'required|numeric',
            'id_coach' => 'required|numeric',
            'id_acquisition_channel' => 'required|numeric',    
            'i_accept_terms_conditions' =>  'numeric|between:1,2',
             ]);
    


         //Obteniendo variables post


   
         $vc_name=$request->input('vc_name');
         $vc_last_name=$request->input('vc_last_name');
         $vc_sur_name=$request->input('vc_sur_name');
         $dt_born_year=$request->input('dt_born_year');
         $dt_born_month=$request->input('dt_born_month');
         $dt_born_day=$request->input('dt_born_day');
         $i_civil_status=$request->input('i_civil_status');
         $i_gender=$request->input('i_gender');
         $id_origin_state=$request->input('id_origin_state');
         $id_origin_city=$request->input('id_origin_city');
         $id_employment=$request->input('id_employment');
         $vc_email=$request->input('vc_email');
         $vc_cellphone_number=$request->input('vc_cellphone_number');
         $vc_adress_street=$request->input('vc_adress_street');
         $vc_adress_number=$request->input('vc_adress_number');
         $vc_adress_apartment=$request->input('vc_adress_apartment');
         $id_adress_city=$request->input('id_adress_city');
         $vc_adress_suburb=$request->input('vc_adress_suburb');
         $vc_emergency_contact_name=$request->input('vc_emergency_contact_name');
         $vc_emergency_contact_last_name=$request->input('vc_emergency_contact_last_name');
         $vc_emergency_contact_sur_name=$request->input('vc_emergency_contact_sur_name');
         $vc_emergency_contact_email=$request->input('vc_emergency_contact_email');
         $vc_emergency_contact_cell_number=$request->input('vc_emergency_contact_cell_number');
         $id_previous_experience=$request->input('id_previous_experience');
         $id_customer_goal=$request->input('id_customer_goal');
         $id_workout_plan=$request->input('id_workout_plan');
         $id_coach=$request->input('id_coach');
         $id_acquisition_channel=$request->input('id_acquisition_channel');
         $i_accept_terms_conditions=$request->input('i_accept_terms_conditions');
         $id_prospect_perfomance_test=$request->input('id_prospect_perfomance_test');
        


        //Validaciones de campos vacios, reset NULL
     
    

        if(empty($id_origin_city)){$id_origin_city='0';}
        if(empty($vc_adress_apartment)){$vc_adress_apartment='';}
        if(empty($vc_emergency_contact_name)){$vc_emergency_contact_name='';}
        if(empty($vc_emergency_contact_last_name)){$vc_emergency_contact_last_name='';}
        if(empty($vc_emergency_contact_sur_name)){$vc_emergency_contact_sur_name='';}
        if(empty($i_accept_terms_conditions)){$i_accept_terms_conditions='0';}
        if(empty($id_prospect_perfomance_test)){$id_prospect_perfomance_test='0';}


    
       // Genera fecha de nacimiento a partir de año mes  y dia

        $dt_born_concat=$dt_born_year.'-'.$dt_born_month.'-'.$dt_born_day;

        $dt_born = new Carbon($dt_born_concat);
        $dt_born->format('Y-M-D');



          
        
         //Variable de Estatus en 0, usuario registrado sin ninguna membresia.
         $id_status='0';

          //Variable de sector vacio, se agregara posterior con un evento.

         $i_adress_sector_suburb='0';
         

        //Insercion de datos del cliente 


       $statment_registry=DB::statement('CALL sp_create_new_client(?,?,?, @id_client)',[ $branch, $dt_registry ,$id_status  ]);
       $registry_result=DB::select('select @id_client as new_client');
       $id_client = $registry_result[0]->new_client;


        //Insercion de datos de la experiencia del servicio , se obtiene primero el prospecto generado 

        $registry_exp_result=DB::statement('CALL sp_create_new_client_personal_information (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,@id_clients_personal_information)',[ $id_client, $branch,  utf8_decode($vc_name), utf8_decode($vc_last_name), utf8_decode($vc_sur_name),$dt_born, $i_civil_status, $i_gender, $id_origin_state, $id_origin_city,$id_employment, $vc_email, $vc_cellphone_number,  utf8_decode($vc_adress_street),   $vc_adress_number, $vc_adress_apartment,  $id_adress_city, $vc_adress_suburb,  $i_adress_sector_suburb,   utf8_decode($vc_emergency_contact_name), utf8_decode($vc_emergency_contact_last_name) ,  utf8_decode($vc_emergency_contact_sur_name),  $vc_emergency_contact_email ,   $vc_emergency_contact_cell_number, $id_previous_experience, $id_customer_goal, $id_workout_plan,  $id_coach,  $id_acquisition_channel, $id_prospect_perfomance_test,  $i_accept_terms_conditions  ]);
        $id_clients_personal_information=DB::select('select @id_clients_personal_information as new_clients_personal_information');

    
    return redirect()->route('registry.index')-> with('status','Los datos del cliente '.utf8_decode($vc_name).' '.utf8_decode($vc_last_name).' fueron guardados correctamente');


    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id_prospect_perfomance_test)
    {
         // Propiedades de Navegacion
         $branch='1';
         $type_menu='5';
         $user_system =  \Session::get('vc_user_system');
         
        // $id_prospect_perfomance_test=$request->input('id_prospect_perfomance_test');
     
 // $dia->setTimezone('America/Monterrey');
 
        // $today->setTimezone('Europe/Stockholm');
         // $dt_registry= new Carbon( $today, 'Europe/Stockholm');
        
 
         //Carga de Catalogos
 
             //Canal de Adquisicion 
             $acquisition_channel = DB::select('SELECT id_acquisition_channel, vc_acquisition_channel from cat_acquisition_channel  where b_status = 1 order by 1 asc');        
 
             // Catalogo estado civil
 
             $civil_status = DB::select('SELECT i_civil_status, vc_civil_status FROM cat_civil_status   ORDER BY i_civil_status  ASC');
                
             // Catalogo estados
 
             $states= DB::select('SELECT id_state, vc_state FROM cat_states   ORDER BY vc_state ASC');
 
              // Catalogo ciudades
 
             $cities_local= DB::select('SELECT id_city, vc_city FROM cat_cities   WHERE id_state="19" ORDER BY vc_city ASC');
             
             // Catalogo de sucursales
 
             $employments= DB::select('SELECT id_employment, vc_employment FROM cat_employments  ORDER BY id_employment ASC');
          // Catalogo de sucursales
 
             $terms_conditions= DB::select('SELECT vc_terms_conditions FROM cat_branches  WHERE  id_branch=?', [ $branch] );
 
             
             // catalogo de niveles de entrenamiento
             $workout_plan= DB::select('SELECT id_workout_plan, vc_workout_plan_alias from cat_workout_plan  where b_status = 1 order by 1 asc');
 
 
             //Experiencia
              $previous_experience = DB::select('SELECT id_previous_experience, vc_previous_experience from cat_previous_experience  where b_status = 1 order by id_previous_experience asc');
 
              //Metas del prospecto
              $customer_goals = DB::select('SELECT id_customer_goal, vc_customer_goal from cat_customer_goals  where b_status = 1 order by 1 asc');
 
             //Coach de la prueba
             $coach = DB::select('SELECT id_team_member, vc_team_member from cat_team_member  where b_status = 1 AND i_type_team_member in(2,3) order by 1 desc');
 

             $perfomance_tests_detail= DB::select('SELECT  id_prospect_perfomance_test, vc_name, vc_last_name, vc_sur_name, i_age, i_gender , vc_email,  id_previous_experience , id_customer_goal , id_workout_plan, id_coach
             FROM log_prospect_perfomance_test WHERE id_prospect_perfomance_test=?', [$id_prospect_perfomance_test]);



 
        // return   $id_prospect_perfomance_test;
 
         return view('registry.edit',compact('civil_status','states', 'cities_local','employments','terms_conditions','workout_plan','previous_experience', 'customer_goals', 'acquisition_channel','coach', 'perfomance_tests_detail','type_menu','user_system'));
      
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
