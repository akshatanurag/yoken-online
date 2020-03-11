<?php

namespace App\Http\Controllers;

use App\Resource;
use App\Webinar;
use App\Course;
use DB;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Validation\Rule;

class ResourceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:institute', ['only'=> ['listInstitute', 'createInstitute', 'storeInstitute', 'showInstitute', 'destroyInstitute', 'updateInstitute']]);
        $this->middleware('auth', ['only'=> ['list', 'create', 'store', 'show', 'destroy', 'update']]);
        $this->middleware('isAdmin', ['only'=> ['list', 'create', 'store', 'show', 'destroy', 'update']]);
    }

    public function listResources()
    {
        return view('admin.resources', [
            'resources' => Resource::get()
        ]);
    }

    public function create()
    {
        return view('admin.resources-create', [
            'webinars' => Webinar::all(),
            'courses' => Course::all()
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|between:3,50',
            'description' => 'required|string',
            'image' => 'required|image|max:5120',
            'embed_code' => 'required|string',
            'expiry' => 'nullable|date',
            'webinar_id' => 'nullable|exists:webinars,id',
            'course_id' => 'nullable|exists:courses,id'
        ]);
        $resource = new Resource;
        $resource->name = $request->name;
        $resource->description = $request->description;
        $fileName = md5(uniqid(rand(), true)) . '.'. $request->file('image')->getClientOriginalExtension();
        $path = storage_path('app/public/resources/' . $fileName);
        Image::make($request->file('image'))->save($path);
        $resource->image = $path;
        $resource->embed_code = $request->embed_code;
        $resource->expiry = $request->expiry;
        $resource->webinar_id = $request->webinar_id;
        $resource->course_id = $request->course_id;
        $resource->save();
        return redirect(route('admin.resources'));
    }

    public function show(Resource $resource)
    {
        return view('admin.resources-edit', [
            'resource' => $resource,
            'webinars' => Webinar::all(),
            'courses' => Course::all()
        ]);
    }

    public function destroy(Resource $resource)
    {
        $resource->delete();
        return redirect(route('admin.resources'));
    }

    public function update(Resource $resource, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|between:3,50',
            'description' => 'required|string',
            'image' => 'nullable|image|max:5120',
            'embed_code' => 'required|string',
            'expiry' => 'nullable|date',
            'webinar_id' => 'nullable|exists:webinars,id',
            'course_id' => 'nullable|exists:courses,id'
        ]);
        $resource->name = $request->name;
        $resource->description = $request->description;
        if ($request->file('image')) {
            $fileName = md5(uniqid(rand(), true)) . '.'. $request->file('image')->getClientOriginalExtension();
            $path = storage_path('app/public/resources/' . $fileName);
            Image::make($request->file('image'))->save($path);
            $resource->image = $path;
        }
        $resource->embed_code = $request->embed_code;
        $resource->expiry = $request->expiry;
        $resource->webinar_id = $request->webinar_id;
        $resource->course_id = $request->course_id;
        $resource->save();
        return redirect()->back();
    }



    public function listResourcesInstitute()
    {
        return view('institute.resources', [
            'resources' => Resource::where('course.institute_id', \Auth::guard('institute')->user()->id)->get()
        ]);
    }

    public function createInstitute()
    {
        return view('institute.resources-create', [
            'courses' => Course::where('institute_id', \Auth::guard('institute')->user()->id)->get()
        ]);
    }

    public function storeInstitute(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|between:3,50',
            'description' => 'required|string',
            'image' => 'required|image|max:5120',
            'embed_code' => 'required|string',
            'expiry' => 'nullable|date',
            'course_id' => 'required|exists:courses,id'
        ]);
        $resource = new Resource;
        $resource->name = $request->name;
        $resource->description = $request->description;
        $fileName = md5(uniqid(rand(), true)) . '.'. $request->file('image')->getClientOriginalExtension();
        $path = storage_path('app/public/resources/' . $fileName);
        Image::make($request->file('image'))->save($path);
        $resource->image = $path;
        $resource->embed_code = $request->embed_code;
        $resource->expiry = $request->expiry;
        $resource->course_id = $request->course_id;
        $resource->save();
        return redirect(route('institute.resources'));
    }

    public function showInstitute(Resource $resource)
    {
        return view('institute.resources-edit', [
            'resource' => $resource,
            'courses' => Course::all()
        ]);
    }

    public function destroyInstitute(Resource $resource)
    {
        $resource->delete();
        return redirect(route('institute.resources'));
    }

    public function updateInstitute(Resource $resource, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|between:3,50',
            'description' => 'required|string',
            'image' => 'nullable|image|max:5120',
            'embed_code' => 'required|string',
            'expiry' => 'nullable|date',
            'course_id' => 'required|exists:courses,id'
        ]);
        $resource->name = $request->name;
        $resource->description = $request->description;
        if ($request->file('image')) {
            $fileName = md5(uniqid(rand(), true)) . '.'. $request->file('image')->getClientOriginalExtension();
            $path = storage_path('app/public/resources/' . $fileName);
            Image::make($request->file('image'))->save($path);
            $resource->image = $path;
        }
        $resource->embed_code = $request->embed_code;
        $resource->expiry = $request->expiry;
        $resource->course_id = $request->course_id;
        $resource->save();
        return redirect()->back();
    }
}
