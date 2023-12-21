<?php

namespace App\Http\Controllers\Backend;

use App\Model\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Model\ProductWarehousing;
use Carbon\Carbon;
use App\Model\Supplier;
use Excel;

class WarehouseController extends Controller
{
    public function __construct()
    {
        $suppliers = Supplier::all();
        view()->share([
            'suppliers' => $suppliers
        ]);
    }
    //
    public function index(Request $request)
    {
        $products = ProductWarehousing::with([
            'product' => function($product)
            {
                $product->select('*')->with('unit');
            }
        ])->select('pw_product_id', DB::raw('SUM(pw_total_number) AS total_product'));

        if ($request->p_code) {
            $pro = Product::where('p_code', $request->p_code)->first();
            if ($pro) {
                $products = $products->where('pw_product_id', $pro->id);
            } else {
                $products = $products->where('pw_product_id', NULL);
            }
        }

        if ($request->p_name) {
             $prod= Product::where('p_name','like','%'. $request->p_name .'%')->pluck('id')->toArray();

             if ($prod) {
                 $products = $products->whereIn('pw_product_id', $prod);
             } else {
                 $products = $products->where('pw_product_id', NULL);
             }
        }
        if($request->supplier_id) {
            $products = $products->where('pw_supplier_id', $request->supplier_id);
        }
        $products = $products->groupBy('pw_product_id')->paginate(10);

        $viewData = [
            'products' => $products,
            'warehouse' => 'active',
        ];

        return view('backend.warehouse.index', $viewData);
    }

