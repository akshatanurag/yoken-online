<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Institute;
use App\Course;
use View;
use Intervention\Image\ImageManagerStatic as Image;
class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('isAdmin');
    }
    public function webinarReport(Request $request)
    {
        $webinars = \App\Webinar::orderBy('starts_at', 'DESC')->get();
        if (!$request->has('webinar') || !$request->has('period')) {
            if ($request->has('webinar') || $request->has('period')) {
                $this->validate($request, [
                    "webinar" => "required",
                    "period" => "required|in:hd,dw,wm,my,yy"
                ]);
            }
            return view('admin.report-webinar', [
                'webinars' => $webinars
            ]);
        } else {
            $this->validate($request, [
                "webinar" => "required",
                "period" => "required|in:hd,dw,wm,my,yy"
            ]);
            $webinar = \App\Webinar::find($request->input('webinar'));
            $registrations_general = [];
            $labels = [];
            $registrations_users_list = [];
            if ($request->input('period') == 'hd') {
                for ($i = 0; $i < 24; $i ++) {
                    $labels[] = '"' . date("Y-m-d H:i:s", strtotime("-" . (23 - $i) . " hour")) . '"' ;
                }
            } elseif ($request->input('period') == 'dw') {
                for ($i = 0; $i < 7; $i ++) {
                    $labels[] = '"' . date('Y-m-d', strtotime("-" . (6 - $i) . " day")) . '"' ;
                }
            } elseif ($request->input('period') == 'wm') {
                $labels[] = '"3 Weeks Ago"' ;
                $labels[] = '"2 Weeks Ago"' ;
                $labels[] = '"1 Week Ago"' ;
                $labels[] = '"This Week"' ;
            } elseif ($request->input('period') == 'my') {
                for ($i = 0; $i < 12; $i ++) {
                    $labels[] = '"' . date('F', strtotime("-" . (11 - $i) . " month")) . '"' ;
                }
            } elseif ($request->input('period') == 'yy') {
                for ($i = 0; $i < 10; $i ++) {
                    $labels[] = '"' . date('Y', strtotime("-" . (9 - $i) . " year")) . '"' ;
                }
            }
            $labels = implode(",", $labels);
            if ($request->input('period') == 'hd') {
                $registrations_final_list = $webinar->registrations()->whereDate('created_at', date('Y-m-d'))->get();
                if ($registrations_final_list->count()) {
                    for ($i = 0; $i < 24; $i ++) {
                        $registrations_list = $webinar->registrations()->whereRaw('HOUR(created_at) = '. date("H", strtotime("-" . (23 - $i) . " hour")))->whereDate('created_at', date('Y-m-d', strtotime("-" . (23 - $i) . " hour")))->get();
                        $money_received = 0;
                        foreach ($registrations_list as $registration) {
                            $fees = $registration->base_fees;
                            $discount = $registration->base_discount;
                            $yokenCharge = getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                            $baseAmount = $fees - ($fees * $discount)/100;
                            $money_received += ((100 * $baseAmount) / (100 + $yokenCharge)) - $registration->institute_rebate;
                        }
                        if (!isset($registrations_general [$i])) {
                            $registrations_general [$i] = $money_received;
                        } else {
                            $registrations_general [$i] += $money_received;
                        }
                    }
                }
            } elseif ($request->input('period') == 'dw') {
                $registrations_final_list = $webinar->registrations()->whereDate('created_at', ">", date('Y-m-d', strtotime("-7 days")))->get();
                if ($registrations_final_list->count()) {
                    for ($i = 0; $i < 7; $i ++) {
                        $registrations_list = $webinar->registrations()->whereDate('created_at', date('Y-m-d', strtotime("-" . (6 - $i) . " day")))->get();
                        $money_received = 0;
                        foreach ($registrations_list as $registration) {
                            $fees = $registration->base_fees;
                            $discount = $registration->base_discount;
                            $yokenCharge = getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                            $baseAmount = $fees - ($fees * $discount)/100;
                            $money_received += ((100 * $baseAmount) / (100 + $yokenCharge)) - $registration->institute_rebate;
                        }
                        if (!isset($registrations_general [$i])) {
                            $registrations_general [$i] = $money_received;
                        } else {
                            $registrations_general [$i] += $money_received;
                        }
                    }
                }
            } elseif ($request->input('period') == 'wm') {
                $registrations_final_list = $webinar->registrations()->whereDate('created_at', ">", date('Y-m-d', strtotime("-4 week")))->get();
                if ($registrations_final_list->count()) {
                    for ($i = 0; $i < 4; $i ++) {
                        $registrations_list = $webinar->registrations()->whereDate('created_at', ">", date('Y-m-d', strtotime("-" . (4 - $i) . " week")))->whereDate('created_at', "<=", date('Y-m-d', strtotime("-" . (3 - $i) . " week")))->get();
                        $money_received = 0;
                        foreach ($registrations_list as $registration) {
                            $fees = $registration->base_fees;
                            $discount = $registration->base_discount;
                            $yokenCharge = getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                            $baseAmount = $fees - ($fees * $discount)/100;
                            $money_received += ((100 * $baseAmount) / (100 + $yokenCharge)) - $registration->institute_rebate;
                        }
                        if (!isset($registrations_general [$i])) {
                            $registrations_general [$i] = $money_received;
                        } else {
                            $registrations_general [$i] += $money_received;
                        }
                    }
                }
            } elseif ($request->input('period') == 'my') {
                $registrations_final_list = $webinar->registrations()->whereDate('created_at', ">", date('Y-m-d', strtotime("-12 month")))->get();
                if ($registrations_final_list->count()) {
                    for ($i = 0; $i < 12; $i ++) {
                        $registrations_list = $webinar->registrations()->whereDate('created_at', ">", date('Y-m-d', strtotime("-" . (12 - $i) . " month")))->whereDate('created_at', "<=", date('Y-m-d', strtotime("-" . (11 - $i) . " month")))->get();
                        $money_received = 0;
                        foreach ($registrations_list as $registration) {
                            $fees = $registration->base_fees;
                            $discount = $registration->base_discount;
                            $yokenCharge = getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                            $baseAmount = $fees - ($fees * $discount)/100;
                            $money_received += ((100 * $baseAmount) / (100 + $yokenCharge)) - $registration->institute_rebate;
                        }
                        if (!isset($registrations_general [$i])) {
                            $registrations_general [$i] = $money_received;
                        } else {
                            $registrations_general [$i] += $money_received;
                        }
                    }
                }
            } else {
                $registrations_final_list = $webinar->registrations()->whereDate('created_at', ">", date('Y-m-d', strtotime("-10 year")))->get();
                if ($registrations_final_list->count()) {
                    for ($i = 0; $i < 10; $i ++) {
                        $registrations_list = $webinar->registrations()->whereDate('created_at', ">", date('Y-m-d', strtotime("-" . (10 - $i) . " year")))->whereDate('created_at', "<=", date('Y-m-d', strtotime("-" . (9 - $i) . " year")))->get();
                        $money_received = 0;
                        foreach ($registrations_list as $registration) {
                            $fees = $registration->base_fees;
                            $discount = $registration->base_discount;
                            $yokenCharge = getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                            $baseAmount = $fees - ($fees * $discount)/100;
                            $money_received += ((100 * $baseAmount) / (100 + $yokenCharge)) - $registration->institute_rebate;
                        }
                        if (!isset($registrations_general [$i])) {
                            $registrations_general [$i] = $money_received;
                        } else {
                            $registrations_general [$i] += $money_received;
                        }
                    }
                }
            }
            return view('admin.report-webinar', [
                'webinars'                      => $webinars,
                'webinar_details'               => $webinar,
                'period'                        => $request->input('period'),
                'labels'                        => $labels,
                'registrations_general'         => $registrations_general,
                'registrations_list'            => $registrations_final_list
            ]);
        }
    }
    public function report(Request $request)
    {
        $institutes = \App\Institute::all();
        if (!$request->has('institute') || !$request->has('period')) {
            if ($request->has('institute') || $request->has('period')) {
                $this->validate($request, [
                    "institute" => "required",
                    "period" => "required|in:hd,dw,wm,my,yy"
                ]);
            }
            return view('admin.report', [
                'institutes' => $institutes
            ]);
        } else {
            $this->validate($request, [
                "institute" => "required",
                "period" => "required|in:hd,dw,wm,my,yy"
            ]);
            $institute = \App\Institute::find($request->input('institute'));
            $courses = \App\Course::where('institute_id', $institute->id)->get();
            $enrollments_course = $enrollments_general = $enrollments_course_general = [];
            $enrollments_general [0] = $enrollments_general [1] = [];
            $labels = $pie_labels = $enrollments_users_list = [];
            if ($request->input('period') == 'hd') {
                for ($i = 0; $i < 24; $i ++) {
                    $labels[] = '"' . date("Y-m-d H:i:s", strtotime("-" . (23 - $i) . " hour")) . '"' ;
                }
            } elseif ($request->input('period') == 'dw') {
                for ($i = 0; $i < 7; $i ++) {
                    $labels[] = '"' . date('Y-m-d', strtotime("-" . (6 - $i) . " day")) . '"' ;
                }
            } elseif ($request->input('period') == 'wm') {
                $labels[] = '"3 Weeks Ago"' ;
                $labels[] = '"2 Weeks Ago"' ;
                $labels[] = '"1 Week Ago"' ;
                $labels[] = '"This Week"' ;
            } elseif ($request->input('period') == 'my') {
                for ($i = 0; $i < 12; $i ++) {
                    $labels[] = '"' . date('F', strtotime("-" . (11 - $i) . " month")) . '"' ;
                }
            } elseif ($request->input('period') == 'yy') {
                for ($i = 0; $i < 10; $i ++) {
                    $labels[] = '"' . date('Y', strtotime("-" . (9 - $i) . " year")) . '"' ;
                }
            }
            $labels = implode(", ", $labels);
            foreach ($courses as $course) {
                $batches = \App\Batch::where('course_id', $course->id)->pluck('id')->toArray();
                if ($request->input('period') == 'hd') {
                    $enrollments_final_list = \App\Enrollment::whereIn('batch_id', $batches)->whereDate('created_at', date('Y-m-d'))->get();
                    if ($enrollments_final_list->count()) {
                        $enrollments_course [$course->id] = [
                            0 => [],
                            1 => []
                        ];
                        for ($i = 0; $i < 24; $i ++) {
                            $enrollments_list = \App\Enrollment::whereIn('batch_id', $batches)->whereRaw('HOUR(created_at) = '. date("H", strtotime("-" . (23 - $i) . " hour")))->whereDate('created_at', date('Y-m-d', strtotime("-" . (23 - $i) . " hour")))->get();
                            $online_money_received = $offline_money_received = 0;
                            foreach ($enrollments_list as $enrollment) {
                                $fees = $enrollment->base_fees;
                                $discount = $enrollment->base_discount;
                                $yokenCharge = getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                                $baseAmount = $fees - ($fees * $discount)/100;
                                if($enrollment->type === 1) {
                                    $online_money_received += ((100 * $baseAmount) / (100 + $yokenCharge)) - $enrollment->institute_rebate;
                                } else {
                                    $offline_money_received += ((100 * $baseAmount) / (100 + $yokenCharge)) - $enrollment->institute_rebate;
                                }
                            }
                            $enrollments_course [$course->id][0][$i] = $offline_money_received;
                            $enrollments_course [$course->id][1][$i] = $online_money_received;
                            if (!isset($enrollments_general [0][$i])) {
                                $enrollments_general [0][$i] = $offline_money_received;
                                $enrollments_general [1][$i] = $online_money_received;
                            } else {
                                $enrollments_general [0][$i] += $offline_money_received;
                                $enrollments_general [1][$i] += $online_money_received;
                            }
                        }
                        $enrollments_users_list [$course->id] = $enrollments_final_list;
                    }
                } elseif ($request->input('period') == 'dw') {
                    $enrollments_final_list = \App\Enrollment::whereIn('batch_id', $batches)->whereDate('created_at', ">", date('Y-m-d', strtotime("-7 days")))->get();
                    if ($enrollments_final_list->count()) {
                        $enrollments_course [$course->id] = [
                            0 => [],
                            1 => []
                        ];
                        for ($i = 0; $i < 7; $i ++) {
                            $enrollments_list = \App\Enrollment::whereIn('batch_id', $batches)->whereDate('created_at', date('Y-m-d', strtotime("-" . (6 - $i) . " day")))->get();
                            $online_money_received = $offline_money_received = 0;
                            foreach ($enrollments_list as $enrollment) {
                                $fees = $enrollment->base_fees;
                                $discount = $enrollment->base_discount;
                                $yokenCharge = getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                                $baseAmount = $fees - ($fees * $discount)/100;
                                if($enrollment->type === 1) {
                                    $online_money_received += ((100 * $baseAmount) / (100 + $yokenCharge)) - $enrollment->institute_rebate;
                                } else {
                                    $offline_money_received += ((100 * $baseAmount) / (100 + $yokenCharge)) - $enrollment->institute_rebate;
                                }
                            }
                            $enrollments_course [$course->id][0][$i] = $offline_money_received;
                            $enrollments_course [$course->id][1][$i] = $online_money_received;
                            if (!isset($enrollments_general [0][$i])) {
                                $enrollments_general [0][$i] = $offline_money_received;
                                $enrollments_general [1][$i] = $online_money_received;
                            } else {
                                $enrollments_general [0][$i] += $offline_money_received;
                                $enrollments_general [1][$i] += $online_money_received;
                            }
                        }
                        $enrollments_users_list [$course->id] = $enrollments_final_list;
                    }
                } elseif ($request->input('period') == 'wm') {
                    $enrollments_final_list = \App\Enrollment::whereIn('batch_id', $batches)->whereDate('created_at', ">", date('Y-m-d', strtotime("-4 week")))->get();
                    if ($enrollments_final_list->count()) {
                        $enrollments_course [$course->id] = [
                            0 => [],
                            1 => []
                        ];
                        for ($i = 0; $i < 4; $i ++) {
                            $enrollments_list = \App\Enrollment::whereIn('batch_id', $batches)->whereDate('created_at', ">", date('Y-m-d', strtotime("-" . (4 - $i) . " week")))->whereDate('created_at', "<=", date('Y-m-d', strtotime("-" . (3 - $i) . " week")))->get();
                            $online_money_received = $offline_money_received = 0;
                            foreach ($enrollments_list as $enrollment) {
                                $fees = $enrollment->base_fees;
                                $discount = $enrollment->base_discount;
                                $yokenCharge = getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                                $baseAmount = $fees - ($fees * $discount)/100;
                                if($enrollment->type === 1) {
                                    $online_money_received += ((100 * $baseAmount) / (100 + $yokenCharge)) - $enrollment->institute_rebate;
                                } else {
                                    $offline_money_received += ((100 * $baseAmount) / (100 + $yokenCharge)) - $enrollment->institute_rebate;
                                }
                            }
                            $enrollments_course [$course->id][0][$i] = $offline_money_received;
                            $enrollments_course [$course->id][1][$i] = $online_money_received;
                            if (!isset($enrollments_general [0][$i])) {
                                $enrollments_general [0][$i] = $offline_money_received;
                                $enrollments_general [1][$i] = $online_money_received;
                            } else {
                                $enrollments_general [0][$i] += $offline_money_received;
                                $enrollments_general [1][$i] += $online_money_received;
                            }
                        }
                        $enrollments_users_list [$course->id] = $enrollments_final_list;
                    }
                } elseif ($request->input('period') == 'my') {
                    $enrollments_final_list = \App\Enrollment::whereIn('batch_id', $batches)->whereDate('created_at', ">", date('Y-m-d', strtotime("-12 month")))->get();
                    if ($enrollments_final_list->count()) {
                        $enrollments_course [$course->id] = [
                            0 => [],
                            1 => []
                        ];
                        for ($i = 0; $i < 12; $i ++) {
                            $enrollments_list = \App\Enrollment::whereIn('batch_id', $batches)->whereDate('created_at', ">", date('Y-m-d', strtotime("-" . (12 - $i) . " month")))->whereDate('created_at', "<=", date('Y-m-d', strtotime("-" . (11 - $i) . " month")))->get();
                            $online_money_received = $offline_money_received = 0;
                            foreach ($enrollments_list as $enrollment) {
                                $fees = $enrollment->base_fees;
                                $discount = $enrollment->base_discount;
                                $yokenCharge = getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                                $baseAmount = $fees - ($fees * $discount)/100;
                                if($enrollment->type === 1) {
                                    $online_money_received += ((100 * $baseAmount) / (100 + $yokenCharge)) - $enrollment->institute_rebate;
                                } else {
                                    $offline_money_received += ((100 * $baseAmount) / (100 + $yokenCharge)) - $enrollment->institute_rebate;
                                }
                            }
                            $enrollments_course [$course->id][0][$i] = $offline_money_received;
                            $enrollments_course [$course->id][1][$i] = $online_money_received;
                            if (!isset($enrollments_general [0][$i])) {
                                $enrollments_general [0][$i] = $offline_money_received;
                                $enrollments_general [1][$i] = $online_money_received;
                            } else {
                                $enrollments_general [0][$i] += $offline_money_received;
                                $enrollments_general [1][$i] += $online_money_received;
                            }
                        }
                        $enrollments_users_list [$course->id] = $enrollments_final_list;
                    }
                } else {
                    $enrollments_final_list = \App\Enrollment::whereIn('batch_id', $batches)->whereDate('created_at', ">", date('Y-m-d', strtotime("-10 year")))->get();
                    if ($enrollments_final_list->count()) {
                        $enrollments_course [$course->id] = [
                            0 => [],
                            1 => []
                        ];
                        for ($i = 0; $i < 10; $i ++) {
                            $enrollments_list = \App\Enrollment::whereIn('batch_id', $batches)->whereDate('created_at', ">", date('Y-m-d', strtotime("-" . (10 - $i) . " year")))->whereDate('created_at', "<=", date('Y-m-d', strtotime("-" . (9 - $i) . " year")))->get();
                            $online_money_received = $offline_money_received = 0;
                            foreach ($enrollments_list as $enrollment) {
                                $fees = $enrollment->base_fees;
                                $discount = $enrollment->base_discount;
                                $yokenCharge = getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                                $baseAmount = $fees - ($fees * $discount)/100;
                                if($enrollment->type === 1) {
                                    $online_money_received += ((100 * $baseAmount) / (100 + $yokenCharge)) - $enrollment->institute_rebate;
                                } else {
                                    $offline_money_received += ((100 * $baseAmount) / (100 + $yokenCharge)) - $enrollment->institute_rebate;
                                }
                            }
                            $enrollments_course [$course->id][0][$i] = $offline_money_received;
                            $enrollments_course [$course->id][1][$i] = $online_money_received;
                            if (!isset($enrollments_general [0][$i])) {
                                $enrollments_general [0][$i] = $offline_money_received;
                                $enrollments_general [1][$i] = $online_money_received;
                            } else {
                                $enrollments_general [0][$i] += $offline_money_received;
                                $enrollments_general [1][$i] += $online_money_received;
                            }
                        }
                        $enrollments_users_list [$course->id] = $enrollments_final_list;
                    }
                }
                if (isset($enrollments_course [$course->id])) {
                    $enrollments_course_general [$course->id] = 0;
                    foreach($enrollments_course [$course->id] as $value) {
                        $enrollments_course_general [$course->id] = array_sum($enrollments_course [$course->id][0]) + array_sum($enrollments_course [$course->id][1]);
                    }
                }
                $pie_labels [] = '"' . $course->name . '"' ;
            }
            $pie_labels = implode(", ", $pie_labels);
            return view('admin.report', [
                'institutes'                    => $institutes,
                'institute_details'             => $institute,
                'period'                        => $request->input('period'),
                'courses'                       => $courses,
                'enrollments_course'            => $enrollments_course,
                'labels'                        => $labels,
                'pie_labels'                    => $pie_labels,
                'enrollments_general'           => $enrollments_general,
                'enrollments_course_general'    => $enrollments_course_general,
                'enrollments_users_list'        => $enrollments_users_list
            ]);
        }
    }
    public function download(Request $request)
    {
        $excel_file = new \PHPExcel();
        $excel_file->getProperties()->setTitle("Document");
        $excel_file->removeSheetByIndex(0);
        $institute = \App\Institute::find($request->input('institute'));
        $courses = \App\Course::where('institute_id', $institute->id)->get();
        $sheet_count = 0;
        foreach ($courses as $course) {
            $batches = \App\Batch::where('course_id', $course->id)->get();
            $excel_file->createSheet($sheet_count);
            $excel_file->setActiveSheetIndex($sheet_count);
            $excel_file->getActiveSheet()->setTitle("Course --- " . substr(preg_replace('/[^a-z0-9]+/', '-', strtolower($course->name)), 0, 19));
            $sheet_count++;
            $count = -1;
            foreach ($batches as $batch) {
                $count += 2;
                $excel_file->getActiveSheet()->setCellValue(('A'.$count), "Batch : " . str_replace("/", "-", $batch->commence_date));
                $count ++;
                $excel_file->getActiveSheet()->setCellValue(('A'.$count), "Time");
                $excel_file->getActiveSheet()->setCellValue(('B'.$count), "User ID");
                $excel_file->getActiveSheet()->setCellValue(('C'.$count), "User Name");
                $excel_file->getActiveSheet()->setCellValue(('D'.$count), "User Email");
                $excel_file->getActiveSheet()->setCellValue(('E'.$count), "User Phone");
                $excel_file->getActiveSheet()->setCellValue(('F'.$count), "Course ID");
                $excel_file->getActiveSheet()->setCellValue(('G'.$count), "Course Name");
                $excel_file->getActiveSheet()->setCellValue(('H'.$count), "Base Price (Discounted)");
                $excel_file->getActiveSheet()->setCellValue(('I'.$count), "Institute Promo");
                $excel_file->getActiveSheet()->setCellValue(('J'.$count), "Yoken Promo");
                $excel_file->getActiveSheet()->setCellValue(('K'.$count), "Yoken Fees");
                $excel_file->getActiveSheet()->setCellValue(('L'.$count), "Fees Received");
                $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Payment Type");
                $excel_file->getActiveSheet()->setCellValue(('N'.$count), "Payment Mode");
                $excel_file->getActiveSheet()->setCellValue(('O'.$count), "Payment Status");
                if ($request->input('period') == 'hd') {
                    $enrollment_final_list = \App\Enrollment::where('batch_id', $batch->id)->whereDate('created_at', date('Y-m-d'))->get();
                    foreach ($enrollment_final_list as $enrollment) {
                        $count++;
                        $fees = $enrollment->base_fees;
                        $discount = $enrollment->base_discount;
                        $yoken_charge= getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                        $base_amount = $fees - (($fees * $discount) / 100);
                        $yoken_price = ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ($yoken_charge / 100);
                        $money_received = ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ((100 - $yoken_charge) / 100);
                        $user = \App\User::where('id', $enrollment->user_id)->first();
                        $excel_file->getActiveSheet()->setCellValue(('A'.$count), $enrollment->created_at);
                        $excel_file->getActiveSheet()->setCellValue(('B'.$count), $user->id);
                        $excel_file->getActiveSheet()->setCellValue(('C'.$count), $user->name);
                        $excel_file->getActiveSheet()->setCellValue(('D'.$count), $user->email);
                        $excel_file->getActiveSheet()->setCellValue(('E'.$count), $user->phone);
                        $excel_file->getActiveSheet()->setCellValue(('F'.$count), $course->id);
                        $excel_file->getActiveSheet()->setCellValue(('G'.$count), $course->name);
                        $excel_file->getActiveSheet()->setCellValue(('H'.$count), $base_amount);
                        $excel_file->getActiveSheet()->setCellValue(('I'.$count), $enrollment->institute_promo_code);
                        $excel_file->getActiveSheet()->setCellValue(('J'.$count), $enrollment->yoken_promo_code);
                        $excel_file->getActiveSheet()->setCellValue(('K'.$count), ($yoken_price - $enrollment->yoken_rebate));
                        $excel_file->getActiveSheet()->setCellValue(('L'.$count), $money_received);
                        $amount = $enrollment->base_fees - ($enrollment->base_fees * ($enrollment->base_discount / 100)) - $enrollment->yoken_rebate - $enrollment->institute_rebate;
                        if ($amount <= 0) {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Free");
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "---");
                        } elseif ($enrollment->type == 1 && !is_null($enrollment->payment)) {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Online");
                            if(strtolower($enrollment->payment->payment_status) == "pending") {
                                $excel_file->getActiveSheet()->setCellValue(('O'.$count), $enrollment->payment->payment_status . "\r\nURL: " . $enrollment->payment->payment_details["longurl"]);
                            } else {
                                $excel_file->getActiveSheet()->setCellValue(('O'.$count), $enrollment->payment->payment_status);
                            }
                        } elseif ($enrollment->type == 1) {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Online");
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "Errored");
                        } elseif ($enrollment->type == 1) {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Online");
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "Errored");
                        } elseif ($enrollment->type == 0) {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Offline");
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "---");
                        } else {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Unknown");
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "---");
                        }
                        if ($enrollment->one_time == 1) {
                            $excel_file->getActiveSheet()->setCellValue(('N'.$count), "One Time");
                        } else {
                            $excel_file->getActiveSheet()->setCellValue(('N'.$count), "Installments (ID : " . $enrollment->installment_id . ")");
                        }
                    }
                } elseif ($request->input('period') == 'dw') {
                    $enrollment_final_list = \App\Enrollment::where('batch_id', $batch->id)->whereDate('created_at', date('Y-m-d'))->get();
                    foreach ($enrollment_final_list as $enrollment) {
                        $count++;
                        $fees = $enrollment->base_fees;
                        $discount = $enrollment->base_discount;
                        $yoken_charge= getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                        $base_amount = $fees - (($fees * $discount) / 100);
                        $yoken_price = ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ($yoken_charge / 100);
                        $money_received = ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ((100 - $yoken_charge) / 100);
                        $user = \App\User::where('id', $enrollment->user_id)->first();
                        $excel_file->getActiveSheet()->setCellValue(('A'.$count), $enrollment->created_at);
                        $excel_file->getActiveSheet()->setCellValue(('B'.$count), $user->id);
                        $excel_file->getActiveSheet()->setCellValue(('C'.$count), $user->name);
                        $excel_file->getActiveSheet()->setCellValue(('D'.$count), $user->email);
                        $excel_file->getActiveSheet()->setCellValue(('E'.$count), $user->phone);
                        $excel_file->getActiveSheet()->setCellValue(('F'.$count), $course->id);
                        $excel_file->getActiveSheet()->setCellValue(('G'.$count), $course->name);
                        $excel_file->getActiveSheet()->setCellValue(('H'.$count), $base_amount);
                        $excel_file->getActiveSheet()->setCellValue(('I'.$count), $enrollment->institute_promo_code);
                        $excel_file->getActiveSheet()->setCellValue(('J'.$count), $enrollment->yoken_promo_code);
                        $excel_file->getActiveSheet()->setCellValue(('K'.$count), ($yoken_price - $enrollment->yoken_rebate));
                        $excel_file->getActiveSheet()->setCellValue(('L'.$count), $money_received);
                        $amount = $enrollment->base_fees - ($enrollment->base_fees * ($enrollment->base_discount / 100)) - $enrollment->yoken_rebate - $enrollment->institute_rebate;
                        if ($amount <= 0) {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Free");
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "---");
                        } elseif ($enrollment->type == 1 && !is_null($enrollment->payment)) {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Online");
                            if(strtolower($enrollment->payment->payment_status) == "pending") {
                                $excel_file->getActiveSheet()->setCellValue(('O'.$count), $enrollment->payment->payment_status . "\r\nURL: " . $enrollment->payment->payment_details["longurl"]);
                            } else {
                                $excel_file->getActiveSheet()->setCellValue(('O'.$count), $enrollment->payment->payment_status);
                            }
                        } elseif ($enrollment->type == 1) {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Online");
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "Errored");
                        } elseif ($enrollment->type == 0) {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Offline");
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "---");
                        } else {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Unknown");
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "---");
                        }
                        if ($enrollment->one_time == 1) {
                            $excel_file->getActiveSheet()->setCellValue(('N'.$count), "One Time");
                        } else {
                            $excel_file->getActiveSheet()->setCellValue(('N'.$count), "Installments (ID : " . $enrollment->installment_id . ")");
                        }
                    }
                } elseif ($request->input('period') == 'wm') {
                    $enrollment_final_list = \App\Enrollment::where('batch_id', $batch->id)->whereDate('created_at', ">", date('Y-m-d', strtotime("-4 week")))->get();
                    foreach ($enrollment_final_list as $enrollment) {
                        $count++;
                        $fees = $enrollment->base_fees;
                        $discount = $enrollment->base_discount;
                        $yoken_charge= getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                        $base_amount = $fees - (($fees * $discount) / 100);
                        $yoken_price = ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ($yoken_charge / 100);
                        $money_received = ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ((100 - $yoken_charge) / 100);
                        $user = \App\User::where('id', $enrollment->user_id)->first();
                        $excel_file->getActiveSheet()->setCellValue(('A'.$count), $enrollment->created_at);
                        $excel_file->getActiveSheet()->setCellValue(('B'.$count), $user->id);
                        $excel_file->getActiveSheet()->setCellValue(('C'.$count), $user->name);
                        $excel_file->getActiveSheet()->setCellValue(('D'.$count), $user->email);
                        $excel_file->getActiveSheet()->setCellValue(('E'.$count), $user->phone);
                        $excel_file->getActiveSheet()->setCellValue(('F'.$count), $course->id);
                        $excel_file->getActiveSheet()->setCellValue(('G'.$count), $course->name);
                        $excel_file->getActiveSheet()->setCellValue(('H'.$count), $base_amount);
                        $excel_file->getActiveSheet()->setCellValue(('I'.$count), $enrollment->institute_promo_code);
                        $excel_file->getActiveSheet()->setCellValue(('J'.$count), $enrollment->yoken_promo_code);
                        $excel_file->getActiveSheet()->setCellValue(('K'.$count), ($yoken_price - $enrollment->yoken_rebate));
                        $excel_file->getActiveSheet()->setCellValue(('L'.$count), $money_received);
                        $amount = $enrollment->base_fees - ($enrollment->base_fees * ($enrollment->base_discount / 100)) - $enrollment->yoken_rebate - $enrollment->institute_rebate;
                        if ($amount <= 0) {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Free");
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "---");
                        } elseif ($enrollment->type == 1 && !is_null($enrollment->payment)) {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Online");
                            if(strtolower($enrollment->payment->payment_status) == "pending") {
                                $excel_file->getActiveSheet()->setCellValue(('O'.$count), $enrollment->payment->payment_status . "\r\nURL: " . $enrollment->payment->payment_details["longurl"]);
                            } else {
                                $excel_file->getActiveSheet()->setCellValue(('O'.$count), $enrollment->payment->payment_status);
                            }
                        } elseif ($enrollment->type == 1) {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Online");
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "Errored");
                        } elseif ($enrollment->type == 0) {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Offline");
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "---");
                        } else {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Unknown");
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "---");
                        }
                        if ($enrollment->one_time == 1) {
                            $excel_file->getActiveSheet()->setCellValue(('N'.$count), "One Time");
                        } else {
                            $excel_file->getActiveSheet()->setCellValue(('N'.$count), "Installments (ID : " . $enrollment->installment_id . ")");
                        }
                    }
                } elseif ($request->input('period') == 'my') {
                    $enrollment_final_list = \App\Enrollment::where('batch_id', $batch->id)->whereDate('created_at', ">", date('Y-m-d', strtotime("-12 month")))->get();
                    foreach ($enrollment_final_list as $enrollment) {
                        $count++;
                        $fees = $enrollment->base_fees;
                        $discount = $enrollment->base_discount;
                        $yoken_charge= getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                        $base_amount = $fees - (($fees * $discount) / 100);
                        $yoken_price = ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ($yoken_charge / 100);
                        $money_received = ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ((100 - $yoken_charge) / 100);
                        $user = \App\User::where('id', $enrollment->user_id)->first();
                        $excel_file->getActiveSheet()->setCellValue(('A'.$count), $enrollment->created_at);
                        $excel_file->getActiveSheet()->setCellValue(('B'.$count), $user->id);
                        $excel_file->getActiveSheet()->setCellValue(('C'.$count), $user->name);
                        $excel_file->getActiveSheet()->setCellValue(('D'.$count), $user->email);
                        $excel_file->getActiveSheet()->setCellValue(('E'.$count), $user->phone);
                        $excel_file->getActiveSheet()->setCellValue(('F'.$count), $course->id);
                        $excel_file->getActiveSheet()->setCellValue(('G'.$count), $course->name);
                        $excel_file->getActiveSheet()->setCellValue(('H'.$count), $base_amount);
                        $excel_file->getActiveSheet()->setCellValue(('I'.$count), $enrollment->institute_promo_code);
                        $excel_file->getActiveSheet()->setCellValue(('J'.$count), $enrollment->yoken_promo_code);
                        $excel_file->getActiveSheet()->setCellValue(('K'.$count), ($yoken_price - $enrollment->yoken_rebate));
                        $excel_file->getActiveSheet()->setCellValue(('L'.$count), $money_received);
                        $amount = $enrollment->base_fees - ($enrollment->base_fees * ($enrollment->base_discount / 100)) - $enrollment->yoken_rebate - $enrollment->institute_rebate;
                        if ($amount <= 0) {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Free");
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "---");
                        } elseif ($enrollment->type == 1 && !is_null($enrollment->payment)) {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Online");
                            if(strtolower($enrollment->payment->payment_status) == "pending") {
                                $excel_file->getActiveSheet()->setCellValue(('O'.$count), $enrollment->payment->payment_status . "\r\nURL: " . $enrollment->payment->payment_details["longurl"]);
                            } else {
                                $excel_file->getActiveSheet()->setCellValue(('O'.$count), $enrollment->payment->payment_status);
                            }
                        } elseif ($enrollment->type == 1) {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Online");
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "Errored");
                        } elseif ($enrollment->type == 0) {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Offline");
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "---");
                        } else {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Unknown");
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "---");
                        }
                        if ($enrollment->one_time == 1) {
                            $excel_file->getActiveSheet()->setCellValue(('N'.$count), "One Time");
                        } else {
                            $excel_file->getActiveSheet()->setCellValue(('N'.$count), "Installments (ID : " . $enrollment->installment_id . ")");
                        }
                    }
                } else {
                    $enrollment_final_list = \App\Enrollment::where('batch_id', $batch->id)->whereDate('created_at', ">", date('Y-m-d', strtotime("-10 year")))->get();
                    foreach ($enrollment_final_list as $enrollment) {
                        $count++;
                        $fees = $enrollment->base_fees;
                        $discount = $enrollment->base_discount;
                        $yoken_charge= getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                        $base_amount = $fees - (($fees * $discount) / 100);
                        $yoken_price = ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ($yoken_charge / 100);
                        $money_received = ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ((100 - $yoken_charge) / 100);
                        $user = \App\User::where('id', $enrollment->user_id)->first();
                        $excel_file->getActiveSheet()->setCellValue(('A'.$count), $enrollment->created_at);
                        $excel_file->getActiveSheet()->setCellValue(('B'.$count), $user->id);
                        $excel_file->getActiveSheet()->setCellValue(('C'.$count), $user->name);
                        $excel_file->getActiveSheet()->setCellValue(('D'.$count), $user->email);
                        $excel_file->getActiveSheet()->setCellValue(('E'.$count), $user->phone);
                        $excel_file->getActiveSheet()->setCellValue(('F'.$count), $course->id);
                        $excel_file->getActiveSheet()->setCellValue(('G'.$count), $course->name);
                        $excel_file->getActiveSheet()->setCellValue(('H'.$count), $base_amount);
                        $excel_file->getActiveSheet()->setCellValue(('I'.$count), $enrollment->institute_promo_code);
                        $excel_file->getActiveSheet()->setCellValue(('J'.$count), $enrollment->yoken_promo_code);
                        $excel_file->getActiveSheet()->setCellValue(('K'.$count), ($yoken_price - $enrollment->yoken_rebate));
                        $excel_file->getActiveSheet()->setCellValue(('L'.$count), $money_received);
                        $amount = $enrollment->base_fees - ($enrollment->base_fees * ($enrollment->base_discount / 100)) - $enrollment->yoken_rebate - $enrollment->institute_rebate;
                        if ($amount <= 0) {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Free");
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "---");
                        } elseif ($enrollment->type == 1 && !is_null($enrollment->payment)) {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Online");
                            if(strtolower($enrollment->payment->payment_status) == "pending") {
                                $excel_file->getActiveSheet()->setCellValue(('O'.$count), $enrollment->payment->payment_status . "\r\nURL: " . $enrollment->payment->payment_details["longurl"]);
                            } else {
                                $excel_file->getActiveSheet()->setCellValue(('O'.$count), $enrollment->payment->payment_status);
                            }
                        } elseif ($enrollment->type == 1) {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Online");
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "Errored");
                        } elseif ($enrollment->type == 0) {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Offline");
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "---");
                        } else {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Unknown");
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "---");
                        }
                        if ($enrollment->one_time == 1) {
                            $excel_file->getActiveSheet()->setCellValue(('N'.$count), "One Time");
                        } else {
                            $excel_file->getActiveSheet()->setCellValue(('N'.$count), "Installments (ID : " . $enrollment->installment_id . ")");
                        }
                    }
                }
            }
        }
        //$excel_file->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . preg_replace('/[^a-z0-9]+/', '-', strtolower($institute->name)) . ' Enrollments.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $excel_writer = \PHPExcel_IOFactory::createWriter($excel_file, 'Excel2007');
        $excel_writer->save('php://output');
    }
    public function downloadAll()
    {
        $excel_file = new \PHPExcel();
        $excel_file->getProperties()->setTitle("Document");
        $excel_file->removeSheetByIndex(0);
        $institutes_list = \App\Institute::all();
        foreach ($institutes_list as $institute) {
            $courses = \App\Course::where('institute_id', $institute->id)->get();
            $sheet_count = 0;
            $excel_file->createSheet($sheet_count);
            $excel_file->setActiveSheetIndex($sheet_count);
            $excel_file->getActiveSheet()->setTitle("Institute --- " . substr(preg_replace('/[^a-z0-9]+/', '-', strtolower($institute->name)), 0, 16));
            $sheet_count++;
            $count = -1;
            foreach ($courses as $course) {
                $batches = \App\Batch::where('course_id', $course->id)->get();
                $count += 2;
                $excel_file->getActiveSheet()->setCellValue(('A'.$count), "Course : " . str_replace("/", "-", $course->name));
                $count ++;
                $excel_file->getActiveSheet()->setCellValue(('A'.$count), "Time");
                $excel_file->getActiveSheet()->setCellValue(('B'.$count), "User ID");
                $excel_file->getActiveSheet()->setCellValue(('C'.$count), "User Name");
                $excel_file->getActiveSheet()->setCellValue(('D'.$count), "User Email");
                $excel_file->getActiveSheet()->setCellValue(('E'.$count), "User Phone");
                $excel_file->getActiveSheet()->setCellValue(('F'.$count), "Course ID");
                $excel_file->getActiveSheet()->setCellValue(('G'.$count), "Course Name");
                $excel_file->getActiveSheet()->setCellValue(('H'.$count), "Batch Date");
                $excel_file->getActiveSheet()->setCellValue(('I'.$count), "Base Price (Discounted)");
                $excel_file->getActiveSheet()->setCellValue(('J'.$count), "Institute Promo");
                $excel_file->getActiveSheet()->setCellValue(('K'.$count), "Yoken Promo");
                $excel_file->getActiveSheet()->setCellValue(('L'.$count), "Yoken Fees");
                $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Fees Received");
                $excel_file->getActiveSheet()->setCellValue(('N'.$count), "Payment Type");
                $excel_file->getActiveSheet()->setCellValue(('O'.$count), "Payment Mode");
                $excel_file->getActiveSheet()->setCellValue(('P'.$count), "Payment Status");
                foreach ($batches as $batch) {
                    $enrollment_final_list = \App\Enrollment::where('batch_id', $batch->id)->get();
                    foreach ($enrollment_final_list as $enrollment) {
                        $count++;
                        $fees = $enrollment->base_fees;
                        $discount = $enrollment->base_discount;
                        $yoken_charge= getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                        $base_amount = $fees - (($fees * $discount) / 100);
                        $yoken_price = ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ($yoken_charge / 100);
                        $money_received = ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ((100 - $yoken_charge) / 100);
                        $user = \App\User::where('id', $enrollment->user_id)->first();
                        $excel_file->getActiveSheet()->setCellValue(('A'.$count), $enrollment->created_at);
                        $excel_file->getActiveSheet()->setCellValue(('B'.$count), $user->id);
                        $excel_file->getActiveSheet()->setCellValue(('C'.$count), $user->name);
                        $excel_file->getActiveSheet()->setCellValue(('D'.$count), $user->email);
                        $excel_file->getActiveSheet()->setCellValue(('E'.$count), $user->phone);
                        $excel_file->getActiveSheet()->setCellValue(('F'.$count), $course->id);
                        $excel_file->getActiveSheet()->setCellValue(('G'.$count), $course->name);
                        $excel_file->getActiveSheet()->setCellValue(('H'.$count), $batch->commence_date);
                        $excel_file->getActiveSheet()->setCellValue(('I'.$count), $base_amount);
                        $excel_file->getActiveSheet()->setCellValue(('J'.$count), $enrollment->institute_promo_code);
                        $excel_file->getActiveSheet()->setCellValue(('K'.$count), $enrollment->yoken_promo_code);
                        $excel_file->getActiveSheet()->setCellValue(('L'.$count), ($yoken_price - $enrollment->yoken_rebate));
                        $excel_file->getActiveSheet()->setCellValue(('M'.$count), $money_received);
                        $amount = $enrollment->base_fees - ($enrollment->base_fees * ($enrollment->base_discount / 100)) - $enrollment->yoken_rebate - $enrollment->institute_rebate;
                        if ($amount <= 0) {
                            $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Free");
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "---");
                        } elseif ($enrollment->type == 1 && !is_null($enrollment->payment)) {
                            $excel_file->getActiveSheet()->setCellValue(('N'.$count), "Online");
                            if(strtolower($enrollment->payment->payment_status) == "pending") {
                                $excel_file->getActiveSheet()->setCellValue(('P'.$count), $enrollment->payment->payment_status . "\r\nURL: " . $enrollment->payment->payment_details["longurl"]);
                            } else {
                                $excel_file->getActiveSheet()->setCellValue(('P'.$count), $enrollment->payment->payment_status);
                            }
                        } elseif ($enrollment->type == 1) {
                            $excel_file->getActiveSheet()->setCellValue(('N'.$count), "Online");
                            $excel_file->getActiveSheet()->setCellValue(('P'.$count), "Errored");
                        } elseif ($enrollment->type == 0) {
                            $excel_file->getActiveSheet()->setCellValue(('N'.$count), "Offline");
                            $excel_file->getActiveSheet()->setCellValue(('P'.$count), "---");
                        } else {
                            $excel_file->getActiveSheet()->setCellValue(('N'.$count), "Unknown");
                            $excel_file->getActiveSheet()->setCellValue(('P'.$count), "---");
                        }
                        if ($enrollment->one_time == 1) {
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "One Time");
                        } else {
                            $excel_file->getActiveSheet()->setCellValue(('O'.$count), "Installments (ID : " . $enrollment->installment_id . ")");
                        }
                    }
                }
            }
        }
        //$excel_file->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Institutes Enrollments.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $excel_writer = \PHPExcel_IOFactory::createWriter($excel_file, 'Excel2007');
        $excel_writer->save('php://output');
    }
    public function webinarDownload(Request $request)
    {
        $excel_file = new \PHPExcel();
        $excel_file->getProperties()->setTitle("Document");
        $webinar = \App\Webinar::find($request->input('webinar'));
        $excel_file->getActiveSheet()->setTitle("Webinar --- " . substr(preg_replace('/[^a-z0-9]+/', '-', $webinar->name), 0, 18));
        $excel_file->getActiveSheet()->setCellValue('A1', "Time");
        $excel_file->getActiveSheet()->setCellValue('B1', "User ID");
        $excel_file->getActiveSheet()->setCellValue('C1', "User Name");
        $excel_file->getActiveSheet()->setCellValue('D1', "User Email");
        $excel_file->getActiveSheet()->setCellValue('E1', "User Phone");
        $excel_file->getActiveSheet()->setCellValue('F1', "Base Price (Discounted)");
        $excel_file->getActiveSheet()->setCellValue('G1', "Yoken Fees");
        $excel_file->getActiveSheet()->setCellValue('H1', "Fees Received");
        $excel_file->getActiveSheet()->setCellValue('I1', "Payment Status");
        if ($request->input('period') == 'hd') {
            $registrations_final_list = $webinar->registrations()->whereDate('created_at', date('Y-m-d'))->get();
            $count = 1;
            foreach ($registrations_final_list as $registration) {
                $count++;
                $fees = $registration->base_fees;
                $discount = $registration->base_discount;
                $yoken_charge= getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                $base_amount = $fees - (($fees * $discount) / 100);
                $yoken_price = $base_amount * ($yoken_charge / 100);
                $money_received = $base_amount * ((100 - $yoken_charge) / 100);
                $user = \App\User::where('id', $registration->user_id)->first();
                $excel_file->getActiveSheet()->setCellValue(('A'.$count), $registration->created_at);
                $excel_file->getActiveSheet()->setCellValue(('B'.$count), $user->id);
                $excel_file->getActiveSheet()->setCellValue(('C'.$count), $user->name);
                $excel_file->getActiveSheet()->setCellValue(('D'.$count), $user->email);
                $excel_file->getActiveSheet()->setCellValue(('E'.$count), $user->phone);
                $excel_file->getActiveSheet()->setCellValue(('F'.$count), $base_amount);
                $excel_file->getActiveSheet()->setCellValue(('G'.$count), $yoken_price);
                $excel_file->getActiveSheet()->setCellValue(('H'.$count), $money_received);
                $amount = $registration->base_fees - ($registration->base_fees * ($registration->base_discount / 100));
                if ($amount <= 0) {
                    $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Free");
                } elseif (!is_null($registration->payment)) {
                    if(strtolower($registration->payment->payment_status) == "pending") {
                        $excel_file->getActiveSheet()->setCellValue(('I'.$count), $registration->payment->payment_status . "\r\nURL: " . $registration->payment->payment_details["longurl"]);
                    } else {
                        $excel_file->getActiveSheet()->setCellValue(('I'.$count), $registration->payment->payment_status);
                    }
                } else {
                    $excel_file->getActiveSheet()->setCellValue(('I'.$count), "Errored");
                }
            }
        } elseif ($request->input('period') == 'dw') {
            $registrations_final_list = $webinar->registrations()->whereDate('created_at', date('Y-m-d'))->get();
            $count = 1;
            foreach ($registrations_final_list as $registration) {
                $count++;
                $fees = $registration->base_fees;
                $discount = $registration->base_discount;
                $yoken_charge= getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                $base_amount = $fees - (($fees * $discount) / 100);
                $yoken_price = $base_amount * ($yoken_charge / 100);
                $money_received = $base_amount * ((100 - $yoken_charge) / 100);
                $user = \App\User::where('id', $registration->user_id)->first();
                $excel_file->getActiveSheet()->setCellValue(('A'.$count), $registration->created_at);
                $excel_file->getActiveSheet()->setCellValue(('B'.$count), $user->id);
                $excel_file->getActiveSheet()->setCellValue(('C'.$count), $user->name);
                $excel_file->getActiveSheet()->setCellValue(('D'.$count), $user->email);
                $excel_file->getActiveSheet()->setCellValue(('E'.$count), $user->phone);
                $excel_file->getActiveSheet()->setCellValue(('F'.$count), $base_amount);
                $excel_file->getActiveSheet()->setCellValue(('G'.$count), $yoken_price);
                $excel_file->getActiveSheet()->setCellValue(('H'.$count), $money_received);
                $amount = $registration->base_fees - ($registration->base_fees * ($registration->base_discount / 100));
                if ($amount <= 0) {
                    $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Free");
                } elseif (!is_null($registration->payment)) {
                    if(strtolower($registration->payment->payment_status) == "pending") {
                        $excel_file->getActiveSheet()->setCellValue(('I'.$count), $registration->payment->payment_status . "\r\nURL: " . $registration->payment->payment_details["longurl"]);
                    } else {
                        $excel_file->getActiveSheet()->setCellValue(('I'.$count), $registration->payment->payment_status);
                    }
                } else {
                    $excel_file->getActiveSheet()->setCellValue(('I'.$count), "Errored");
                }
            }
        } elseif ($request->input('period') == 'wm') {
            $registrations_final_list = $webinar->registrations()->whereDate('created_at', ">", date('Y-m-d', strtotime("-4 week")))->get();
            $count = 1;
            foreach ($registrations_final_list as $registration) {
                $count++;
                $fees = $registration->base_fees;
                $discount = $registration->base_discount;
                $yoken_charge= getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                $base_amount = $fees - (($fees * $discount) / 100);
                $yoken_price = $base_amount * ($yoken_charge / 100);
                $money_received = $base_amount * ((100 - $yoken_charge) / 100);
                $user = \App\User::where('id', $registration->user_id)->first();
                $excel_file->getActiveSheet()->setCellValue(('A'.$count), $registration->created_at);
                $excel_file->getActiveSheet()->setCellValue(('B'.$count), $user->id);
                $excel_file->getActiveSheet()->setCellValue(('C'.$count), $user->name);
                $excel_file->getActiveSheet()->setCellValue(('D'.$count), $user->email);
                $excel_file->getActiveSheet()->setCellValue(('E'.$count), $user->phone);
                $excel_file->getActiveSheet()->setCellValue(('F'.$count), $base_amount);
                $excel_file->getActiveSheet()->setCellValue(('G'.$count), $yoken_price);
                $excel_file->getActiveSheet()->setCellValue(('H'.$count), $money_received);
                $amount = $registration->base_fees - ($registration->base_fees * ($registration->base_discount / 100));
                if ($amount <= 0) {
                    $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Free");
                } elseif (!is_null($registration->payment)) {
                    if(strtolower($registration->payment->payment_status) == "pending") {
                        $excel_file->getActiveSheet()->setCellValue(('I'.$count), $registration->payment->payment_status . "\r\nURL: " . $registration->payment->payment_details["longurl"]);
                    } else {
                        $excel_file->getActiveSheet()->setCellValue(('I'.$count), $registration->payment->payment_status);
                    }
                } else {
                    $excel_file->getActiveSheet()->setCellValue(('I'.$count), "Errored");
                }
            }
        } elseif ($request->input('period') == 'my') {
            $registrations_final_list = $webinar->registrations()->whereDate('created_at', ">", date('Y-m-d', strtotime("-12 month")))->get();
            $count = 1;
            foreach ($registrations_final_list as $registration) {
                $count++;
                $fees = $registration->base_fees;
                $discount = $registration->base_discount;
                $yoken_charge= getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                $base_amount = $fees - (($fees * $discount) / 100);
                $yoken_price = $base_amount * ($yoken_charge / 100);
                $money_received = $base_amount * ((100 - $yoken_charge) / 100);
                $user = \App\User::where('id', $registration->user_id)->first();
                $excel_file->getActiveSheet()->setCellValue(('A'.$count), $registration->created_at);
                $excel_file->getActiveSheet()->setCellValue(('B'.$count), $user->id);
                $excel_file->getActiveSheet()->setCellValue(('C'.$count), $user->name);
                $excel_file->getActiveSheet()->setCellValue(('D'.$count), $user->email);
                $excel_file->getActiveSheet()->setCellValue(('E'.$count), $user->phone);
                $excel_file->getActiveSheet()->setCellValue(('F'.$count), $base_amount);
                $excel_file->getActiveSheet()->setCellValue(('G'.$count), $yoken_price);
                $excel_file->getActiveSheet()->setCellValue(('H'.$count), $money_received);
                $amount = $registration->base_fees - ($registration->base_fees * ($registration->base_discount / 100));
                if ($amount <= 0) {
                    $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Free");
                } elseif (!is_null($registration->payment)) {
                    if(strtolower($registration->payment->payment_status) == "pending") {
                        $excel_file->getActiveSheet()->setCellValue(('I'.$count), $registration->payment->payment_status . "\r\nURL: " . $registration->payment->payment_details["longurl"]);
                    } else {
                        $excel_file->getActiveSheet()->setCellValue(('I'.$count), $registration->payment->payment_status);
                    }
                } else {
                    $excel_file->getActiveSheet()->setCellValue(('I'.$count), "Errored");
                }
            }
        } else {
            $registrations_final_list = $webinar->registrations()->whereDate('created_at', ">", date('Y-m-d', strtotime("-10 year")))->get();
            $count = 1;
            foreach ($registrations_final_list as $registration) {
                $count++;
                $fees = $registration->base_fees;
                $discount = $registration->base_discount;
                $yoken_charge= getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                $base_amount = $fees - (($fees * $discount) / 100);
                $yoken_price = $base_amount * ($yoken_charge / 100);
                $money_received = $base_amount * ((100 - $yoken_charge) / 100);
                $user = \App\User::where('id', $registration->user_id)->first();
                $excel_file->getActiveSheet()->setCellValue(('A'.$count), $registration->created_at);
                $excel_file->getActiveSheet()->setCellValue(('B'.$count), $user->id);
                $excel_file->getActiveSheet()->setCellValue(('C'.$count), $user->name);
                $excel_file->getActiveSheet()->setCellValue(('D'.$count), $user->email);
                $excel_file->getActiveSheet()->setCellValue(('E'.$count), $user->phone);
                $excel_file->getActiveSheet()->setCellValue(('F'.$count), $base_amount);
                $excel_file->getActiveSheet()->setCellValue(('G'.$count), $yoken_price);
                $excel_file->getActiveSheet()->setCellValue(('H'.$count), $money_received);
                $amount = $registration->base_fees - ($registration->base_fees * ($registration->base_discount / 100));
                if ($amount <= 0) {
                    $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Free");
                } elseif (!is_null($registration->payment)) {
                    if(strtolower($registration->payment->payment_status) == "pending") {
                        $excel_file->getActiveSheet()->setCellValue(('I'.$count), $registration->payment->payment_status . "\r\nURL: " . $registration->payment->payment_details["longurl"]);
                    } else {
                        $excel_file->getActiveSheet()->setCellValue(('I'.$count), $registration->payment->payment_status);
                    }
                } else {
                    $excel_file->getActiveSheet()->setCellValue(('I'.$count), "Errored");
                }
            }
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . preg_replace('/[^a-z0-9]+/', '-', strtolower($webinar->name)) . ' Registrations.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $excel_writer = \PHPExcel_IOFactory::createWriter($excel_file, 'Excel2007');
        $excel_writer->save('php://output');
    }
    public function webinarDownloadAll()
    {
        $excel_file = new \PHPExcel();
        $excel_file->getProperties()->setTitle("Document");
        $webinars_list = \App\Webinar::all();
        $sheet_count = 0;
        foreach ($webinars_list as $webinar) {
            $excel_file->createSheet($sheet_count);
            $excel_file->setActiveSheetIndex($sheet_count);
            $excel_file->getActiveSheet()->setTitle("Webinar --- " . substr(preg_replace('/[^a-z0-9]+/', '-', $webinar->name), 0, 18));
            $sheet_count++;
            $excel_file->getActiveSheet()->setCellValue('A1', "Time");
            $excel_file->getActiveSheet()->setCellValue('B1', "User ID");
            $excel_file->getActiveSheet()->setCellValue('C1', "User Name");
            $excel_file->getActiveSheet()->setCellValue('D1', "User Email");
            $excel_file->getActiveSheet()->setCellValue('E1', "User Phone");
            $excel_file->getActiveSheet()->setCellValue('F1', "Base Price (Discounted)");
            $excel_file->getActiveSheet()->setCellValue('G1', "Yoken Fees");
            $excel_file->getActiveSheet()->setCellValue('H1', "Fees Received");
            $excel_file->getActiveSheet()->setCellValue('I1', "Payment Status");
            $registrations_final_list = $webinar->registrations()->get();
            $count = 1;
            foreach ($registrations_final_list as $registration) {
                $count++;
                $fees = $registration->base_fees;
                $discount = $registration->base_discount;
                $yoken_charge= getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                $base_amount = $fees - (($fees * $discount) / 100);
                $yoken_price = $base_amount * ($yoken_charge / 100);
                $money_received = $base_amount * ((100 - $yoken_charge) / 100);
                $user = \App\User::where('id', $registration->user_id)->first();
                $excel_file->getActiveSheet()->setCellValue(('A'.$count), $registration->created_at);
                $excel_file->getActiveSheet()->setCellValue(('B'.$count), $user->id);
                $excel_file->getActiveSheet()->setCellValue(('C'.$count), $user->name);
                $excel_file->getActiveSheet()->setCellValue(('D'.$count), $user->email);
                $excel_file->getActiveSheet()->setCellValue(('E'.$count), $user->phone);
                $excel_file->getActiveSheet()->setCellValue(('F'.$count), $base_amount);
                $excel_file->getActiveSheet()->setCellValue(('G'.$count), $yoken_price);
                $excel_file->getActiveSheet()->setCellValue(('H'.$count), $money_received);
                $amount = $registration->base_fees - ($registration->base_fees * ($registration->base_discount / 100));
                if ($amount <= 0) {
                    $excel_file->getActiveSheet()->setCellValue(('M'.$count), "Free");
                } elseif (!is_null($registration->payment)) {
                    if(strtolower($registration->payment->payment_status) == "pending") {
                        $excel_file->getActiveSheet()->setCellValue(('I'.$count), $registration->payment->payment_status . "\r\nURL: " . $registration->payment->payment_details["longurl"]);
                    } else {
                        $excel_file->getActiveSheet()->setCellValue(('I'.$count), $registration->payment->payment_status);
                    }
                } else {
                    $excel_file->getActiveSheet()->setCellValue(('I'.$count), "Errored");
                }
            }
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Webinars Registrations.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $excel_writer = \PHPExcel_IOFactory::createWriter($excel_file, 'Excel2007');
        $excel_writer->save('php://output');
    }
    public function showInstituteList ()
    {
        $institutes = Institute::all();
        return view('admin.institute-listing', compact('institutes'));
    }
    public function showEditInstitute(Institute $institute)
    {
        return view('admin.institute-edit', compact('institute'));
    }
    public function showCreateInstitute()
    {
        return view('admin.institute-create');
    }

    public function listCourses($instituteId)
    {
        $courses = Course::where('institute_id',$instituteId)->get();
        return View::make('admin.courses-listing',[
            'courses' => $courses,
        ]);
    }
    public function updateInstitute (Request $request)
    {
        $this->validate($request,[
            'name' => 'required',
            'description' => 'required',
            'state' => 'required',
            'city' => 'required',
            'location' => 'required',
            'contact' => 'required',
            'affiliation' => 'required',
            'no_of_students' => 'required',
            'logo_upload' => 'max:5120',
            'instituteId' => 'required',
        ]);
        $institute = \App\Institute::find($request->instituteId);
        $institute->name = $request->name;
        $institute->description = $request->description;
        $institute->state = $request->state;
        $institute->city = $request->city;
        $institute->location = $request->location;
        $institute->contact = $request->contact;
        $institute->affiliation = $request->affiliation;
        $institute->no_of_students = $request->no_of_students;
        //return $request->all();
        if($request->hasFile('logo_upload'))
        {
            $originalPath = $institute->logo_file;
            \Storage::delete($originalPath);
            $path = $request->file('logo_upload')->store('institute_logos');
            $institute->logo_file = $path;
        }
        if($request->hasFile('institution_display_pictures'))
        {
            $paths = [];
            $files = $request->file('institution_display_pictures');
            foreach ($files as $file) {
                $path = $file->store('institution_display_pictures');
                $paths[] = $path;
            }
            $institute->display_pic_links = implode(';', $paths);
        }
        $institute->save();
        return redirect()->back();
    }
    public function createInstitute (Request $request)
    {
        $this->validate($request,[
            'name' => 'required',
            'description' => 'required',
            'state' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'city' => 'required',
            'location' => 'required',
            'contact' => 'required',
            'affiliation' => 'required',
            'no_of_students' => 'required',
            'logo_upload' => 'max:5120'
        ]);
        $institute = new \App\Institute;
        $institute->name = $request->name;
        $institute->description = $request->description;
        $institute->state = $request->state;
        $institute->email = $request->email;
        $institute->address = $request->address;
        $institute->city = $request->city;
        $institute->location = $request->location;
        $institute->contact = $request->contact;
        $institute->affiliation = $request->affiliation;
        $institute->no_of_students = $request->no_of_students;
        //return $request->all();
        if($request->hasFile('logo_upload'))
        {
            $originalPath = $institute->logo_file;
            \Storage::delete($originalPath);
            $path = $request->file('logo_upload')->store('institute_logos');
            $institute->logo_file = $path;
        }
        if($request->hasFile('institution_display_pictures'))
        {
            $paths = [];
            $files = $request->file('institution_display_pictures');
            foreach ($files as $file) {
                $path = $file->store('institution_display_pictures');
                $paths[] = $path;
            }
            $institute->display_pic_links = implode(';', $paths);
        }
        $password = str_random(8);
        $institute->display_pic_links = implode(';', $paths);
        $institute->password = bcrypt($password);
        $institute->save();
        \Mail::to($institute)->send(new \App\Mail\Institute($institute, $password));
        return redirect()->back();
    }
    public function updateBatch(Request $request)
    {
        $this->validate($request,[
            'no_of_seats' => 'required',
            'commence_date' => 'required',
            'days' => 'required',
            'timings' => 'required',
            'batchId' => 'required'
        ]);
        $batch =  \App\Batch::find($request->batchId);
        $batch->no_of_seats = $request->no_of_seats;
        $batch->commence_date = $request->commence_date;
        $batch->days = implode(';',$request->days);
        $batch->timings = implode(';',$request->timings);
        $batch->save();
        return redirect("/admin/batches/" . $batch->course->id);
    }
    public function updateFaculty(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
            'experience' => 'required',
            'speciality' => 'required',
            'facultyId' => 'required',
            'faculty_image' => 'max:5120'
        ]);

        $faculty = \App\Faculty::find($request->facultyId);
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

    public function updateCourse(Request $request)
    {
        $this->validate($request,[
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
        ],[
            'name.required' => '',
        ]);
        $course = Course::find($request->id);
        $course->name = $request->name;
        $course->description = $request->description;
        $course->demo_classes = $request->demo_classes;
        if($request->hasFile('course_image'))
        {
            $fileName = md5(uniqid(rand(), true)) . '.'. $request->file('course_image')->getClientOriginalExtension();
            $path = storage_path('app/public/course_logos/' . $fileName);
            $width = Image::make($request->file('course_image'))->width();
            $height = Image::make($request->file('course_image'))->height();
            if($width >= 500 && $width>=$height)
            {
                $resizedLogo = Image::make($request->file('course_image'))
                    ->resize(500, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
            }
            else if($height>=500 && $height>$width){
                $resizedLogo = Image::make($request->file('course_image'))
                    ->resize(null, 500, function ($constraint) {
                        $constraint->aspectRatio();
                    });
            }
            else $resizedLogo = Image::make($request->file('course_image'));
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
        \DB::transaction(function() use ($course, $request) {
            $course->save();
            \DB::table('category_course')->where('course_id', $request->id)->delete();
            foreach($request->category as $category)
            {
                \DB::table('category_course')->insert([
                    'category_id' => $category,'course_id' => $request->id
                ]);
            }
        });
        session()->flash('status', 'Course has been Updated.');
        return redirect()->back();
    }
}
