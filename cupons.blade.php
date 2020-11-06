@extends('layouts.primary_template')
@section('seccion')

<div class="container">

    @include('common.errors')
    @include('common.success')



    <div class="row justify-content-md-center py-4">

        <div class="col-md-3 mb-3 text-center">
            <ul class="list-group mb-3">
                <li class="list-group-item d-flex justify-content-between lh-condensed">
                    <div class="col-md-mx-n-6 mb-3  ">
                        <h1> <i class="fas fa-ticket-alt text-info"></i></h1>
                    </div>
                    <div class="col-md-mx-n-6 mb-3">
                        <h6 class="my-0 text-muted">Cupones<br><small><b> Hoy:</b></small></h6>
                        <h3 class="my-0"><b>{{$cupons_created_today[0]->i_cupons_today}}</b></h3>
                    </div>
                </li>

                <li class="list-group-item d-flex justify-content-between lh-condensed">
                    <div class="col-md-12 mb-3 ">
                        <small class="text-info"><i class="fas fa-sticky-note text-info"></i> Total:<b>
                                {{ $cupons_created_total[0]->i_cupons_total}} </b></small> |
                        <small class="text-success"> <i class="far fa-check-square text-success"></i> Usados:<b>
                                {{ $cupons_used_total[0]->i_cupons_used}} </b> </small>
                    </div>
                </li>
            </ul>
        </div>

    </div>


    <!-- INFORMACION PERSONAL -->

    <div class="row mb-3">

        <div class="input-group mb-1 mt-2"> <button class="btn btn-outline-dark disabled" aria-disabled="true"
                type="button" id="button-addon1">Buscar Correo</button>
            <input id="filtrar" type="text" class="form-control"
                placeholder="Ingresa el  E-mail o codigo de cupón por buscar">
        </div>

        <div class="card-header col-md-12 order-md-1 mb-1  text-right">
            <p> <i class="fas fa-project-diagram text-info"> </i> <b> Total de cupones generados</b></p>
        </div>
        <div class="col-md-12 order-md-1 ">

            <div class="row">


                <table class="table table-hover table-bordered ">
                    <thead>
                        <tr>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Tipo de cliente</th>
                            <th class="text-center">Codigo de cupón</th>
                            <th class="text-center">Valido por</th>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">E-mail</th>
                            <th class="text-center">Celular</th>
                            <th class="text-center">Fecha Expiración</th>
                            <th class="text-center">Canjeado</th>
                        </tr>
                    </thead>
                    <tbody class="buscar">
                        @foreach ($cupons_detail as $tb_cupons_detail)
                        <tr>
                            <td class="text-center">
                                <p class="text-dark"> <small><small> {{date("Y-m-d",strtotime($tb_cupons_detail->dt_created))}}
                                    </small></small></p>
                            </td>

                            <td class="text-center">
                                <small> <small> 
                                    

                                @switch($tb_cupons_detail->id_client_type)

                                @case(1)
                                <p class="text-warning"> Prospecto informe </p>
                                @break
                                @case(2)
                                <p class="text-primary">  Prospecto prueba </p>
                                @break
                                @case(3)
                                <p class="text-success">  Cliente activo </p>
                                <kbd class="bg-muted" > ID:{{$tb_cupons_detail->id_client}} </kbd>
                                @break
                                @case(8)
                                <p class="text-info"> Paq. Visita Vigente </p>
                                <kbd class="bg-muted" > ID:{{$tb_cupons_detail->id_client}} </kbd>
                                @break
                                @case(9)
                                <p class="text-danger"> Paq. Visita expirada </p>
                                <kbd class="bg-muted" > ID:{{$tb_cupons_detail->id_client}} </kbd>
                                @break
                                
                                @case(4)
                                <p class="text-danger">  Cliente suspendido </p> 
                                <kbd class="bg-muted" > ID: {{$tb_cupons_detail->id_client}} </kbd>
                                @break
                                @case(5)
                                <p class="text-primary">  Prospecto portal </p>
                                @break
                                @case(6)
                                <p class="text-dark">  Prospecto portal mensaje</p> 
                                @break

                                @default <p class="text-secondary">No
                                            aplica</p> 
                                @break

                                @endswitch

                            </small>  </small>
                            </td>


                            <td class="text-center"><small><code>
                                    {{utf8_encode($tb_cupons_detail->vc_promotion_code)}} </code> </small></td>

                            <td class="text-center">
                                <samp><small>
                                        <small> Cantidad : {{utf8_encode($tb_cupons_detail->i_unity)}}
                                            {{utf8_encode($tb_cupons_detail->vc_promotion_type)}},
                                            Tipo:
                                            {{utf8_encode($tb_cupons_detail->vc_promotion_promotion_exchange_type)}}
                                        </small> </small> </samp>
                            </td>

                            

                            <td class="text-center">
                                <p class="text-success"><small> {{ utf8_encode($tb_cupons_detail->vc_name) }}
                                    </small></p>
                            </td>

                            <td class="text-center">
                                <p class="text-info"><small> {{ utf8_encode($tb_cupons_detail->vc_email) }}
                                    </small></p>
                            </td>

                            <td class="text-center">
                                <p class="text-muted"><small> {{ utf8_encode($tb_cupons_detail->vc_cellphone_number) }}
                                    </small></p>
                            </td>

                            <td class="text-center">
                                <p class="text-danger"> <small><small>
                                        {{date("Y-m-d",strtotime($tb_cupons_detail->dt_expiration))}}
                                    </small></small></p>
                            </td>

                            <td class="text-center">
                                @switch($tb_cupons_detail->i_used )
                                @case(0)
                                <small><kbd class="bg-muted"> No canjeado </small> </kbd>
                                @break
                                @case(1)
                                <small><kbd class="bg-success"> {{  $tb_cupons_detail->dt_used }} </small> </kbd>
                                @break
                                @endswitch
                            </td>

                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div> <!-- ROW2-->
        </div>

    </div>
    @endsection
