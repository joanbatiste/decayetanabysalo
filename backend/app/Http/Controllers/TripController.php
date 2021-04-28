<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use Illuminate\Database\QueryException;

class TripController extends Controller
{
    //Crear un trip
    public function tripCreate(Request $request){
        
        $title = $request->input('title');
        $destination = $request->input('destination');
        $description = $request->input('description');
        $link = $request->input('link');
        $userId = $request->input('userId');

        try{
            return Trip::create([
                'title' => $title,
                'destination' => $destination,
                'description' => $description,
                'link' => $link,
                'userId' => $userId
            ]);
        }catch(QueryException $error){
            $eCode = $error->errorInfo[1];
            if($eCode == 1062){
                return response()->json([
                    'error' => 'No se puede publicar tu viaje'
                ]);
            }
        }

    }

    //Traer todos los trips de un user
    public function findTripsByUserId($userid){
        try{
            return Trip::all()->where('userId', '=', $userid);

        }catch(QueryException $error){
            return $error;

        }
    }

    //Editar un trip creado
    public function tripUpdate(Request $request, $userid, $tripid){

        $trip = Trip::find($tripid);
        //Comprobamos que el trip existe
        if(!$trip){
            return response()->json([
                'error'=> 'El viaje no existe'
            ]);
        }
        //Comprobamos que el user es el propietario del trip
        if($trip['userId'] != $userid){
            return response()->json([
                'error'=> 'No estas autorizado a editar este viaje'
            ]);
        }
        //Intentamos editar el trip
        try{
            return $trip->update([
                'title'=>$request->title,
                'destination'=>$request->destination,
                'description'=>$request->description,
                'link'=>$request->link
            ]);
        }catch(QueryException $error){
            return $error;
        };

    }

    //Eliminar un Trip 
    public function deleteTrip(Request $request,$userid, $tripid){
        $trip = Trip::find($tripid);
        //Comprobamos que existe el trip
        if(!$trip){
            return response()->json([
                'error'=> 'El viaje no existe'
            ]);
        }
        //Comprobamos la identidad del creador
        if($trip['userId'] != $userid){
            return response()->json([
                'error'=> 'No estas autorizado a eliminar este viaje'
            ]);
        }
        try{
            return Trip::destroy([
                'id'=>$tripid,
            ]);
        }catch (QueryException $error){
            return $error;
        };
    }
}
