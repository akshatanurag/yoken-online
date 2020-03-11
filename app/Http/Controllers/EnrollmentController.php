<?php

namespace App\Http\Controllers;

use App\Course;
use App\Coupon;
use App\Batch;
use DB;
use App\Mail\Enrollment;
use App\Mail\EnrollmentAdmin;

class EnrollmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['verify']]);
    }
    public function index($id)
    {
        session()->forget('payable_amount');
        session()->forget('institute_coupon');
        session()->forget('yoken_coupon');
        session()->forget('yoken_rebate');
        session()->forget('institute_rebate');
        session()->forget('yoken_applied');
        session()->forget('ins_applied');
        $course = Course::where('id', $id)->where('status', 1)->first();
        if ($course != null) {
            $course->batches = $course->batches->filter(function($batch){
                return strtotime(str_replace('/', '-', $batch->commence_date)) > time();
            });
            if ($course->batches->count() > 0) {
                $fees = $course->fees;
                $discount = $course->discount;
                $baseAmount = $fees - ($fees * $discount) / 100;
                session()->put('courseId', $course->id);
                session()->put('payable_amount', $baseAmount);
                return \View::make('courses.enroll', [
                    'course' => $course,
                ]);
            } else {
                return redirect("/");
            }
        } else {
            return redirect("/");
        }
    }
    public function store()
    {
        //return redirect()->back();
        $this->validate(request(), [
            'captcha' => 'required|captcha',
            'batch' => 'required',
            'paymentOption' => 'required',
            'paymentType' => 'required'
        ]);
        $ot = 0;
        $type = request('paymentType');
        $course = \App\Batch::find(request('batch'))->course;
        if (request('paymentOption') == 'ot') {
            $fees = $course->fees;
            $discount = $course->discount;
            $ot = 1;
            $installmentId = null;
        } else {
            $installmentId = request('paymentOption');
            $installmentAmounts = \App\Installment::find($installmentId)->amounts;
            $fees = explode(";", $installmentAmounts)[0];
            $discount = 0;
        }
        if (session()->has('yoken_coupon')) {
            $yokenCoupon = session()->get('yoken_coupon');
            $yokenRebate = session()->get('yoken_rebate');
        } else {
            $yokenCoupon = null;
            $yokenRebate = 0;
        }
        if (session()->has('institute_coupon')) {
            $instituteCoupon = session()->get('institute_coupon');
            $instituteRebate = session()->get('institute_rebate');
        } else {
            $instituteCoupon = null;
            $instituteRebate = 0;
        }

        $enrollment = \App\Enrollment::create([
            'user_id' => auth()->user()->id,
            'batch_id' => request('batch'),
            'base_fees' => $fees,
            'base_discount' => $discount,
            'one_time' => $ot,
            'type' => $type,
            'installment_id' => $installmentId,
            'yoken_promo_code' => $yokenCoupon,
            'yoken_rebate' => $yokenRebate,
            'institute_promo_code' => $instituteCoupon,
            'institute_rebate' => $instituteRebate,
            'installment_index' => 0
        ]);

        $coupon = Coupon::where('name', request('promo_code'))->first();
        if($coupon != null) {
            $countData = \DB::table('coupon_user')->select('use_count')
                ->where([
                    ['user_id', \App\User::find(auth()->user()->id)->id],
                    ['coupon_id', $coupon->id ],
                    ['course_id', $course->id]])
                ->first();
            if (isset($countData)) {
                \DB::table('coupon_user')->where([
                    ['user_id', \App\User::find(auth()->user()->id)->id],
                    ['coupon_id', $coupon->id ],
                    ['course_id', $course->id]])
                    ->update(['use_count' =>$countData->use_count+1]);
            } else {
                \DB::table('coupon_user')->insert(
                    ['user_id'=>\App\User::find(auth()->user()->id)->id,
                        'coupon_id' => $coupon->id,
                        'course_id' => $course->id,
                        'use_count'=> 1]
                );
            }
        }
        $coupon = Coupon::where('name', request('ins_promo_code'))->first();
        if($coupon != null) {
            $countData = \DB::table('coupon_user')->select('use_count')
                ->where([
                    ['user_id', \App\User::find(auth()->user()->id)->id],
                    ['coupon_id', $coupon->id ],
                    ['course_id', $course->id]])
                ->first();
            if (isset($countData)) {
                \DB::table('coupon_user')->where([
                    ['user_id', \App\User::find(auth()->user()->id)->id],
                    ['coupon_id', $coupon->id ],
                    ['course_id', $course->id]])
                    ->update(['use_count' =>$countData->use_count+1]);
            } else {
                \DB::table('coupon_user')->insert(
                    ['user_id'=>\App\User::find(auth()->user()->id)->id,
                        'coupon_id' => $coupon->id,
                        'course_id' => $course->id,
                        'use_count'=> 1]
                );
            }
        }

        \Mail::to(auth()->user())->send(new Enrollment($enrollment));
        $admins = \App\User::where('role', 0)->get();
        $admins->each(function($user) use ($enrollment) {
            \Mail::to($user)->send(new EnrollmentAdmin($enrollment));
        });

        if ($type == 0) {
            return redirect('/?m=1');
        } else {
            $amount = session()->get('payable_amount');
            try {
                if (\App::environment('local', 'testing')) {
                    $instamojoApi = new \Instamojo\Instamojo(getenv('INSTA_API_KEY'), getenv('INSTA_AUTH_TOKEN'), 'https://test.instamojo.com/api/1.1/');
                } else {
                    $instamojoApi = new \Instamojo\Instamojo(getenv('INSTA_API_KEY'), getenv('INSTA_AUTH_TOKEN'));
                }
                $response = $instamojoApi->paymentRequestCreate([
                    "purpose" => "Yoken Payment",
                    "amount" => $amount,
                    "send_email" => false,
                    "send_sms" => false,
                    "email" => auth()->user()->email,
                    "webhook" => "https://yokenonline.com/api/payment-hook",
                    "redirect_url" => "https://yokenonline.com/"
                ]);
                $response = $instamojoApi-> paymentRequestStatus($response['id']);
                $payment =  new \App\Payment;
                $payment->enrollment_id = $enrollment->id;
                $payment->payment_id = $response['id'];
                $payment->payment_status = "Pending";
                $payment->payment_details = $response;
                $payment->save();
                return redirect($response['longurl']);
            } catch (Exception $e) {
                print('Error: ' . $e->getMessage());
            }
        }
    }
    public function verify()
    {
        $data = $_POST;
        $mac_provided = $data['mac'];
        unset($data['mac']);  // Remove the MAC key from the data.
        $ver = explode('.', phpversion());
        $major = (int) $ver[0];
        $minor = (int) $ver[1];

        if ($major >= 5 and $minor >= 4) {
            ksort($data, SORT_STRING | SORT_FLAG_CASE);
        } else {
            uksort($data, 'strcasecmp');
        }

        $mac_calculated = hash_hmac("sha1", implode("|", $data), getenv('INSTA_API_SALT'));

        if ($mac_provided == $mac_calculated) {
            echo "MAC is fine";
            $payment = \App\Payment::where('payment_id', $data['payment_request_id'])->first();
            $payment->payment_status = $data['status'];
            $payment->payment_details = json_encode($data);
            $payment->save();
            die("Payment Successful");
        } else {
            die("Invalid MAC passed");
        }
    }
}
