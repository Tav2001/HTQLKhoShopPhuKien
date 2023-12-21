@extends('backend.layouts.app')
@section('content')
    <section class="content-header">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Thống kê số lượng hàng bán ra</h3>
                    <div class="box-header">
                        <form action="" class="form-inline">
                            <div class="col-sm-2" style="padding: 0px">
                                <input type="text" class="form-control" name="p_code" placeholder="Mã hàng hóa ..." value="{{ Request::get('p_code') }}"  style="width: 100%;">
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
                            <div class="col-sm-1" style="padding: 0px; margin-right: 15px">
                                <select name="day" class="form-control" style="width: 100%;">
                                    <option value="">Ngày</option>
                                    @for($i =1; $i <= 31; $i ++)
                                        <option  {{Request::get('day')  == $i ? 'selected=selected' : '' }}  value="{{ $i }}"> Ngày {{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-sm-1" style="padding: 0px; margin-right: 15px">
                                <select name="month" class="form-control" style="width: 100%;">
                                    <option value="">Tháng</option>
                                    @for($i =1; $i <= 12; $i ++)
                                        <option  {{Request::get('month')  == $i ? 'selected=selected' : '' }}  value="{{ $i }}"> Tháng {{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-sm-1" style="padding: 0px; margin-right: 15px">
                                <select name="year" class="form-control" style="width: 100%;">
                                    <?php
                                    $curentYear = date('Y');
                                    $srartYear = intval($curentYear) - 10;
                                    $endYear = intval($curentYear) + 10
                                    ?>
                                    <option value="">Năm</option>
                                    @for($i = $srartYear; $i <= $endYear; $i ++)
                                        <option  {{Request::get('year')  == $i ? 'selected=selected' : '' }}  value="{{ $i }}"> Năm {{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                            <button type="submit" class="btn btn-info col-sm-1"><i class="fa fa-search"></i> Tìm kiếm</button>
                            <div class="col-sm-2">
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
                            <th>Tổng số lượng nhập</th>
                            <th>Tổng số lượng xuất</th>
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
                                                <?php $goods_issue = isset($goodsIssue->total_product_goods_issue) ? $goodsIssue->total_product_goods_issue : 0 ?>
                                                {{ $goods_issue }}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($productsGoodsReceipt as $ke => $goodsReceipt)
                                            @if($goodsReceipt->pw_product_id ==  $product->pw_product_id)
                                                <?php $goods_receipt = isset($goodsReceipt->total_product_goods_receipt) ? $goodsReceipt->total_product_goods_receipt : 0 ?>
                                                {!!  str_replace('-', '', $goods_receipt) !!}
                                            @endif
                                        @endforeach
                                    </td>
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