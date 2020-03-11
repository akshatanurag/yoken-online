<?php

namespace App\Http\Controllers;

use App\Installment;
use Illuminate\Http\Request;

class InstallmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($courseId)
    {
        $installments = Installment::where('course_id', $courseId)->get();
        return view('installments.installments-listing', compact(['installments', 'courseId']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($courseId)
    {
        return view('installments.add', compact('courseId'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
           'frequency' => 'required',
            'amounts' => 'required',
            'durations' => 'required',
        ]);

        $installment = new Installment();
        $installment->frequency = $request->frequency;
        $installment->amounts = implode(';', $request->amounts);
        $installment->payment_duration = implode(';', $request->durations);
        $installment->course_id = $request->courseId;
        $installment->save();

        return redirect(route('installment.view', ['course'=>$request->courseId]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Installment  $installment
     * @return \Illuminate\Http\Response
     */
    public function show(Installment $installment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Installment  $installment
     * @return \Illuminate\Http\Response
     */
    public function edit(Installment $installment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Installment  $installment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Installment $installment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Installment  $installment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Installment $installment)
    {
        $installment->delete();
        return redirect(route('installment.view', ['course'=>$installment->course->id]));
    }
}
