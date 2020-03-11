<?php

namespace App\Http\Controllers;

use App\Faculty;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;

class FacultyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($courseId)
    {
        $faculties = Faculty::where('course_id', $courseId)->get();
        if(\Auth::guard('institute')->check())
            return view('faculties.faculties-listing', compact('faculties', 'courseId'));
        return view('admin.faculty-listing', compact('faculties', 'courseId'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($courseId)
    {
        return view('faculties.add', compact('courseId'));
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
            'name' => 'required',
            'description' => 'required',
            'experience' => 'required',
            'speciality' => 'required',
            'faculty_image' => 'required|max:5120'
        ]);

        $faculty = new Faculty();
        $faculty -> name = $request->name;
        $faculty->course_id = $request->courseId;
        $faculty -> description = $request->description;
        $faculty -> experience = $request->experience;
        $faculty -> speciality = $request->speciality;

        if($request->hasFile('faculty_image'))
        {
            $fileName = md5(uniqid(rand(), true)) . '.'. $request->file('faculty_image')->getClientOriginalExtension();
            $path = storage_path('app/public/faculty_profile/' . $fileName);
            $width = Image::make($request->file('faculty_image'))->width();
            $height = Image::make($request->file('faculty_image'))->height();
            if($width >= 500 && $width>=$height)
            {
                $resizedLogo = Image::make($request->file('faculty_image'))
                    ->resize(500, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
            }
            else if($height>=500 && $height>$width){
                $resizedLogo = Image::make($request->file('faculty_image'))
                    ->resize(null, 500, function ($constraint) {
                        $constraint->aspectRatio();
                    });
            }
            else $resizedLogo = Image::make($request->file('faculty_image'));
            $croppedLogo = $resizedLogo
                ->crop($request->cropped_w, $request->cropped_h, $request->cropped_x, $request->cropped_y)
                ->save($path);
            $faculty->pic_link = $path;
        }
        else return response()->json([
            'message' => 'Faculty profile picture missing!'
        ], 422);

        $faculty->save();
        return redirect(route('faculty.view', ['course' => $request->courseId]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Faculty  $faculty
     * @return \Illuminate\Http\Response
     */
    public function show(Faculty $faculty)
    {
        if(\Auth::guard('institute')->check())
            return view('faculties.edit', compact('faculty'));
        return view('admin.faculty-edit', compact('faculty'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Faculty  $faculty
     * @return \Illuminate\Http\Response
     */
    public function edit(Faculty $faculty)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Faculty  $faculty
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
            'experience' => 'required',
            'speciality' => 'required',
            'facultyId' => 'required',
            'faculty_image' => 'max:5120'
        ]);

        $faculty = Faculty::find($request->facultyId);
        $faculty -> name = $request->name;
        $faculty -> description = $request->description;
        $faculty -> experience = $request->experience;
        $faculty -> speciality = $request->speciality;

        if($request->hasFile('faculty_image'))
        {
            $fileName = md5(uniqid(rand(), true)) . '.'. $request->file('faculty_image')->getClientOriginalExtension();
            $path = storage_path('app/public/faculty_profile/' . $fileName);
            $width = Image::make($request->file('faculty_image'))->width();
            $height = Image::make($request->file('faculty_image'))->height();
            if($width >= 500 && $width>=$height)
            {
                $resizedLogo = Image::make($request->file('faculty_image'))
                    ->resize(500, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
            }
            else if($height>=500 && $height>$width){
                $resizedLogo = Image::make($request->file('faculty_image'))
                    ->resize(null, 500, function ($constraint) {
                        $constraint->aspectRatio();
                    });
            }
            else $resizedLogo = Image::make($request->file('faculty_image'));
            $croppedLogo = $resizedLogo
                ->crop($request->cropped_w, $request->cropped_h, $request->cropped_x, $request->cropped_y)
                ->save($path);
            \Storage::delete(str_replace(storage_path() . '/app/public', '', $faculty->pic_link));
            $faculty->pic_link = $path;
        }

        $faculty->save();
        return redirect(route('faculty.update', ['faculty' => $request->facultyId]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Faculty  $faculty
     * @return \Illuminate\Http\Response
     */
    public function destroy(Faculty $faculty)
    {
        \Storage::delete(str_replace(storage_path() . '/app/public', '', $faculty->pic_link));
        $faculty->delete();
        return redirect(route('faculty.view',['course'=>$faculty->course->id]));
    }
}
