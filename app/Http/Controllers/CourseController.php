<?php

namespace App\Http\Controllers;

use App\Category;
use App\Course;
use App\Batch;
use App\Institute;
use View;
use DB;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;

class CourseController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->input('q');
        $selectedCategories = $request->input('categories');
        $selectedLocations = $request->input('locations');
        $categories = new Category();
        $coursesBuilder = $courses = Course::with('institute')->where(function ($query) use ($keyword) {
            $query->where(app('db')->raw('REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(`name`, "(", " "), ")", " "), ",", " "), ".", " "), "/", " ")'), "like", str_replace("(", " ", str_replace(")", " ", str_replace(".", " ", str_replace(",", " ", str_replace("/", " ", $keyword))))))
                ->orWhere(app('db')->raw('REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(`name`, "(", " "), ")", " "), ",", " "), ".", " "), "/", " ")'), "like", str_replace("(", " ", str_replace(")", " ", str_replace(".", " ", str_replace(",", " ", str_replace("/", " ", $keyword))))) .' %')
                ->orWhere(app('db')->raw('REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(`name`, "(", " "), ")", " "), ",", " "), ".", " "), "/", " ")'), "like", '% '. str_replace("(", " ", str_replace(")", " ", str_replace(".", " ", str_replace(",", " ", str_replace("/", " ", $keyword))))))
                ->orWhere(app('db')->raw('REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(`name`, "(", " "), ")", " "), ",", " "), ".", " "), "/", " ")'), "like", '% '. str_replace("(", " ", str_replace(")", " ", str_replace(".", " ", str_replace(",", " ", str_replace("/", " ", $keyword))))) .' %');
        })->where('status', 1);

        if ($request->has('categories')) {
            $coursesBuilder = $coursesBuilder->category($selectedCategories);
        }
        if ($request->has('locations')) {
            $coursesBuilder = $coursesBuilder->location($selectedLocations);
        }
        $locations = \App\Institute::distinct('location')->pluck('location');
        $final_courses = $coursesBuilder->orderByRaw('RAND()')->paginate(15);
        return View::make('courses.index', [
            'courses' => $final_courses,
            'keyword' => $keyword,
            'categories' => $categories->all(),
            'selectedCategories' => $selectedCategories,
            'locations' => $locations,
            'selectedLocations' => $selectedLocations,
        ]);
    }

        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\Http\Response
         */
    public function create()
    {
        return View::make('courses.add-course', [
            'categories' => Category::all()
        ]);
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
            'name' => 'required|min:0|max:30',
            'description' => 'required|min:0|max:1800',
            'category' => 'required',
            'course_image' => 'required|max:5120',
            'demo_classes' => 'required',
            'classes_per_week' => 'required',
            'one_time_fees' => 'required',
            'discount' => 'required',
            'duration' => 'required|',
            'duration_type' => 'required',
            'syllabus' => 'required|max:4800',
        ]);
        $course = new Course();
        $course->institute_id = \Auth::guard('institute')->user()->id;
        $course->name = $request->name;
        $course->description = $request->description;
        $course->demo_classes = $request->demo_classes;
        $fileName = md5(uniqid(rand(), true)) . '.'. $request->file('course_image')->getClientOriginalExtension();
        $path = storage_path('app/public/course_logos/' . $fileName);
        $width = Image::make($request->file('course_image'))->width();
        $height = Image::make($request->file('course_image'))->height();
        if ($width >= 500 && $width>=$height) {
            $resizedLogo = Image::make($request->file('course_image'))
                ->resize(500, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
        } elseif ($height>=500 && $height>$width) {
            $resizedLogo = Image::make($request->file('course_image'))
                ->resize(null, 500, function ($constraint) {
                    $constraint->aspectRatio();
                });
        } else {
            $resizedLogo = Image::make($request->file('course_image'));
        }
        $croppedLogo = $resizedLogo
            ->crop($request->cropped_w, $request->cropped_h, $request->cropped_x, $request->cropped_y)
            ->save($path);
        $course->pic_link = $path;
        //$course->pic_link = $request->file('cropped_image')->store('course_logos');
        $course->classes_per_week = $request->classes_per_week;
        $course->fees = $request->one_time_fees;
        $course->discount = $request->discount;
        $course->duration = $request->duration;
        $course->duration_type = $request->duration_type;
        $course->syllabus = $request->syllabus;
        \DB::transaction(function () use ($course, $request) {
            $course->save();
            foreach ($request->category as $category) {
                \DB::table('category_course')->insert([
                    'category_id' => $category,'course_id' => $course->id
                ]);
            }
        });
        session()->flash('status', 'Course has been added.');
        return redirect(route('course.create'));
    }

        /**
         * Display the specified resource.
         *
         * @param  \App\Course  $course
         * @return \Illuminate\Http\Response
         */
    public function show(Course $course)
    {
        return View::make('courses.course', [
            'course' => $course,
        ]);
    }
    public function listCourses()
    {
        $courses = Course::where('institute_id', \Auth::guard('institute')->user()->id)->get();
        return View::make('courses.courses-listing', [
            'courses' => $courses,
        ]);
    }
        /**
         * Show the form for editing the specified resource.
         *
         * @param  \App\Course  $course
         * @return \Illuminate\Http\Response
         */
    public function edit(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:0|max:30',
            'description' => 'required|min:0|max:1800',
            'category' => 'required',
            'course_image' => 'max:5120',
            'demo_classes' => 'required',
            'classes_per_week' => 'required',
            'one_time_fees' => 'required',
            'discount' => 'required',
            'duration' => 'required|',
            'duration_type' => 'required',
            'syllabus' => 'required|max:4800',
        ]);
        $course = Course::find($request->id);
        $course->name = $request->name;
        $course->description = $request->description;
        $course->demo_classes = $request->demo_classes;
        if ($request->hasFile('course_image')) {
            $fileName = md5(uniqid(rand(), true)) . '.'. $request->file('course_image')->getClientOriginalExtension();
            $path = storage_path('app/public/course_logos/' . $fileName);
            $width = Image::make($request->file('course_image'))->width();
            $height = Image::make($request->file('course_image'))->height();
            if ($width >= 500 && $width>=$height) {
                $resizedLogo = Image::make($request->file('course_image'))
                    ->resize(500, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
            } elseif ($height>=500 && $height>$width) {
                $resizedLogo = Image::make($request->file('course_image'))
                    ->resize(null, 500, function ($constraint) {
                        $constraint->aspectRatio();
                    });
            } else {
                $resizedLogo = Image::make($request->file('course_image'));
            }
            $croppedLogo = $resizedLogo
                ->crop($request->cropped_w, $request->cropped_h, $request->cropped_x, $request->cropped_y)
                ->save($path);
            \Storage::delete(str_replace(storage_path() . '/app/public', '', $course->pic_link));
            $course->pic_link = $path;
        }

        $course->classes_per_week = $request->classes_per_week;
        $course->fees = $request->one_time_fees;
        $course->discount = $request->discount;
        $course->duration = $request->duration;
        $course->duration_type = $request->duration_type;
        $course->syllabus = $request->syllabus;
        \DB::transaction(function () use ($course, $request) {
            $course->save();
            \DB::table('category_course')->where('course_id', $request->id)->delete();
            foreach ($request->category as $category) {
                \DB::table('category_course')->insert([
                    'category_id' => $category,'course_id' => $request->id
                ]);
            }
        });
        session()->flash('status', 'Course has been Updated.');
        return redirect(route('course.edit.view', ['course'=>$request->id]));
    }

    public function showEdit(Course $course)
    {
        $selectedCategories = $course->categories()->pluck('categories.id');
        $categories = Category::all();
        if (\Auth::guard('institute')->check()) {
            if ($course->institute->id == \Auth::guard('institute')->user()->id) {
                return view('courses.course-edit', compact('course', 'categories', 'selectedCategories'));
            } else {
                return response('Sorry. You are not permitted to access this page.');
            }
        }
        return view('admin.course-edit', compact('course', 'categories', 'selectedCategories'));
    }
        /**
         * Update the specified resource in storage.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \App\Course  $course
         * @return \Illuminate\Http\Response
         */
    public function update(Request $request, Course $course)
    {
        //
    }

        /**
         * Remove the specified resource from storage.
         *
         * @param  \App\Course  $course
         * @return \Illuminate\Http\Response
         */
    public function destroy(Course $course)
    {
        //
    }

    public function preview(Course $course)
    {
        if ((\Auth::check() && \Auth::user()->role==0) || $course->institute_id == \Auth::guard('institute')->user()->id) {
            return view('courses.preview', compact('course'));
        }
        return response('Whoops! Looks like you do not belong here.', 403);
    }

    public function activate(Course $course)
    {
        $course->status = 1;
        $course->save();
        return redirect(route('courses.list'));
    }

    public function deactivate(Course $course)
    {
        $course->status = 0;
        $course->save();
        return redirect(route('courses.list'));
    }
}
