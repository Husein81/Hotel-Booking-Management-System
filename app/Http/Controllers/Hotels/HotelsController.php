<?php

namespace App\Http\Controllers\Hotels;

use App\Http\Controllers\Controller;
use App\Models\Apartment\Apartment;
use Illuminate\Http\Request;
use App\Models\Booking\Booking;
use App\Models\Hotel\Hotel;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Laravel\Ui\Presets\React;
use Stripe\Stripe;
use Stripe\Exception\InvalidRequestException;
class HotelsController extends Controller
{
    public function rooms( $id ) {
        $getRooms = Apartment::select()->orderBy('id','desc')->take(6)->where('hotel_id',$id)->get();


        return view('hotels.rooms',compact('getRooms'));
    }
    public function roomDetails( $id ) {
        $getRoom = Apartment::find($id);
        $gethotel = Hotel::find($id);

        return view('hotels.roomdetails',compact('getRoom'));
    }
    public function roomBooking(Request $request, $id ) {

        $room = Apartment::find($id);
        $hotel = Hotel::find($id);

        $currentDate = Carbon::now();

        if( $currentDate->lessThan(Carbon::parse($request->check_in)) && $currentDate->lessThan(Carbon::parse($request->check_out))) {

            if (Carbon::parse($request->check_in)->lessThan(Carbon::parse($request->check_out))) {

                $datetime1 = new DateTime($request->check_in);
                $datetime2 = new DateTime($request->check_out);
                $interval = $datetime1->diff($datetime2);
                $days = $interval->format('%a');

                $bookngRooms = Booking::create([
                    "name" => $request->name,
                    "email" => $request->email,
                    "phone_number" => $request->phone_number,
                    "check_in" => $request->check_in,
                    "check_out" => $request->check_out,
                    "days" => $days,
                    "price" => $days * $room->price,
                    "user_id" => Auth::user()->id,
                    "room_name" => $room->name,
                    // "hotel_name" => $hotel->name
                ]);
                $total_price = $days*$room->price;
                $price = Session::put('price',$total_price);

                return Redirect::route('hotel.payment');
            } else {
              return Redirect::route('hotel.rooms.details',$room->id)->with(['error'=>'check out date should be greater than check in date']);
            }
        } else {
            return Redirect::route('hotel.rooms.details',$room->id)->with(['error' => 'Choose dates in the future, invalid check-in or check-out dates']);
        }
    }



    public function paymentForm(){

        return view('hotels.payment');
    }


    public function checkout(Request $request){
        Stripe::setApiKey(env('STRIPE_SK'));

        $token = $request->stripeToken;

        if (!$token) {
            throw new InvalidRequestException("Invalid payment source.", 400);
        }
        \Stripe\Charge::create([
            'amount' => Session::get('price'),
            'currency'=>'usd',
            'source' => $token

        ]);

        Session::flash('success','Payment has been successfully');
        return redirect()->route('hotel.success');

    }

    public function success(){



        Session::forget('price');
        return view('hotels.success');
    }
}