    public function statistical(Request $request)
    {
        $products = ProductWarehousing::with([
        'product' => function($product)
        {
            $product->select('*')->with('unit');
        }
        ])->select('pw_product_id', DB::raw('SUM(pw_total_number) AS total_product'));

        $productsGoodsIssue = ProductWarehousing::select('pw_product_id', DB::raw('SUM(pw_total_number) AS total_product_goods_issue'))->where('pw_type', 1);
        $productsGoodsReceipt = ProductWarehousing::select('pw_product_id', DB::raw('SUM(pw_total_number) AS total_product_goods_receipt'))->where('pw_type', 2);

        if($request->p_code) {
            $product = Product::where('p_code', $request->p_code)->first();
            $id = '';
            if ($product) {
                $id = $product->id;
            }
            $products = $products->where('pw_product_id', $id);
            $productsGoodsIssue = $productsGoodsIssue->where('pw_product_id', $id);
            $productsGoodsReceipt = $productsGoodsReceipt->where('pw_product_id', $id);
        }

        if($request->supplier_id) {
            $products = $products->where('pw_supplier_id', $request->supplier_id);
            $productsGoodsIssue = $productsGoodsIssue->where('pw_supplier_id', $request->supplier_id);
            $productsGoodsReceipt = $productsGoodsReceipt->where('pw_supplier_id', $request->supplier_id);
        }

        if ($request->start_day) {

            $time_form = Carbon::createFromFormat('Y-m-d', $request->start_day)->setTime(00,00,00);
            $products = $products->where('created_at', '>=',  $time_form);
            $productsGoodsIssue = $productsGoodsIssue->where('created_at', '>=',  $time_form);
            $productsGoodsReceipt = $productsGoodsReceipt->where('created_at', '>=',  $time_form);
        }

        if ($request->end_day) {
            $time_to   = Carbon::createFromFormat('Y-m-d', $request->end_day)->setTime(23,59,59);
            $products = $products->where('created_at', '<=',  $time_to);
            $productsGoodsIssue = $productsGoodsIssue->where('created_at', '<=',  $time_to);
            $productsGoodsReceipt = $productsGoodsReceipt->where('created_at', '<=',  $time_to);
        }

        $products = $products->groupBy('pw_product_id');
        $productsGoodsIssue = $productsGoodsIssue->groupBy('pw_product_id');
        $productsGoodsReceipt = $productsGoodsReceipt->groupBy('pw_product_id');

        if (isset($request->export) && $request->export == 'excel') {
            try{
                $products = $products->groupBy('pw_product_id')->get();
                $productsGoodsIssue = $productsGoodsIssue->groupBy('pw_product_id')->get();
                $productsGoodsReceipt = $productsGoodsReceipt->groupBy('pw_product_id')->get();

                return Excel::create('thong-ke-nhap-xuat'.Carbon::now(), function($excel) use ($products, $productsGoodsIssue, $productsGoodsReceipt) {

                    $excel->sheet('Thống kê nhập xuất', function ($sheet) use ($products, $productsGoodsIssue, $productsGoodsReceipt) {

                        $sheet->cell('A1', function ($cell) {
                            $cell->setValue('STT');
                            $cell->setAlignment('center');
                        });

                        $sheet->cell('B1', function ($cell) {
                            $cell->setValue('Mã hàng hóa');
                            $cell->setAlignment('center');
                        });
                        $sheet->cell('C1', function ($cell) {
                            $cell->setValue('Tên hàng hóa');
                            $cell->setAlignment('center');
                        });

                        $sheet->cell('D1', function ($cell) {
                            $cell->setValue('Đơn vị');
                            $cell->setAlignment('center');
                        });

                        $sheet->cell('E1', function ($cell) {
                            $cell->setValue('Số lượng nhập');
                            $cell->setAlignment('center');
                        });

                        $sheet->cell('F1', function ($cell) {
                            $cell->setValue('Số lượng xuất');
                            $cell->setAlignment('center');
                        });

                        $sheet->cell('G1', function ($cell) {
                            $cell->setValue('Tồn kho');
                            $cell->setAlignment('center');
                        });

                        foreach ($products as $key => $product)
                        {
                            $i= $key+2;
                            $sheet->cell('A'.$i, $key + 1);
                            $sheet->cell('B'.$i, $product->product->p_code);
                            $sheet->cell('C'.$i, $product->product->p_name);
                            $sheet->cell('D'.$i, $product->product->unit->u_name);
                            $total_product_goods_issue = 0;
                            foreach($productsGoodsIssue as $keys => $goodsIssue) {
                                if($goodsIssue->pw_product_id ==  $product->pw_product_id) {
                                    $total_product_goods_issue = $goodsIssue->total_product_goods_issue;
                                }
                            }
                            $sheet->cell('E'.$i, $total_product_goods_issue);
                            $total_product_goods_receipt = 0;
                            foreach($productsGoodsReceipt as $ke => $goodsReceipt) {
                                if($goodsReceipt->pw_product_id ==  $product->pw_product_id) {
                                    $total_product_goods_receipt = str_replace('-', '', $goodsReceipt->total_product_goods_receipt);
                                }
                            }
                            $sheet->cell('F'.$i, $total_product_goods_receipt);
                            $sheet->cell('G'.$i, $product->total_product);
                        }
                    });

                })->download('xlsx');

            }catch (\Exception $exception) {
                Log::error("[Error Export xlsx ]".$exception->getMessage());
                return redirect()->back()->with('danger',"- " .$exception->getMessage());
            }
        }
        $products = $products->paginate(10);
        $productsGoodsIssue = $productsGoodsIssue->paginate(10);
        $productsGoodsReceipt = $productsGoodsReceipt->paginate(10);

        $viewData = [
            'products' => $products,
            'productsGoodsIssue' => $productsGoodsIssue,
            'productsGoodsReceipt' => $productsGoodsReceipt,
            'statistical' => 'active',
        ];

        return view('backend.warehouse.statistical', $viewData);
    }

