<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PickupLocation;
use App\Http\Resources\Address\PickupLocationCollection;
use App\Http\Resources\Address\PickupLocation as PickupLocationResource;
use Carbon\Carbon;

class PickupLocationController extends Controller
{
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
        // $location = PickupLocation::where(['city_id' => $ ]);
        return response()->json(request()->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showLocation(Request $request)
    {
        $location = PickupLocation::where(['city_id' => $request->cityId, 'state_region_id' => $request->regionId])->get();
        return new PickupLocationCollection($location);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setLocation(Request $request)
    {
        $loc = PickupLocation::find($request->pickupAddressId);
        if (!$loc) {
            return;
        }
        $user = $request->user();
        if (count($user->pickups) > 0) {
            $user->pickups()->detach();
        }

        $user->pickups()->attach($request->pickupAddressId, ['default_pickup' => $request->defaultPickup ? 1 : 0]);
        return new PickupLocationResource($loc);
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
