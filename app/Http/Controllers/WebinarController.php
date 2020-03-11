<?php

namespace App\Http\Controllers;

use App\Webinar;
use DB;
use App\Mail\Registration;
use App\Mail\RegistrationAdmin;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;

class WebinarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except'=> ['index', 'verify']]);
        $this->middleware('isAdmin', ['except'=> ['index', 'host', 'verify', 'register', 'showRegister']]);
    }

    public function index()
    {
        $webinars = new Webinar();
        return view('webinars.index', [
            'webinars' => $webinars->where('starts_at', '>', DB::raw('now()'))->paginate(9)
        ]);
    }

    public function create()
    {
        return view('webinars.create');
    }

    public function listWebinars()
    {
        $webinars = Webinar::all();
        return view('webinars.webinars-listing', compact('webinars'));
    }

    public function showRegister(Webinar $webinar){
        if(strtotime(str_replace('/', '-', $webinar->starts_at)) > time ()) {
            return view('webinars.register', compact('webinar'));
        } else {
            return redirect("/");
        }
    }

    public function register(Webinar $webinar){
        $this->validate(request(), [
            'captcha' => 'required|captcha',
        ]);
        $registration = new \App\WebinarRegistration;
        $registration->user_id = auth()->user()->id;
        $registration->webinar_id = $webinar->id;
        $registration->base_fees = $webinar->fees;
        $registration->base_discount = $webinar->discount;
        $registration->save();

        \Mail::to(auth()->user())->send(new Registration($registration));
        $admins = \App\User::where('role', 0)->get();
        $admins->each(function($user) use ($registration) {
            \Mail::to($user)->send(new RegistrationAdmin($registration));
        });

        $amount = $webinar->fees - ($webinar->fees * ($webinar->discount / 100));
        if ($amount != 0) {
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
                    "webhook" => "https://yokenonline.com/api/webinar-payment-hook",
                    "redirect_url" => "https://yokenonline.com/webinar-thank-you"
                ]);
                $response = $instamojoApi-> paymentRequestStatus($response['id']);
                $payment =  new \App\WebinarPayment;
                $payment->webinar_registration_id = $registration->id;
                $payment->payment_id = $response['id'];
                $payment->payment_status = "Pending";
                $payment->payment_details = $response;
                $payment->save();
                return redirect($response['longurl']);
            } catch (Exception $e) {
                print('Error: ' . $e->getMessage());
            }
        } else {
            return redirect("https://yokenonline.com/webinar-thank-you?m=1");
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
            $payment = \App\WebinarPayment::where('payment_id', $data['payment_request_id'])->first();
            $payment->payment_status = $data['status'];
            $payment->payment_details = json_encode($data);
            $payment->save();
            die("Payment Successful");
        } else {
            die("Invalid MAC passed");
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
            'webinar_pic' => 'required|max:5120',
            'start_date' => 'required',
            //'start_time' => 'required',
            'end_date' => 'required',
            //'end_time' => 'required',
            'fees' => 'required',
            'discount' => 'required',
            'room_url' => 'required',
        ]);
        $webinar = new Webinar();
        $webinar->name = $request->name;
        $webinar->description = $request->description;
        $fileName = md5(uniqid(rand(), true)) . '.'. $request->file('webinar_pic')->getClientOriginalExtension();
        $path = storage_path('app/public/webinars/' . $fileName);
        Image::make($request->file('webinar_pic'))->save($path);
        $webinar->image_url = $path;
        $webinar->starts_at = date('Y-m-d H:i:s', strtotime($request->start_date . ' ' .$request->start_time));
        $webinar->ends_at = date('Y-m-d H:i:s', strtotime($request->end_date . ' ' .$request->end_time));
        $webinar->fees = $request->fees;
        $webinar->discount = $request->discount;
        $webinar->room_url = $request->room_url;
        $webinar->save();
        return redirect(route('webinars.list'));
    }
    public function show(Webinar $webinar)
    {
        return view('webinars.edit', compact('webinar'));
    }
    public function destroy(Webinar $webinar)
    {
        $webinar->delete();
        return redirect(route('webinars.list'));
    }

    public function update(Webinar $webinar, Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
            'start_date' => 'required',
            //'start_time' => 'required',
            'end_date' => 'required',
            //'end_time' => 'required',
            'fees' => 'required',
            'discount' => 'required',
            'room_url' => 'required'
        ]);
        $webinar->name = $request->name;
        $webinar->description = $request->description;
        if ($request->hasFile('webinar_pic')) {
            $fileName = md5(uniqid(rand(), true)) . '.'. $request->file('webinar_pic')->getClientOriginalExtension();
            $path = storage_path('app/public/webinars/' . $fileName);
            Image::make($request->file('webinar_pic'))->save($path);
            \Storage::delete(str_replace(storage_path() . '/app/public', '', $webinar->image_url));
            $webinar->image_url = $path;
        }
        $webinar->starts_at = date('Y-m-d H:i:s', strtotime($request->start_date .' ' .$request->start_time));
        $webinar->ends_at = date('Y-m-d H:i:s', strtotime($request->end_date .' ' .$request->end_time));
        $webinar->fees = $request->fees;
        $webinar->discount = $request->discount;
        $webinar->room_url = $request->room_url;
        $webinar->save();
        return redirect()->back();
    }
    public function edit()
    {
    }

    public function host()
    {
        $webinar = \App\Webinar::whereRaw('starts_at <= now()')->whereRaw('ends_at > now()')->orderByRaw('starts_at')->first();
        if($webinar == null) {
            return view('webinars.webinar-hosting');
        }
        $registration = \App\WebinarRegistration::where("user_id", auth()->user()->id)->where("webinar_id", $webinar->id)->first();
        if($registration == null) {
            return view('webinars.webinar-hosting');
        }
        if ($webinar->fees == 0 || $webinar->discount == 100) {
            return view('webinars.webinar-hosting', compact("webinar"));
        }
        $payment = \App\WebinarPayment::where("webinar_registraton_id", $registration->id)->first();
        if($payment == null || strtolower($payment->payment_status) != strtolower("credit")) {
            return view('webinars.webinar-hosting');
        } else {
            return view('webinars.webinar-hosting', compact("webinar"));
        }
    }
}
