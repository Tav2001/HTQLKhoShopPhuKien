<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Model\Customer;

class CustomerController extends Controller
{
    public function __construct()
    {
        view()->share([
            'customer_menu' => 'active'
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $customers = Customer::select('*');
        if ($request->ct_code) {
            $customers = $customers->where('ct_code', $request->ct_code);
        }

        if ($request->ct_name) {
            $customers = $customers->where('ct_name','like','%'.$request->ct_name.'%');
        }

        $customers = $customers->orderBy('id', 'DESC')->paginate(20);

        return view('backend.customer.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('backend.customer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        //
        if ($this->processCreateOrUpdate($request)) {
            return redirect()->back()->with('success','[Success] Thêm mới thành công.');
        }

        return redirect()->back()->with('danger', '[Error] Đã xảy ra lỗi không thể lưu dữ liệu.');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $customer = Customer::find($id);
        if (!$customer) {
            return redirect()->route('get.list.customer')->with('danger', '[Error] Đã xảy ra lỗi dữ liệu không tồn tại.');
        }
        return view('backend.customer.update', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerRequest $request, $id)
    {
        //
        if ($this->processCreateOrUpdate($request, $id)) {
            return redirect()->back()->with('success','[Success] Chỉnh sửa thành công.');
        }
        return redirect()->back()->with('danger', '[Error] Đã xảy ra lỗi không thể lưu dữ liệu.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        //
        $customer = Customer::findOrFail($id);
        if (!$customer) {
            return redirect()->route('get.list.suppliers')->with('danger', '[Error] Đã xảy ra lỗi không thể xóa dữ liệu.');
        }
        try {
            $customer->delete();
            return redirect()->back()->with('success','[Success] Xóa thành công.');
        } catch (\Exception $exception) {
            Log::error($exception);
            return redirect()->back()->with('danger', '[Error] Đã xảy ra lỗi không thể xóa dữ liệu.');
        }
    }

    /**
     * @param $request
     * @param null $id
     * @return bool
     */
    public function processCreateOrUpdate($request, $id = NULL)
    {
        $data = $request->except('_token');
        $data['updated_at'] = now();

        DB::beginTransaction();
        try {
            if ($id === NULL) {
                $data['created_at'] = now();
                Customer::insert($data);
            } else {
                Customer::find($id)->update($data);
            }
            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return false;
        }
    }
}
