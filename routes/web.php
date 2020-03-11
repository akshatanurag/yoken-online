<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Http\Request;

Route::group(['web'], function () {
    Auth::routes();
    Route::get('/', function (Request $request) {
        if ($request->has('payment_request_id')) {
            try {
                if (\App::environment('local', 'testing')) {
                    $instamojoApi = new \Instamojo\Instamojo(getenv('INSTA_API_KEY'), getenv('INSTA_AUTH_TOKEN'), 'https://test.instamojo.com/api/1.1/');
                } else {
                    $instamojoApi = new \Instamojo\Instamojo(getenv('INSTA_API_KEY'), getenv('INSTA_AUTH_TOKEN'));
                }
                $response = $instamojoApi->paymentRequestStatus($request->input('payment_request_id'));
                $response = $response["payments"];
                if (empty($response[0]) === false && $response[0]["status"] == "Credit") {
                    if (auth()->user()) {
                        \Mail::to(auth()->user())->send(new Payment());
                    }
                    return redirect("/?m=1");
                }
            } catch (Exception $e) {
                return redirect("/?m=2");
            }
        }
        return view('index');
    });
    Route::get('/about-us', function () {
        return view('about-us');
    });
    Route::get('/terms-and-conditions', function () {
        return view('terms-and-conditions');
    });
    Route::get('/privacy-policy', function () {
        return view('privacy-policy');
    });
    Route::get('/contact-us', function () {
        return view('contact-us');
    });
    Route::get('/faqs', function () {
        return view('faqs');
    });
    Route::get('/webinar-thank-you', function (Request $request) {
        if ($request->has('payment_request_id')) {
            try {
                if (\App::environment('local', 'testing')) {
                    $instamojoApi = new \Instamojo\Instamojo(getenv('INSTA_API_KEY'), getenv('INSTA_AUTH_TOKEN'), 'https://test.instamojo.com/api/1.1/');
                } else {
                    $instamojoApi = new \Instamojo\Instamojo(getenv('INSTA_API_KEY'), getenv('INSTA_AUTH_TOKEN'));
                }
                $response = $instamojoApi->paymentRequestStatus($request->input('payment_request_id'));
                $response = $response["payments"];
                if (empty($response[0]) === false && $response[0]["status"] == "Credit") {
                    if (auth()->user()) {
                        \Mail::to(auth()->user())->send(new WebinarPayment());
                    }
                    return redirect("/webinar-thank-you?m=1");
                }
            } catch (Exception $e) {
                return redirect("/webinar-thank-you?m=2");
            }
        }
        return view('/webinar-thank-you');
    });
    Route::get('/webinar-registration-acknowledgement', function () {
        return view('webinar-registration-acknowledgement');
    });
    Route::get('/browse-courses', 'CourseController@index');
    Route::get('/home', 'HomeController@index');
    Route::get('/view-course/{course}', 'CourseController@show');
    Route::get('/enroll/{course}', 'EnrollmentController@index');
    Route::post('/enroll', 'EnrollmentController@store');
    Route::post('/apply-coupon', 'CouponController@apply');
    Route::get('/auth/facebook', 'Auth\LoginController@redirectToProvider');
    Route::get('/auth/facebook/callback', 'Auth\LoginController@handleProviderCallback');
    Route::get('/webinars', 'WebinarController@index');
    Route::get('/webinar/register/{webinar}', 'WebinarController@showRegister');
    Route::post('/webinar/register/{webinar}', 'WebinarController@register');
    Route::get('/webinar-live', 'WebinarController@host');

    Route::prefix('user')->group(function () {
        Route::get('/', 'UserController@showCourses')->name('user.course.edit');
        Route::get('/webinars', 'UserController@showWebinars')->name('user.webinar.edit');
        Route::get('/resources', 'UserController@showResources')->name('user.resources');
        Route::get('/resources/{resource}', 'UserController@viewResource')->name('user.resources-view');
        Route::get('/profile', 'UserController@index')->name('user.profile.edit');
        Route::post('/profile', 'UserController@update');
    });
    Route::prefix('institute')->group(function () {
        Route::get('/', 'InstituteController@index')->name('institute.dashboard');
        Route::get('/login', 'Auth\InstituteLoginController@showLoginForm')->name('institute.login');
        Route::post('/login', 'Auth\InstituteLoginController@login')->name('institute.login.submit');
        Route::get('/my-institute', 'InstituteController@showDetails');
        Route::get('/edit-institute', 'InstituteController@showEdit');
        Route::post('/edit-institute', 'InstituteController@update');
        Route::get('/institute-logo/{name}', 'InstituteController@getInstituteLogo');
        //
        Route::get('/add-course', 'CourseController@create')->name('course.create');
        Route::post('/add-course', 'CourseController@store')->name('course.store');
        Route::get('/courses', 'CourseController@listCourses')->name('courses.list');
        Route::get('/edit-course/{course}', 'CourseController@showEdit')->name('course.edit.view');
        Route::post('/edit-course', 'CourseController@edit')->name('course.edit');
        Route::post('/activate-course/{course}', 'CourseController@activate')->name('course.activate');
        Route::post('/deactivate-course/{course}', 'CourseController@deactivate')->name('course.deactivate');
        //
        Route::get('/batches/{course}', 'BatchController@index')->name('batch.view');
        Route::get('/add-batch/{course}', 'BatchController@create')->name('batch.create');
        Route::get('/edit-batch/{batch}', 'BatchController@show')->name('batch.update');
        Route::post('/edit-batch', 'BatchController@edit')->name('batch.update.store');
        Route::post('/add-batch', 'BatchController@store')->name('batch.store');
        Route::post('/delete-batch/{batch}', 'BatchController@destroy')->name('batch.delete');
        //
        Route::get('/faculties/{course}', 'FacultyController@index')->name('faculty.view');
        Route::get('/add-faculty/{course}', 'FacultyController@create')->name('faculty.create');
        Route::post('/add-faculty', 'FacultyController@store')->name('faculty.store');
        Route::post('/delete-faculty/{faculty}', 'FacultyController@destroy')->name('faculty.delete');
        Route::get('/edit-faculty/{faculty}', 'FacultyController@show')->name('faculty.update');
        Route::post('/edit-faculty', 'FacultyController@update')->name('faculty.update.store');
        //
        Route::get('/installments/{course}', 'InstallmentController@index')->name('installment.view');
        Route::get('/add-installment/{course}', 'InstallmentController@create')->name('installment.create');
        Route::post('/add-installment', 'InstallmentController@store')->name('installment.store');
        Route::post('/delete-installment/{installment}', 'InstallmentController@destroy')->name('installment.delete');
        Route::get('/edit-installment/{installment}', 'InstallmentController@show')->name('installment.update');
        Route::post('/edit-installment', 'InstallmentController@update')->name('installment.update.store');
        //
        Route::get('/faqs/{course}', 'FaqController@index')->name('faq.view');
        Route::get('/add-faq/{course}', 'FaqController@create')->name('faq.create');
        Route::post('/add-faq', 'FaqController@store')->name('faq.store');
        Route::post('/delete-faq/{faq}', 'FaqController@destroy')->name('faq.delete');
        Route::get('/edit-faq/{faq}', 'FaqController@show')->name('faq.update');
        Route::post('/edit-faq', 'FaqController@edit')->name('faq.update.store');
        //
        Route::get('/coupons', 'CouponController@index')->name('coupon.view');
        Route::get('add-coupon', 'CouponController@create')->name('coupon.create');
        Route::post('/add-coupon', 'CouponController@store')->name('coupon.store');
        Route::post('/delete-coupon/{coupon}', 'CouponController@destroy')->name('coupon.delete');
        //
        Route::get('/preview/{course}', 'CourseController@preview')->name('course.preview');
        Route::get('change-password', 'InstituteController@changePasswordShow');
        Route::post('change-password', 'InstituteController@changePassword')->name('change.institute.password');
        //
        Route::get('/report', 'InstituteController@report');
        Route::get('/report/download', 'InstituteController@download');
        Route::get('/report/download/all', 'InstituteController@downloadAll');
        //Resources
        Route::get('/resources', 'ResourceController@listResourcesInstitute')->name('institute.resources');
        Route::get('/resources/create', 'ResourceController@createInstitute')->name('institute.resources-create');
        Route::post('/resources/store', 'ResourceController@storeInstitute')->name('institute.resources-store');
        Route::post('/resources/delete/{resource}', 'ResourceController@destroyInstitute')->name('institute.resources-delete');
        Route::get('/resources/edit/{resource}', 'ResourceController@showInstitute')->name('institute.resources-show');
        Route::post('/resources/edit/{resource}', 'ResourceController@updateInstitute')->name('institute.resources-update');
    });
    Route::prefix('admin')->group(function () {
        Route::get('/', 'AdminController@showInstituteList');
        Route::get('/view-institutes', 'AdminController@showInstituteList');
        Route::get('/create-institute', 'AdminController@showCreateInstitute')->name('admin.show-create.institute');
        Route::post('/create-institute', 'AdminController@createInstitute')->name('admin.create.institute');
        Route::get('/edit-institute/{institute}', 'AdminController@showEditInstitute');
        Route::post('/edit-institute', 'AdminController@updateInstitute')->name('admin.update.institute');
        Route::get('/list-courses/{instituteId}', 'AdminController@listCourses')->name('admin.list.courses');
        Route::get('/preview-course/{course}', 'CourseController@preview');
        //
        Route::get('/report', 'AdminController@report');
        Route::get('/report/download', 'AdminController@download');
        Route::get('/report/download/all', 'AdminController@downloadAll');
        Route::get('/report-webinar', 'AdminController@webinarReport');
        Route::get('/report-webinar/download', 'AdminController@webinarDownload');
        Route::get('/report-webinar/download/all', 'AdminController@webinarDownloadAll');
        //
        Route::get('/edit-course/{course}', 'CourseController@showEdit')->name('course.update');
        Route::post('/edit-course', 'AdminController@updateCourse')->name('admin.update.course');
        //
        Route::get('/batches/{course}', 'BatchController@index');
        Route::get('/edit-batch/{batch}', 'BatchController@show')->name('batch.update');
        Route::post('/edit-batch', 'AdminController@updateBatch')->name('admin.update.batch');
        //
        Route::get('/faculties/{course}', 'FacultyController@index');
        Route::get('/edit-faculty/{faculty}', 'FacultyController@show')->name('faculty.update');
        Route::post('/edit-faculty', 'AdminController@updateFaculty')->name('admin.update.faculty');
        //
        Route::get('/installments/{course}', 'InstallmentController@index');
        Route::get('/edit-installment/{installment}', 'InstallmentController@show')->name('installment.update');
        Route::post('/edit-installment', 'AdminController@updateInstallment')->name('admin.update.installment');
        //
        Route::get('/coupons', 'CouponController@index_admin')->name('coupon.view-admin');
        Route::get('add-coupon', 'CouponController@create_admin')->name('coupon.create-admin');
        Route::post('/add-coupon', 'CouponController@store_admin')->name('coupon.store-admin');
        Route::post('/delete-coupon/{coupon}', 'CouponController@destroy_admin')->name('coupon.delete-admin');
        //Webinars
        Route::get('/webinars', 'WebinarController@listWebinars')->name('webinars.list');
        Route::get('/webinar/create', 'WebinarController@create')->name('webinar.create');
        Route::post('/webinar/store', 'WebinarController@store')->name('webinar.store');
        Route::post('/webinar/delete/{webinar}', 'WebinarController@destroy')->name('webinar.delete');
        Route::get('/webinar/edit/{webinar}', 'WebinarController@show')->name('webinar.update.show');
        Route::post('/webinar/edit/{webinar}', 'WebinarController@update')->name('webinar.update');
        //Resources
        Route::get('/resources', 'ResourceController@listResources')->name('admin.resources');
        Route::get('/resources/create', 'ResourceController@create')->name('admin.resources-create');
        Route::post('/resources/store', 'ResourceController@store')->name('admin.resources-store');
        Route::post('/resources/delete/{resource}', 'ResourceController@destroy')->name('admin.resources-delete');
        Route::get('/resources/edit/{resource}', 'ResourceController@show')->name('admin.resources-show');
        Route::post('/resources/edit/{resource}', 'ResourceController@update')->name('admin.resources-update');
    });
});
