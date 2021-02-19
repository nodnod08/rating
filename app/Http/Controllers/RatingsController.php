<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rating;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class RatingsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function rate(Request $request) {
        $validator = Validator::make($request->all(), [
            'rate' => 'required|numeric|gt:0|lt:11',
            'user_id_to_rate' => 'required|numeric',
        ]);

        if($validator->fails()){
            return response(['message' => 'Validation errors', 'errors' =>  $validator->errors(), 'status' => false], 422);
        }


        $input = $request->all();
        $id_of_user_to_rate = $input['user_id_to_rate'];

        if($input['user_id_to_rate'] == $request->user()->id) {
            $response['message'] = "You're anot allowed to rate yourself";
            $response['success'] = false;

            return response()->json($response);
        }

        $user = User::find($request->user()->id);
        $user_to_rate = User::find($id_of_user_to_rate);
        
        $check = $user->whereHas('ratings', function ($query) use ($id_of_user_to_rate){
            return $query->where('user_rated_id', '=', $id_of_user_to_rate);
        })->first();

        if($check) {
            $response['message'] = "You already rated this user";
            $response['success'] = false;

            return response()->json($response);
        } else {
            $rate = new Rating();
            $rate->rate = $input['rate'];
            $rate->user_rated_id = $user_to_rate->id;
            if(isset($input['rate_description'])) {
                $rate->rate_description = $input['rate_description'];
            }
            $rate->user()->associate($user);
            $rate->save();

            $response['message'] = "You successfuly rate this user";
            $response['success'] = true;

            return response()->json($response);
        }
    }

    public function showUsersRatings() {
        $users = User::all();

        $users->transform(function($value) {
            $ids = Rating::where('user_rated_id', $value->id)->get();
            $divisor = count($ids);
            $users_who_rate = [];
            $sum_of_rate = 0;
            foreach ($ids as $key => $us) {
                $user_who_rate = User::whereId($us->user_id)->get();
                $user_who_rate->transform(function($val) use ($us){
                    $val->rate = $us->rate;
                    $val->rate_description = $us->rate_description;
                    $val->rated_date = $us->created_at;
                    return $val;
                });
                $users_who_rate[] = $user_who_rate;
                $sum_of_rate += $us->rate;
            }
            $value->users_who_rate = $users_who_rate;
            if($sum_of_rate) {
                $value->rate_avg = (float) $sum_of_rate / $divisor;
            }
            return $value;
        });

        return $users;
    }

    public function showUserRatings($id) {
        $users = User::whereId($id)->get();

        $users->transform(function($value) {
            $ids = Rating::where('user_rated_id', $value->id)->get();
            $divisor = count($ids);
            $users_who_rate = [];
            $sum_of_rate = 0;
            foreach ($ids as $key => $us) {
                $user_who_rate = User::whereId($us->user_id)->get();
                $user_who_rate->transform(function($val) use ($us){
                    $val->rate = $us->rate;
                    $val->rated_date = $us->created_at;
                    $val->rate_description = $us->rate_description;
                    return $val;
                });
                $users_who_rate[] = $user_who_rate;
                $sum_of_rate += $us->rate;
            }
            $value->users_who_rate = $users_who_rate;
            if($sum_of_rate) {
                $value->rate_avg = (float) $sum_of_rate / $divisor;
            }
            return $value;
        });

        return $users[0];
    }
}