<?php

namespace App\Http\Controllers;

use \App\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    protected $coupon;
    protected $course;
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['apply']]);
        $this->middleware('auth:institute', ['except' => ['apply', 'index_admin', 'create_admin', 'store_admin', 'destroy_admin']]);
        $this->middleware('isAdmin', ['only' => ['index_admin', 'create_admin', 'store_admin', 'destroy_admin']]);
        $this->coupon = Coupon::where('name', request('code'))->first();
    }

    public function apply()
    {
        //is it valid?
        $this->course = \App\Course::find(session()->get('courseId'));
        if (!isset($this->coupon) || ! $this->coupon->expire_timestamp>=date('Y-m-d').' 00:00:00') {
            return response()->json([
                'message'=> 'Invalid coupon applied'
            ], 422);
        }

        //Check if coupon has already been applied
        if ((session()->get('yoken_applied') && session()->get('yoken_coupon') == $this->coupon->name) || (session()->get('ins_applied') && session()->get('institute_coupon') == $this->coupon->name)) {
            return response()->json([
                'message'=> 'Coupon already applied.'
            ], 422);
        }

        //is it actually the coupon code requested?
        if (request('couponBy') != 'YOKEN' && request('couponBy') != '') {
            return response()->json([
                'message'=>'Invalid coupon codes'
            ], 422);
        }
        //is it applicable?
        $target_type = $this->coupon->target_type;
        $target_value = $this->coupon->target_value;

        if (!$this->isApplicable($target_type, $target_value)) {
            return response()->json([
                'message'=>'Coupon code is not applicable'
            ], 422);
        }
        //has the user exhausted the coupon?
        $countData = \DB::table('coupon_user')->select('use_count')
            ->where([
                ['user_id', \App\User::find(auth()->user()->id)->id],
                ['coupon_id', $this->coupon->id ],
                ['course_id', $this->course->id]])
            ->first();

        if (isset($countData) && $countData->use_count >= $this->coupon->allowed_per_user) {
            return response()->json([
                'message'=>'You have already used up this coupon'
            ], 422);
        }
        //otherwise

        /*
         *  base price (after base discount)                                    x
         *  institute coupon discount                                           a % on {base price}
         *  yoken coupon discount value                                         ( a / 100 ) * x
         *  yoken coupon discount                                               b % on {base price}
         *  yoken coupon discount value                                         ( b / 100 ) * x
         *  discounted base price                                               x ( 1 - ( a / 100 ) - ( b / 100 ) )
         *  yoken charge                                                        c % on {discounted base price}
         *  yoken price                                                         ( c / 100 ) * ( x ( 1 - ( a / 100 ) - ( b / 100 ) ) )
         *  institute price                                                     x ( 1 - ( a / 100 ) - ( b / 100 ) ) ( 1 - ( c / 100 ) )
         *
         *  Now we can use these formulas to calculate any of the values by using either the base university price or the final price
        */
        $fees = $this->course->fees;
        $discount = $this->course->discount;
        $couponDiscount = $this->coupon->discount_value;
        $yokenCharge= getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
        $baseAmount = $baseAmountPayable = $fees - ($fees * $discount)/100;
        if (request('paymentOption') == 'ot') {
            if ($this->coupon->created_by != 'YOKEN') {
                session()->put('ins_applied', true);
                session()->put('institute_coupon', $this->coupon->name);

                if (session()->has('payable_amount')) {
                    $baseAmountPayable = session()->get('payable_amount');
                }

                //apply institute coupon
                if ($this->coupon->discount_type == 'PER') {
                    session()->put('payable_amount', $baseAmountPayable - (($couponDiscount / 100) * $baseAmount));
                    session()->put('institute_rebate', (($couponDiscount / 100) * $baseAmount));
                    if (session()->get('payable_amount') < 0) {
                        session()->put('payable_amount', 0);
                        session()->put('institute_rebate', $baseAmountPayable);
                    }
                } else {
                    session()->put('payable_amount', $baseAmountPayable - $couponDiscount);
                    session()->put('institute_rebate', $couponDiscount);
                    if (session()->get('payable_amount') < 0) {
                        session()->put('payable_amount', 0);
                        session()->put('institute_rebate', $baseAmountPayable);
                    }
                }
            } else {
                session()->put('yoken_applied', true);
                //if coupon is created by yoken:
                session()->put('yoken_coupon', $this->coupon->name);

                if (session()->has('payable_amount')) {
                    $baseAmountPayable = session()->get('payable_amount');
                }

                //apply yoken coupon
                if ($this->coupon->discount_type == 'PER') {
                    session()->put('payable_amount', $baseAmountPayable - (($couponDiscount / 100) * $baseAmount));
                    session()->put('yoken_rebate', (($couponDiscount / 100) * $baseAmount));
                    if (session()->get('payable_amount') < 0) {
                        session()->put('payable_amount', 0);
                        session()->put('yoken_rebate', $baseAmountPayable);
                    }
                } else {
                    session()->put('payable_amount', $baseAmountPayable - $couponDiscount);
                    session()->put('yoken_rebate', $couponDiscount);
                    if (session()->get('payable_amount') < 0) {
                        session()->put('payable_amount', 0);
                        session()->put('yoken_rebate', $baseAmountPayable);
                    }
                }
            }
        }
        return response()->json([
            'message'=>session()->get('payable_amount')
        ], 200);
    }
    protected function isApplicable($target_type, $target_value)
    {
        switch ($target_type) {
            case 'ALL':
                return true;
                break;
            case 'CTG':
                return (in_array($target_value, $this->course->categories()
                    ->pluck('id')->toArray()));
                break;
            case 'INS':
                return ($target_value == $this->course->institute_id);
                break;
            case 'CRS':
                return ($target_value == $this->course->id);
                break;
            case 'USR':
                return ($target_value == auth()->user()->id);
                break;
            default:
                return false;
        }
    }

    public function index()
    {
        $coupons = Coupon::where('created_by', \Auth::guard('institute')->user()->id)->get();
        return view('coupons.coupons-listing', compact('coupons'));
    }

    public function index_admin()
    {
        $coupons = Coupon::where('created_by', 'YOKEN')->get();
        return view('coupons.coupons-listing-admin', compact('coupons'));
    }

    public function create()
    {
        $courses = \App\Course::where('institute_id', \Auth::guard('institute')->user()->id)->get();
        return view('coupons.create', compact('courses'));
    }

    public function create_admin()
    {
        $institutes = \App\Institute::with('courses')->get();
        $categories = \App\Category::all();
        $users = \App\User::where('role', '<>', 0)->get();
        return view('coupons.create-admin', compact(['institutes', 'categories', 'users']));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
           'name' => 'required',
            'couponType' => 'required',
            'couponValue' => 'required',
            'allowance' => 'required',
            'target' => 'required',
            'expireTimestamp' => 'required',
        ]);

        $coupon = new Coupon();
        $coupon->name = $request->name;
        $coupon->discount_type = $request->couponType;
        $coupon->discount_value = $request->couponValue;
        $coupon->allowed_per_user = $request->allowance;
        $coupon->created_by = \Auth::guard('institute')->user()->id;
        if ($request->target == 'ALL') {
            $coupon->target_type = 'INS';
            $coupon->target_value = \Auth::guard('institute')->user()->id;
        } else {
            $coupon->target_type = 'CRS';
            $coupon->target_value = $request->target;
        }
        $timestamp = date_create_from_format('d-m-y', $request->expireTimestamp);
        $coupon->expire_timestamp = $timestamp->format('Y-m-d H:i:s');

        $coupon->save();

        return redirect(route('coupon.view'));
    }

    public function store_admin(Request $request)
    {
        $this->validate($request, [
           'name' => 'required',
            'couponType' => 'required',
            'couponValue' => 'required',
            'allowance' => 'required',
            'target' => 'required',
            'expireTimestamp' => 'required',
        ]);

        $coupon = new Coupon();
        $coupon->name = $request->name;
        $coupon->discount_type = $request->couponType;
        $coupon->discount_value = $request->couponValue;
        $coupon->allowed_per_user = $request->allowance;
        $coupon->created_by = 'YOKEN';
        $coupon->target_type = $request->target;
        if ($request->target != 'ALL') {
            $coupon->target_value = $request->target_value;
        } else {
            $coupon->target_value = -1;
        }
        $timestamp = date_create_from_format('d-m-y', $request->expireTimestamp);
        $coupon->expire_timestamp = $timestamp->format('Y-m-d H:i:s');

        $coupon->save();

        return redirect(route('coupon.view-admin'));
    }

    public function destroy($coupon)
    {
        \App\Coupon::find($coupon)->where('created_by', \Auth::guard('institute')->user()->id)->delete();
        return redirect(route('coupon.view'));
    }

    public function destroy_admin($coupon)
    {
        \App\Coupon::find($coupon)->where('created_by', 'YOKEN')->delete();
        return redirect(route('coupon.view-admin'));
    }
}