    public function revenue(Request $request)
    {
        $products = ProductWarehousing::with([
            'product' => function($product)
            {
                $product->select('*')->with('unit');
            },
        ])->select('pw_product_id');
        $productsGoodsIssue = ProductWarehousing::select('pw_product_id', DB::raw('SUM(pw_total_price) AS total_product_goods_issue'))->where('pw_type', 1);
        $productsGoodsReceipt = ProductWarehousing::select('pw_product_id', DB::raw('SUM(pw_total_price) AS total_product_goods_receipt'))->where('pw_type', 2);

        if($request->p_code) {
            $product = Product::where('p_code', $request->p_code)->first();
            $id = '';
            if ($product) {
                $id = $product->id;
            }
            $products = $products->where('pw_product_id', $id);
            $productsGoodsIssue = $productsGoodsIssue->where('pw_product_id', $id);
            $productsGoodsReceipt = $productsGoodsReceipt->where('pw_product_id', $id);
        }

        if($request->supplier_id) {
            $products = $products->where('pw_supplier_id', $request->supplier_id);
            $productsGoodsIssue = $productsGoodsIssue->where('pw_supplier_id', $request->supplier_id);
            $productsGoodsReceipt = $productsGoodsReceipt->where('pw_supplier_id', $request->supplier_id);
        }

        if($request->day) {
            $products = $products->whereDay('created_at', $request->day);
            $productsGoodsIssue = $productsGoodsIssue->whereDay('created_at', $request->day);
            $productsGoodsReceipt = $productsGoodsReceipt->whereDay('created_at', $request->day);
        }

        if($request->month) {
            $products = $products->whereMonth('created_at', $request->month);
            $productsGoodsIssue = $productsGoodsIssue->whereMonth('created_at', $request->month);
            $productsGoodsReceipt = $productsGoodsReceipt->whereMonth('created_at', $request->month);
        }

        if($request->year) {
            $products = $products->whereYear('created_at', $request->year);
            $productsGoodsIssue = $productsGoodsIssue->whereYear('created_at', $request->year);
            $productsGoodsReceipt = $productsGoodsReceipt->whereYear('created_at', $request->year);
        }

        $products = $products->groupBy('pw_product_id');
        $productsGoodsIssue = $productsGoodsIssue->groupBy('pw_product_id');
        $productsGoodsReceipt = $productsGoodsReceipt->groupBy('pw_product_id');

        if (isset($request->export) && $request->export == 'excel') {
            try{
                $products = $products->groupBy('pw_product_id')->get();
                $productsGoodsIssue = $productsGoodsIssue->groupBy('pw_product_id')->get();
                $productsGoodsReceipt = $productsGoodsReceipt->groupBy('pw_product_id')->get();

                return Excel::create('thong-ke-doanh-thu'.Carbon::now(), function($excel) use ($products, $productsGoodsIssue, $productsGoodsReceipt) {

                    $excel->sheet('Thống kê doanh thu', function ($sheet) use ($products, $productsGoodsIssue, $productsGoodsReceipt) {

                        $sheet->cell('A1', function ($cell) {
                            $cell->setValue('STT');
                            $cell->setAlignment('center');
                        });

                        $sheet->cell('B1', function ($cell) {
                            $cell->setValue('Mã hàng hóa');
                            $cell->setAlignment('center');
                        });
                        $sheet->cell('C1', function ($cell) {
                            $cell->setValue('Tên hàng hóa');
                            $cell->setAlignment('center');
                        });

                        $sheet->cell('D1', function ($cell) {
                            $cell->setValue('Đơn vị');
                            $cell->setAlignment('center');
                        });

                        $sheet->cell('E1', function ($cell) {
                            $cell->setValue('Tổng tiền nhập');
                            $cell->setAlignment('center');
                        });

                        $sheet->cell('F1', function ($cell) {
                            $cell->setValue('Tổng tiền nhập');
                            $cell->setAlignment('center');
                        });

                        $sheet->cell('G1', function ($cell) {
                            $cell->setValue('Lợi nhuận');
                            $cell->setAlignment('center');
                        });

                        foreach ($products as $key => $product)
                        {
                            $i= $key+2;
                            $sheet->cell('A'.$i, $key + 1);
                            $sheet->cell('B'.$i, $product->product->p_code);
                            $sheet->cell('C'.$i, $product->product->p_name);
                            $sheet->cell('D'.$i, $product->product->unit->u_name);
                            $total_product_goods_issue = 0;
                            foreach($productsGoodsIssue as $keys => $goodsIssue) {
                                if($goodsIssue->pw_product_id ==  $product->pw_product_id) {
                                    $total_product_goods_issue = $goodsIssue->total_product_goods_issue;
                                    $goods_issue = isset($goodsIssue->total_product_goods_issue) ? $goodsIssue->total_product_goods_issue : 0;
                                }
                            }
                            $sheet->cell('E'.$i, number_format($total_product_goods_issue, 0, ',', ''). "");
                            $total_product_goods_receipt = 0;
                            foreach($productsGoodsReceipt as $ke => $goodsReceipt) {
                                if($goodsReceipt->pw_product_id ==  $product->pw_product_id) {
                                    $total_product_goods_receipt = $goodsReceipt->total_product_goods_receipt;
                                    $goods_receipt = isset($goodsReceipt->total_product_goods_receipt) ? $goodsReceipt->total_product_goods_receipt : 0;
                                }
                            }
                            $sheet->cell('F'.$i, number_format($total_product_goods_receipt, 0, ',', ''). "");
                            $goods_receipt = isset($goods_receipt) ? $goods_receipt : 0;
                            $goods_issue = isset($goods_issue) ? $goods_issue : 0;
                            $revenue = $goods_receipt - $goods_issue;
                            $sheet->cell('G'.$i, $revenue > 0 ? number_format($revenue, 0, ',', '') . '' : 0);
                        }
                    });

                })->download('xlsx');

            }catch (\Exception $exception) {
                Log::error("[Error Export xlsx ]".$exception->getMessage());
                return redirect()->back()->with('danger',"- " .$exception->getMessage());
            }
        }
        $products = $products->paginate(10);
        $productsGoodsIssue = $productsGoodsIssue->paginate(10);
        $productsGoodsReceipt = $productsGoodsReceipt->paginate(10);

        $viewData = [
            'products' => $products,
            'productsGoodsIssue' => $productsGoodsIssue,
            'productsGoodsReceipt' => $productsGoodsReceipt,
            'revenue' => 'active',
        ];

        return view('backend.warehouse.revenue', $viewData);
    }

