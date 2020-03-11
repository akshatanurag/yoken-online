<?php

namespace App\Http\Controllers;

use App\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($courseId)
    {
        $faqs = Faq::where('course_id', $courseId)->get();
        return view('faqs.faqs-listing', compact(['faqs', 'courseId']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($courseId)
    {
        return view('faqs.create', compact('courseId'));
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
            'question' => 'required',
            'question.*' => 'required',
            'answer' => 'required',
            'answer.*' => 'required',
            'courseId' => 'required'
        ],[
            'question.*.required' => 'Please fill out all question fields.',
            'answer.*.required' => 'Please fill out all answer fields.'
        ]);

        $questions = $request->question;
        $answers = $request->answer;

        //fetch each question, answer pair and save it.
        foreach($questions as $key => $question)
        {
            $faq = new Faq();
            $faq->question = $question;
            $faq->answer = $answers[$key];
            $faq->course_id = $request->courseId;
            $faq-> save();
        }

        // if everything goes right, redirect page to faq listing page.
        return redirect(route('faq.view',['course'=>$request->courseId]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Faq  $faq
     * @return \Illuminate\Http\Response
     *
     * Following function returns edit page for faq
     */
    public function show(Faq $faq)
    {
        return view('faqs.edit', compact('faq'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Faq  $faq
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $this->validate($request,[
            'question' => 'required',
            'answer' => 'required',
            'faqId' => 'required'
        ]);

        $faq = Faq::find($request->faqId);
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq-> save();

        return redirect(route('faq.view',['course'=>$faq->course->id]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Faq  $faq
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Faq $faq)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Faq  $faq
     * @return \Illuminate\Http\Response
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();
        return redirect(route('faq.view',['course'=>$faq->course->id]));
    }
}
