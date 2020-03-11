<?php

namespace App\Http\Controllers;

use App\Batch;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($courseId)
    {
       $batches = Batch::where('course_id', $courseId)->get();
       if(\Auth::guard('institute')->check())
           return view('batches.batches-listing', compact(['batches', 'courseId']));
       return view('admin.batches-listing', compact(['batches', 'courseId']));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($courseId)
    {
        return view('batches.create', compact('courseId'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'no_of_seats' => 'required',
            'commence_date' => 'required',
            'days' => 'required',
            'timings' => 'required',
            'courseId' => 'required'
        ]);
        $batch =  new Batch();
        $batch->no_of_seats = $request->no_of_seats;
        $batch->commence_date = $request->commence_date;
        $batch->days = implode(';',$request->days);
        $batch->timings = implode(';',$request->timings);
        $batch->course_id = $request->courseId;
        $batch->save();
        return redirect(route('batch.view',['course'=>$request->courseId]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Batch  $batch
     * @return \Illuminate\Http\Response
     * Following function returns edit page for batch
     */
    public function show(Batch $batch)
    {
        if(\Auth::guard('institute')->check()) {
            if($batch->course->institute->id == \Auth::guard('institute')->user()->id)
                return view('batches.edit', compact('batch'));
            else return response('Sorry. You are not permitted to access this page.');
        }
        return view('admin.batch-edit', compact('batch'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {

        $this->validate($request,[
            'no_of_seats' => 'required',
            'commence_date' => 'required',
            'days' => 'required',
            'timings' => 'required',
            'batchId' => 'required'
        ]);
        $batch =  Batch::find($request->batchId);
        $batch->no_of_seats = $request->no_of_seats;
        $batch->commence_date = $request->commence_date;
        $batch->days = implode(';',$request->days);
        $batch->timings = implode(';',$request->timings);
        $batch->save();
        return redirect(route('batch.view',['course'=>$batch->course->id]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Batch $batch)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Batch $batch)
    {
        $batch->delete();
        return redirect(route('batch.view',['course'=>$batch->course->id]));
    }
}