    public function quantityStatistics(Request $request)
    {
        $products = ProductWarehousing::with([
            'product' => function($product)
            {
                $product->select('*')->with('unit');
            },
        ])->select('pw_product_id');
        $productsGoodsIssue = ProductWarehousing::select('pw_product_id', DB::raw('SUM(pw_total_number) AS total_product_goods_issue'))->where('pw_type', 1);
        $productsGoodsReceipt = ProductWarehousing::select('pw_product_id', DB::raw('SUM(pw_total_number) AS total_product_goods_receipt'))->where('pw_type', 2);

        if($request->p_code) {
            $product = Product::where('p_code', $request->p_code)->first();
            $id = '';
            if ($product) {
                $id = $product->id;
            }
            $products = $products->where('pw_product_id', $id);
            $productsGoodsIssue = $productsGoodsIssue->where('pw_product_id', $id);
            $productsGoodsReceipt = $productsGoodsReceipt->where('pw_product_id', $id);
        }

        if($request->supplier_id) {
            $products = $products->where('pw_supplier_id', $request->supplier_id);
            $productsGoodsIssue = $productsGoodsIssue->where('pw_supplier_id', $request->supplier_id);
            $productsGoodsReceipt = $productsGoodsReceipt->where('pw_supplier_id', $request->supplier_id);
        }

        if($request->day) {
            $products = $products->whereDay('created_at', $request->day);
            $productsGoodsIssue = $productsGoodsIssue->whereDay('created_at', $request->day);
            $productsGoodsReceipt = $productsGoodsReceipt->whereDay('created_at', $request->day);
        }

        if($request->month) {
            $products = $products->whereMonth('created_at', $request->month);
            $productsGoodsIssue = $productsGoodsIssue->whereMonth('created_at', $request->month);
            $productsGoodsReceipt = $productsGoodsReceipt->whereMonth('created_at', $request->month);
        }

        if($request->year) {
            $products = $products->whereYear('created_at', $request->year);
            $productsGoodsIssue = $productsGoodsIssue->whereYear('created_at', $request->year);
            $productsGoodsReceipt = $productsGoodsReceipt->whereYear('created_at', $request->year);
        }

        $products = $products->groupBy('pw_product_id');
        $productsGoodsIssue = $productsGoodsIssue->groupBy('pw_product_id');
        $productsGoodsReceipt = $productsGoodsReceipt->groupBy('pw_product_id');

        if (isset($request->export) && $request->export == 'excel') {
            try{
                $products = $products->groupBy('pw_product_id')->get();
                $productsGoodsIssue = $productsGoodsIssue->groupBy('pw_product_id')->get();
                $productsGoodsReceipt = $productsGoodsReceipt->groupBy('pw_product_id')->get();

                return Excel::create('thong-ke-nhap-xuat-ton'.Carbon::now(), function($excel) use ($products, $productsGoodsIssue, $productsGoodsReceipt) {

                    $excel->sheet('Thống kê nhập xuất tồn đầu', function ($sheet) use ($products, $productsGoodsIssue, $productsGoodsReceipt) {

                        $sheet->cell('A1', function ($cell) {
                            $cell->setValue('STT');
                            $cell->setAlignment('center');
                        });

                        $sheet->cell('B1', function ($cell) {
                            $cell->setValue('Mã hàng hóa');
                            $cell->setAlignment('center');
                        });
                        $sheet->cell('C1', function ($cell) {
                            $cell->setValue('Tên hàng hóa');
                            $cell->setAlignment('center');
                        });

                        $sheet->cell('D1', function ($cell) {
                            $cell->setValue('Đơn vị');
                            $cell->setAlignment('center');
                        });

                        $sheet->cell('E1', function ($cell) {
                            $cell->setValue('Tổng số lượng nhập');
                            $cell->setAlignment('center');
                        });

                        $sheet->cell('F1', function ($cell) {
                            $cell->setValue('Tổng số lượng xuất');
                            $cell->setAlignment('center');
                        });

                        foreach ($products as $key => $product)
                        {
                            $i= $key+2;
                            $sheet->cell('A'.$i, $key + 1);
                            $sheet->cell('B'.$i, $product->product->p_code);
                            $sheet->cell('C'.$i, $product->product->p_name);
                            $sheet->cell('D'.$i, $product->product->unit->u_name);
                            $goods_issue = 0;
                            foreach($productsGoodsIssue as $keys => $goodsIssue) {
                                if($goodsIssue->pw_product_id ==  $product->pw_product_id) {
                                    $goods_issue = isset($goodsIssue->total_product_goods_issue) ? $goodsIssue->total_product_goods_issue : 0;
                                }
                            }
                            $sheet->cell('E'.$i, $goods_issue);
                            $goods_receipt = 0;
                            foreach($productsGoodsReceipt as $ke => $goodsReceipt) {
                                if($goodsReceipt->pw_product_id ==  $product->pw_product_id) {
                                    $goods_receipt = isset($goodsReceipt->total_product_goods_receipt) ? $goodsReceipt->total_product_goods_receipt : 0;
                                }
                            }
                            $sheet->cell('F'.$i, str_replace('-', '', $goods_receipt));
                        }
                    });

                })->download('xlsx');

            }catch (\Exception $exception) {
                Log::error("[Error Export xlsx ]".$exception->getMessage());
                return redirect()->back()->with('danger',"- " .$exception->getMessage());
            }
        }
        $products = $products->paginate(10);
        $productsGoodsIssue = $productsGoodsIssue->paginate(10);
        $productsGoodsReceipt = $productsGoodsReceipt->paginate(10);

        $viewData = [
            'products' => $products,
            'productsGoodsIssue' => $productsGoodsIssue,
            'productsGoodsReceipt' => $productsGoodsReceipt,
            'quantity_statistics' => 'active',
        ];

        return view('backend.warehouse.quantity_statistics', $viewData);
    }
}
