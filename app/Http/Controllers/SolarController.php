<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
// use Laravel\Passport\RefreshToken;
// use Laravel\Passport\Token;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class SolarController extends Controller
{
    
    function __construct(){
        $this->middleware('jwt', ['only' => ['data']]);
        $this->middleware('auth:api')->only(['logout']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
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
    public function edit($id)
    {
        //
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
    
    function register(Request $request){
        try{
            User::create([
                'name' => $request->name,    
                'email' => $request->email,    
                'password' => app('hash')->make($request->password),
            ]);
        }catch(\Exception $e){
            return response()->json(['message' => 'User not created', 418]);
        }
        return response()->json(['message' => 'User created', 201]);
    }
    
    
    function login(Request $request) {
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $key = 'muy_privada'; //Clave para cifrar y descifrar el jwt
            $payload = [
                'user' => $request->input('email'),
                'expiration_date' => Carbon::now()->addHour(),
            ];
            $jwt = JWT::encode($payload, $key, 'HS256');
            return response()->json(['user' => $request->input('email'),'token' => $jwt]);
    }
    
    function logout(Request $request) {
        $user = Auth::user()->token();
        $user->revoke();
        return response()->json(['message' => 'Logged out']);
    }
    
    function getData(){
        $lat = '37.6147109102704';
        $lng = '-3.59123541236144';
        $date = Carbon::now()->format('Y-n-d');
        $d = Carbon::now();
        $hour = $d->hour;
        $minutes = $d->minute;
        $seconds = $d->second;
        $url = sprintf("https://api.sunrise-sunset.org/json?lat=%slng=%sdate0%s", $lat, $lng, $date);
        
        $response = Http::get($url);
        
        $sunData = $response->json();
        $sunrise = $sunData['results']['sunrise'];
        $sunset = $sunData['results']['sunset'];
        
        //Hours
        $hour_sunrise = substr($sunrise, 0, 1);
        $hour_sunrise = $hour_sunrise * 3600;
        
        $hour_sunset = substr($sunset, 0, 1);
        $hour_sunset = ($hour_sunset + 12)*3600;
        
        //Minutes
        $minutes_sunrise = substr($sunrise, 2, 2);
        $minutes_sunrise = $minutes_sunrise * 60;
        
        $minutes_sunset = substr($sunset, 2, 2);
        $minutes_sunset = $minutes_sunset * 60;
        
        
        //Seconds
        $seconds_sunrise = substr($sunrise, 5, 2);
        $seconds_sunset = substr($sunset, 5, 2);
        
        //Total
        $total1 = $hour_sunrise + $minutes_sunrise + $seconds_sunrise;
        $total2 = $hour_sunset + $minutes_sunset + $seconds_sunset;
        
        $now = $hour * 3600 + $minutes * 60 + $seconds;
        
        if($now > $total2){
            return response()->json([
            'sol' => 'Ya ha atardecido'
        ]);
        }
        
        $degrees = 180 + (($now - $total1)/($total2 - $total1)) * (0 - 180);
        
        $radians = $degrees * pi() / 180;
        
        //Valores retornados
        $cos = cos($radians);
        $sin = sin($radians);
        $num1 = rand(1, 0);
        $num2 = rand(1, 0);
        $num3 = rand(1, 0);
        $num4 = rand(1, 0);
        
        return response()->json([
            'coseno' => $cos, 
            'seno' => $sin, 
            'sensor1' => $num1,
            'sensor2' => $num2,
            'sensor3' => $num3,
            'sensor4' => $num4,
        ]);
    }
    
}
