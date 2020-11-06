<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


class cityController extends Controller
{
    public function home() {
        // Propiedades de Navegacion
        
        $branch='1';
        $type_menu='0';
        $membership_models = DB::select('SELECT id_membership_model, vc_membership_model, vc_dashboard_description, round(d_amount, 2) as d_amount , round ((i_percent_off *100),0) as i_percent_off FROM cat_membership_models WHERE b_status = 1  ORDER BY i_promotion_available ASC');
        // return  $type_menu;
         return view('showstates/index',compact('type_menu','user_system','membership_models'));

        }


        public function byState($id_state) {
            // Propiedades de Navegacion
            
            $branch='1';
            $type_menu='0';    
            $cities_models =  DB::statement('SET @counter=0;');
            $cities_models = DB::select('SELECT @counter:=@counter+1 as i_counter, id_state, id_city, vc_city  FROM cat_cities WHERE id_state = ? ORDER BY vc_city ASC',[$id_state]);
            
            // $id_membership= array( 'id',)

           // foreach($membership_models as $i){
             //   $id_membership['id']= $i->id_membership_model_client  ;}

            // $resources = array(
              //  array("name" => "Resource 1", "names" => "resource1"),
               // array("name" => "Resource 2", "names"=> "resource2")
               // );

            foreach( $cities_models as $i_mm){
                $cat_cities[$i_mm->i_counter]= array ($i_mm->id_state,  $i_mm->id_city ,  utf8_encode($i_mm->vc_city) );
          }
    
            // return  $type_menu;
             return $cat_cities ;
    
            }


}
