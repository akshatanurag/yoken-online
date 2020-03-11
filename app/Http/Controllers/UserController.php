<?php

namespace App\Http\Controllers;

use DB;
use Hash;
use View;
use Validator;
use App\Webinar;
use App\Enrollment;
use App\Resource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the user details.
     */
    public function index()
    {
        return View::make('user.index', [
            'user' => auth()->user()
        ]);
    }

    /**
     * Update the user details.
     */
    public function update(Request $request)
    {
        if (isset($_GET['password'])) {
            return $this->updatePassword($request);
        } else {
            return $this->updateUser($request);
        }
    }

    /**
     * Update the user personal details.
     */
    public function updateUser(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore(auth()->user()->id)
            ],
            'phone' => 'required|numeric',
        ]);
        auth()->user()->name = $request->input('name');
        auth()->user()->email = $request->input('email');
        auth()->user()->phone = $request->input('phone');
        auth()->user()->save();

        return View::make('user.index', [
            'user' => auth()->user(),
            'submitted_1' => true
        ]);
    }

    /**
     * Update the user password.
     */
    public function updatePassword(Request $request)
    {
        Validator::extend('old_password', function ($attribute, $value, $parameters, $validator) {
            return Hash::check($value, current($parameters));
        });
        $this->validate($request, [
            'current-password' => 'required|old_password:' . auth()->user()->password,
            'password' => 'required|min:6|confirmed'
        ], [
            'current-password.old_password' => 'Current password entered is incorrect'
        ]);
        auth()->user()->password = bcrypt($request->input('password'));
        auth()->user()->save();

        return View::make('user.index', [
            'user' => auth()->user(),
            'submitted_2' => true
        ]);
    }

    /**
     * Display the user enrolled courses.
     */
    public function showCourses()
    {
        return View::make('user.course', [
            'enrollments' => auth()->user()->enrollments()->orderBy('id', 'DESC')->get()
        ]);
    }

    /**
     * Display the user registered webinars.
     */
    public function showWebinars()
    {
        return View::make('user.webinar', [
            'registrations' => auth()->user()->registrations()->orderBy('id', 'DESC')->get()
        ]);
    }
 
    /**
     * Display the user available resources.
     */
    public function showResources()
    {
        $registrations_webinars = auth()->user()->registrations()->where(function($query) {
            $query->whereHas('payment', function($query) {
                $query->where('payment_status', 'Credit');
            })->orWhereRaw(DB::raw('(`base_fees` - (`base_discount` * `base_fees` / 100)) <= 0'));
        })->get()->pluck('webinar_id');
        $webinar_resources = Resource::where(function($query) {
            $query->whereDate('expiry', '>=', date('Y-m-d'))->orWhere(function($query) {
                $query->whereNull('expiry');
            });
        })->whereIn('webinar_id', $registrations_webinars)->paginate(15);

        $enrollments_courses = auth()->user()->enrollments()->where(function($query) {
            $query->whereHas('payment', function($query) {
                $query->where('payment_status', 'Credit');
            })->orWhereRaw(DB::raw('(`base_fees` - (`base_discount` * `base_fees` / 100) - `yoken_rebate` - `institute_rebate`) <= 0'));
        })->orWhere('type', 0)->get()->pluck('batch.course_id');
        $course_resources = Resource::where(function($query) {
            $query->whereDate('expiry', '>=', date('Y-m-d'))->orWhere(function($query) {
                $query->whereNull('expiry');
            });
        })->whereIn('course_id', $enrollments_courses)->paginate(15, ['*'], 'cpage');

        return view('user.resources', [
            'resources' => [
                'courses' => $course_resources,
                'webinars' => $webinar_resources
            ]
        ]);
    }

    public function viewResource(Resource $resource)
    {
        $registrations_webinars = auth()->user()->registrations()->where(function($query) {
            $query->whereHas('payment', function($query) {
                $query->where('payment_status', 'Credit');
            })->orWhereRaw(DB::raw('(`base_fees` - (`base_discount` * `base_fees` / 100)) <= 0'));
        })->get()->pluck('webinar_id');
        $enrollments_courses = auth()->user()->enrollments()->where(function($query) {
            $query->whereHas('payment', function($query) {
                $query->where('payment_status', 'Credit');
            })->orWhereRaw(DB::raw('(`base_fees` - (`base_discount` * `base_fees` / 100) - `yoken_rebate` - `institute_rebate`) <= 0'));
        })->orWhere('type', 0)->get()->pluck('batch.course_id');

        if (($resource->webinar_id !== null && in_array($resource->webinar_id, $registrations_webinars->toArray())) ||
            $resource->course_id !== null && in_array($resource->course_id, $enrollments_courses->toArray())) {
            return view('user.resources-view', compact("resource"));
        } else {
            return redirect(route('user.resources'));
        }
    }
}
