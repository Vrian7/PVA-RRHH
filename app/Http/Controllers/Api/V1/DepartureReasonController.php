<?php

namespace App\Http\Controllers\Api\V1;

use App\DepartureReason;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/** @resource DepartureType
 *
 * Resource to retrieve, store and update departure types data
 */
class DepartureReasonController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		return DepartureReason::get();
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		return DepartureReason::findOrFail($id);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $departure_type_id
	 * @return \Illuminate\Http\Response
	 */
	public function get_reason($id) {
		return DepartureReason::where('departure_type_id', $id)->get();
	}
}
