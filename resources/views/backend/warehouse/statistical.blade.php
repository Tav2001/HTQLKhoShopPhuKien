@extends('backend.layouts.app')
@section('content')
    <section class="content-header">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Thống kê dữ liệu kho hàng</h3>
                    <div class="box-header">
                        <form action="" class="form-inline">
                            <div class="col-sm-2" style="padding: 0px">
                                <input type="text" class="form-control" name="p_code" placeholder="Tìm mã hàng hóa ..." value="{{ Request::get('p_code') }}"  style="width: 100%;">
                            </div>
                            <div class="col-sm-3">
                                <select name="supplier_id" id="" class="form-control select" style="width: 100%">
                                    <option value="">Chọn nhà cung cấp</option>
                                    @if($suppliers)
                                        @foreach($suppliers as $key => $supplier)
                                            <option {{ $supplier->id == Request::get('supplier_id') ? 'selected=selected' : '' }} value="{{ $supplier->id }}">{{ $supplier->s_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <!-- <div class="col-sm-2">
                                <input type="date" class="form-control" name="start_day" value="{{ Request::get('start_day') }}" style="width: 100%;">
                            </div>
                            <div class="col-sm-2">
                                <input type="date" class="form-control" name="end_day"  value="{{ Request::get('end_day') }}"  style="width: 100%;">
                            </div> -->
                            <button type="submit" class="btn btn-info"><i class="fa fa-search"></i> Tìm kiếm</button>
                            <div class="col-sm-2" style="float: right">
                                <a href="@php echo  strstr(url()->full(),'?') == true ? url()->full() . '&export=excel' : url()->full() . '?export=excel' @endphp" class="btn btn-block btn-success"><i class="fa fa-fw fa-file-excel-o"></i>  Export excel</a>
                            </div>
                        </form>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table class="table table-hover table-bordered">
                        <tbody>
                        <tr>
                            <th width="2%" class="text-center">STT</th>
                            <th>Ảnh</th>
                            <th>Mã hàng hóa</th>
                            <th>Tên hàng hóa</th>
                            <th>Đơn vị</th>
                            <th>Số lượng nhập</th>
                            <th>Số lượng xuất</th>
                            <th>Tồn kho</th>
                        </tr>
                        @if(!$products->isEmpty())
                            @foreach($products as $key => $product)
                                <tr>
                                    <td width="2%" class="text-center">{{$key + 1}}</td>
                                    <td><img src="{!! !empty($product->product->p_images) ? asset('uploads/products/'.$product->product->p_images) : asset('admin/images/no-image.png') !!}" alt="" width="100" height="100"></td>
                                    <td>{{ $product->product->p_code }}</td>
                                    <td>
                                        <p class="text-type-doccument" data-toggle="tooltip" data-placement="top" title="{{ $product->product->p_name }}" >
                                                <span class="content-space" title="{{ $product->product->p_name }}">
                                                    {{ $product->product->p_name }}
                                                </span>
                                        </p>
                                    </td>
                                    <td>{{ $product->product->unit->u_name }}</td>
                                    <td>
                                        @foreach($productsGoodsIssue as $keys => $goodsIssue)
                                            @if($goodsIssue->pw_product_id ==  $product->pw_product_id)
                                                {!! $goodsIssue->total_product_goods_issue !!}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($productsGoodsReceipt as $ke => $goodsReceipt)
                                            @if($goodsReceipt->pw_product_id ==  $product->pw_product_id)
                                                {!!  str_replace('-', '', $goodsReceipt->total_product_goods_receipt) !!}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>{{ $product->total_product }}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="box-footer text-right">
                    {{ $products->appends($query = '')->links() }}
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </section>
@endsection