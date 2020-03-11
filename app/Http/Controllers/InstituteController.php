<?php

namespace App\Http\Controllers;

use App\Institute;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;

class InstituteController extends Controller
{
    public function __construct()
    {
        $this->middleware('instituteOrAdmin', ['except'=> ['index']]);
        $this->middleware('auth:institute', ['only'=> ['index']]);
    }
    public function index()
    {
        $courses = \App\Course::where('institute_id', \Auth::guard('institute')->user()->id)->pluck('id')->toArray();
        $batches = \App\Batch::whereIn('course_id', $courses)->pluck('id')->toArray();
        $enrollments = $enrollments_courses = $labels = $pie_labels = [];
        $enrollments [0] = $enrollments [1] = [];
        for ($i = 0; $i < 12; $i ++) {
            $labels[] = '"' . date('F', strtotime("-" . (11 - $i) . " month")) . '"' ;
        }
        $labels = implode(", ", $labels);
        for ($i = 0; $i < 12; $i ++) {
            $enrollments_list = \App\Enrollment::whereIn('batch_id', $batches)->whereDate('created_at', ">", date('Y-m-d', strtotime("-" . (12 - $i) . " month")))->whereDate('created_at', "<=", date('Y-m-d', strtotime("-" . (11 - $i) . " month")))->get();
            $offline_money_received = $online_money_received = 0;
            foreach ($enrollments_list as $enrollment) {
                $fees = $enrollment->base_fees;
                $discount = $enrollment->base_discount;
                $yoken_charge= getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                $base_amount = $fees - (($fees * $discount) / 100);
                if ($enrollment->type === 1) {
                    $online_money_received += ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ((100 - $yoken_charge) / 100);
                } else {
                    $offline_money_received += ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ((100 - $yoken_charge) / 100);
                }
            }
            $enrollments [0][$i] = $offline_money_received;
            $enrollments [1][$i] = $online_money_received;
        }
        $courses = \App\Course::where('institute_id', \Auth::guard('institute')->user()->id)->get();
        foreach ($courses as $course) {
            $batches = \App\Batch::where('course_id', $course->id)->pluck('id')->toArray();
            $enrollments_list = \App\Enrollment::whereIn('batch_id', $batches)->get();
            $money_received = 0;
            foreach ($enrollments_list as $enrollment) {
                $fees = $enrollment->base_fees;
                $discount = $enrollment->base_discount;
                $yoken_charge= getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                $base_amount = $fees - (($fees * $discount) / 100);
                $money_received += ((100 * $base_amount) / (100 + $yoken_charge)) - $enrollment->institute_rebate;
            }
            $enrollments_courses [$course->id] = $money_received;
            $pie_labels [] = '"' . $course->name . '"';
        }
        $pie_labels = implode(", ", $pie_labels);
        return view('institute.institute-home', [
            'enrollment'            => $enrollments,
            'labels'                => $labels,
            'enrollment_courses'    => $enrollments_courses,
            'pie_labels'            => $pie_labels,
            'courses'               => $courses
        ]);
    }
    public function report(Request $request)
    {
        $courses = \App\Course::where('institute_id', \Auth::guard('institute')->user()->id)->get();
        if (!$request->has('course') || !$request->has('period')) {
            if ($request->has('course') || $request->has('period')) {
                $this->validate($request, [
                    "course" => "required",
                    "period" => "required|in:hd,dw,wm,my,yy"
                ]);
            }
            return view('institute.report', [
                'courses' => $courses
            ]);
        } else {
            $this->validate($request, [
                "course" => "required",
                "period" => "required|in:hd,dw,wm,my,yy"
            ]);
            $course = \App\Course::find($request->input('course'));
            $batches = \App\Batch::where('course_id', $course->id)->get();
            $enrollments_batch = $enrollments_general = $enrollments_batch_general = [];
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
            foreach ($batches as $batch) {
                if ($request->input('period') == 'hd') {
                    $enrollment_final_list = \App\Enrollment::where('batch_id', $batch->id)->whereDate('created_at', date('Y-m-d'))->get();
                    if ($enrollment_final_list->count()) {
                        $enrollments_batch [$batch->id] = [
                            0 => [],
                            1 => []
                        ];
                        for ($i = 0; $i < 24; $i ++) {
                            $enrollments_list = \App\Enrollment::where('batch_id', $batch->id)->whereRaw('HOUR(created_at) = '. date("H", strtotime("-" . (23 - $i) . " hour")))->whereDate('created_at', date('Y-m-d', strtotime("-" . (23 - $i) . " hour")))->get();
                            $online_money_received = $offline_money_received = 0;
                            foreach ($enrollments_list as $enrollment) {
                                $fees = $enrollment->base_fees;
                                $discount = $enrollment->base_discount;
                                $yoken_charge= getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                                $base_amount = $fees - (($fees * $discount) / 100);
                                if ($enrollment->type === 1) {
                                    $online_money_received += ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ((100 - $yoken_charge) / 100);
                                } else {
                                    $offline_money_received += ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ((100 - $yoken_charge) / 100);
                                }
                            }
                            $enrollments_batch [$batch->id][0][$i] = $offline_money_received;
                            $enrollments_batch [$batch->id][1][$i] = $online_money_received;
                            if (!isset($enrollments_general [0][$i])) {
                                $enrollments_general [0][$i] = $offline_money_received;
                                $enrollments_general [1][$i] = $online_money_received;
                            } else {
                                $enrollments_general [0][$i] += $offline_money_received;
                                $enrollments_general [1][$i] += $online_money_received;
                            }
                        }
                        $enrollments_users_list [$batch->id] = $enrollment_final_list;
                    }
                } elseif ($request->input('period') == 'dw') {
                    $enrollment_final_list = \App\Enrollment::where('batch_id', $batch->id)->whereDate('created_at', ">", date('Y-m-d', strtotime("-7 days")))->get();
                    if ($enrollment_final_list->count()) {
                        $enrollments_batch [$batch->id] = [
                            0 => [],
                            1 => []
                        ];
                        for ($i = 0; $i < 7; $i ++) {
                            $enrollments_list = \App\Enrollment::where('batch_id', $batch->id)->whereDate('created_at', date('Y-m-d', strtotime("-" . (6 - $i) . " day")))->get();
                            $online_money_received = $offline_money_received = 0;
                            foreach ($enrollments_list as $enrollment) {
                                $fees = $enrollment->base_fees;
                                $discount = $enrollment->base_discount;
                                $yoken_charge= getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                                $base_amount = $fees - (($fees * $discount) / 100);
                                if ($enrollment->type === 1) {
                                    $online_money_received += ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ((100 - $yoken_charge) / 100);
                                } else {
                                    $offline_money_received += ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ((100 - $yoken_charge) / 100);
                                }
                            }
                            $enrollments_batch [$batch->id][0][$i] = $offline_money_received;
                            $enrollments_batch [$batch->id][1][$i] = $online_money_received;
                            if (!isset($enrollments_general [0][$i])) {
                                $enrollments_general [0][$i] = $offline_money_received;
                                $enrollments_general [1][$i] = $online_money_received;
                            } else {
                                $enrollments_general [0][$i] += $offline_money_received;
                                $enrollments_general [1][$i] += $online_money_received;
                            }
                        }
                        $enrollments_users_list [$batch->id] = $enrollment_final_list;
                    }
                } elseif ($request->input('period') == 'wm') {
                    $enrollment_final_list = \App\Enrollment::where('batch_id', $batch->id)->whereDate('created_at', ">", date('Y-m-d', strtotime("-4 week")))->get();
                    if ($enrollment_final_list->count()) {
                        $enrollments_batch [$batch->id] = [
                            0 => [],
                            1 => []
                        ];
                        for ($i = 0; $i < 4; $i ++) {
                            $enrollments_list = \App\Enrollment::where('batch_id', $batch->id)->whereDate('created_at', ">", date('Y-m-d', strtotime("-" . (4 - $i) . " week")))->whereDate('created_at', "<=", date('Y-m-d', strtotime("-" . (3 - $i) . " week")))->get();
                            $online_money_received = $offline_money_received = 0;
                            foreach ($enrollments_list as $enrollment) {
                                $fees = $enrollment->base_fees;
                                $discount = $enrollment->base_discount;
                                $yoken_charge= getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                                $base_amount = $fees - (($fees * $discount) / 100);
                                if ($enrollment->type === 1) {
                                    $online_money_received += ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ((100 - $yoken_charge) / 100);
                                } else {
                                    $offline_money_received += ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ((100 - $yoken_charge) / 100);
                                }
                            }
                            $enrollments_batch [$batch->id][0][$i] = $offline_money_received;
                            $enrollments_batch [$batch->id][1][$i] = $online_money_received;
                            if (!isset($enrollments_general [0][$i])) {
                                $enrollments_general [0][$i] = $offline_money_received;
                                $enrollments_general [1][$i] = $online_money_received;
                            } else {
                                $enrollments_general [0][$i] += $offline_money_received;
                                $enrollments_general [1][$i] += $online_money_received;
                            }
                        }
                        $enrollments_users_list [$batch->id] = $enrollment_final_list;
                    }
                } elseif ($request->input('period') == 'my') {
                    $enrollment_final_list = \App\Enrollment::where('batch_id', $batch->id)->whereDate('created_at', ">", date('Y-m-d', strtotime("-12 month")))->get();
                    if ($enrollment_final_list->count()) {
                        $enrollments_batch [$batch->id] = [
                            0 => [],
                            1 => []
                        ];
                        for ($i = 0; $i < 12; $i ++) {
                            $enrollments_list = \App\Enrollment::where('batch_id', $batch->id)->whereDate('created_at', ">", date('Y-m-d', strtotime("-" . (12 - $i) . " month")))->whereDate('created_at', "<=", date('Y-m-d', strtotime("-" . (11 - $i) . " month")))->get();
                            $online_money_received = $offline_money_received = 0;
                            foreach ($enrollments_list as $enrollment) {
                                $fees = $enrollment->base_fees;
                                $discount = $enrollment->base_discount;
                                $yoken_charge= getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                                $base_amount = $fees - (($fees * $discount) / 100);
                                if ($enrollment->type === 1) {
                                    $online_money_received += ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ((100 - $yoken_charge) / 100);
                                } else {
                                    $offline_money_received += ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ((100 - $yoken_charge) / 100);
                                }
                            }
                            $enrollments_batch [$batch->id][0][$i] = $offline_money_received;
                            $enrollments_batch [$batch->id][1][$i] = $online_money_received;
                            if (!isset($enrollments_general [0][$i])) {
                                $enrollments_general [0][$i] = $offline_money_received;
                                $enrollments_general [1][$i] = $online_money_received;
                            } else {
                                $enrollments_general [0][$i] += $offline_money_received;
                                $enrollments_general [1][$i] += $online_money_received;
                            }
                        }
                        $enrollments_users_list [$batch->id] = $enrollment_final_list;
                    }
                } else {
                    $enrollment_final_list = \App\Enrollment::where('batch_id', $batch->id)->whereDate('created_at', ">", date('Y-m-d', strtotime("-10 year")))->get();
                    if ($enrollment_final_list->count()) {
                        $enrollments_batch [$batch->id] = [
                            0 => [],
                            1 => []
                        ];
                        for ($i = 0; $i < 10; $i ++) {
                            $enrollments_list = \App\Enrollment::where('batch_id', $batch->id)->whereDate('created_at', ">", date('Y-m-d', strtotime("-" . (10 - $i) . " year")))->whereDate('created_at', "<=", date('Y-m-d', strtotime("-" . (9 - $i) . " year")))->get();
                            $online_money_received = $offline_money_received = 0;
                            foreach ($enrollments_list as $enrollment) {
                                $fees = $enrollment->base_fees;
                                $discount = $enrollment->base_discount;
                                $yoken_charge= getenv('YOKEN_CHARGE'); //Assuming yoken charges 8 percent, maybe make this possible to set in the database?
                                $base_amount = $fees - (($fees * $discount) / 100);
                                if ($enrollment->type === 1) {
                                    $online_money_received += ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ((100 - $yoken_charge) / 100);
                                } else {
                                    $offline_money_received += ($base_amount - $enrollment->institute_rebate - $enrollment->yoken_rebate) * ((100 - $yoken_charge) / 100);
                                }
                            }
                            $enrollments_batch [$batch->id][0][$i] = $offline_money_received;
                            $enrollments_batch [$batch->id][1][$i] = $online_money_received;
                            if (!isset($enrollments_general [0][$i])) {
                                $enrollments_general [0][$i] = $offline_money_received;
                                $enrollments_general [1][$i] = $online_money_received;
                            } else {
                                $enrollments_general [0][$i] += $offline_money_received;
                                $enrollments_general [1][$i] += $online_money_received;
                            }
                        }
                        $enrollments_users_list [$batch->id] = $enrollment_final_list;
                    }
                }
                if (isset($enrollments_batch [$batch->id])) {
                    $enrollments_batch_general [$batch->id] = 0;
                    foreach ($enrollments_batch [$batch->id] as $value) {
                        $enrollments_batch_general [$batch->id] = array_sum($enrollments_batch [$batch->id][0]) + array_sum($enrollments_batch [$batch->id][1]);
                    }
                }
                $pie_labels [] = '"' . $batch->commence_date . '"' ;
            }
            $pie_labels = implode(", ", $pie_labels);
            return view('institute.report', [
                'courses'                   => $courses,
                'course_details'            => $course,
                'period'                    => $request->input('period'),
                'batches'                   => $batches,
                'enrollments_batch'         => $enrollments_batch,
                'labels'                    => $labels,
                'pie_labels'                => $pie_labels,
                'enrollments_general'       => $enrollments_general,
                'enrollments_batch_general' => $enrollments_batch_general,
                'enrollments_users_list'    => $enrollments_users_list
            ]);
        }
    }
    public function download(Request $request)
    {
        $excel_file = new \PHPExcel();
        $excel_file->getProperties()->setTitle("Document");
        $excel_file->removeSheetByIndex(0);
        $course = \App\Course::find($request->input('course'));
        $batches = \App\Batch::where('course_id', $course->id)->get();
        $sheet_count = 0;
        foreach ($batches as $batch) {
            $excel_file->createSheet($sheet_count);
            $excel_file->setActiveSheetIndex($sheet_count);
            $sheet_count++;
            $excel_file->getActiveSheet()->setTitle("Batch --- " . str_replace("/", "-", $batch->commence_date));
            $excel_file->getActiveSheet()->setCellValue('A1', "Time");
            $excel_file->getActiveSheet()->setCellValue('B1', "User ID");
            $excel_file->getActiveSheet()->setCellValue('C1', "User Name");
            $excel_file->getActiveSheet()->setCellValue('D1', "User Email");
            $excel_file->getActiveSheet()->setCellValue('E1', "User Phone");
            $excel_file->getActiveSheet()->setCellValue('F1', "Course ID");
            $excel_file->getActiveSheet()->setCellValue('G1', "Course Name");
            $excel_file->getActiveSheet()->setCellValue('H1', "Base Price (Discounted)");
            $excel_file->getActiveSheet()->setCellValue('I1', "Institute Promo");
            $excel_file->getActiveSheet()->setCellValue('J1', "Yoken Promo");
            $excel_file->getActiveSheet()->setCellValue('K1', "Yoken Fees");
            $excel_file->getActiveSheet()->setCellValue('L1', "Fees Received");
            $excel_file->getActiveSheet()->setCellValue('M1', "Payment Type");
            $excel_file->getActiveSheet()->setCellValue('N1', "Payment Mode");
            $excel_file->getActiveSheet()->setCellValue('O1', "Payment Status");
            if ($request->input('period') == 'hd') {
                $enrollment_final_list = \App\Enrollment::where('batch_id', $batch->id)->whereDate('created_at', date('Y-m-d'))->get();
                $count = 1;
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
            } elseif ($request->input('period') == 'dw') {
                $enrollment_final_list = \App\Enrollment::where('batch_id', $batch->id)->whereDate('created_at', date('Y-m-d'))->get();
                $count = 1;
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
                $count = 1;
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
                $count = 1;
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
                $count = 1;
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
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $course->name . ' Enrollments.xlsx"');
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
        $institute = \Auth::guard('institute')->user();
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
    public function showDetails()
    {
        return view('institute.my-institute', [
            'institute'=> \Auth::guard('institute')->user()
        ]);
    }
    public function showEdit()
    {
        return view('institute.edit', [
            'institute'=>\Auth::guard('institute')->user()
        ]);
    }
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
            'state' => 'required',
            'city' => 'required',
            'location' => 'required',
            'contact' => 'required',
            'affiliation' => 'required',
            'no_of_students' => 'required',
            'logo_upload' => 'max:5120',
            'institution_display_pictures.*' => 'max:5120'
        ]);
        $institute = \App\Institute::find(\Auth::guard('institute')->user()->id);
        $institute->name = $request->name;
        $institute->description = $request->description;
        $institute->state = $request->state;
        $institute->city = $request->city;
        $institute->location = $request->location;
        $institute->contact = $request->contact;
        $institute->affiliation = $request->affiliation;
        $institute->no_of_students = $request->no_of_students;
        //return $request->all();
        if ($request->hasFile('logo_upload')) {
            $fileName = md5(uniqid(rand(), true)) . '.'. $request->file('logo_upload')->getClientOriginalExtension();
            $path = storage_path('app/public/institute_logos/' . $fileName);
            $logo = Image::make($request->file('logo_upload'));
            $originalPath = $institute->logo_file;
            \Storage::delete(str_replace(storage_path() . '/app/public', '', $originalPath));
            $logo->save($path);
            $institute->logo_file = $path;
        }
        if ($request->hasFile('institution_display_pictures')) {
            $images = explode(';', $institute->display_pic_links);
            foreach ($images as $image) {
                \Storage::delete(str_replace(storage_path() . '/app/public', '', $image));
            }
            $url = '';
            foreach ($request->file('institution_display_pictures') as $image) {
                $fileName = md5(uniqid(rand(), true)) . '.'. $image->getClientOriginalExtension();
                $path = storage_path('app/public/institute_display/' . $fileName);
                $logo = Image::make($image);
                //$originalPath = $institute->logo_file;
                //\Storage::delete(str_replace(storage_path() . '/app/public', '', $originalPath));
                $logo->save($path);
                $url .= $path . ';';
            }
            $institute->display_pic_links = $url;
        }
        $institute->save();
        return redirect()->back();
    }

    /*
     *  Show change password form for institute.
     */
    public function changePasswordShow()
    {
        return view('institute.change-password');
    }

    /*
     *  Attempt to change password here.
     */
    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'new_password' => 'required|confirmed|min:6'
        ]);
        $institute = Institute::find(\Auth::guard('institute')->user()->id);
        if (\Hash::check($request->old_password, $institute->password)) {
            $institute->password = \Hash::make($request->new_password);
            $institute->save();
            session()->flash('password_changed', 'Password has been changed successfully.');
            return redirect()->back();
        } else {
            return redirect()->back()->withErrors([
                'message' => 'Incorrect Password.'
            ]);
        }
    }
}
