@extends('backend.layouts.app')
@section('content')
    <section class="content-header">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Danh sách khách hàng</h3>

                    <div class="box-tools">
                        <a href="{!! route('get.create.customer') !!}" class="btn btn-success"><i class="fa fa-plus-circle"></i>Thêm mới</a>
                    </div>
                </div>
                <div class="box-header">
                    <div class="pull-left">
                        <form action="" class="form-inline">
                            <input type="text" class="form-control" name="ct_code" placeholder="Tìm mã khách hàng ..." value="{{ Request::get('ct_code') }}">
                            <input type="text" class="form-control" name="ct_name" placeholder="Tìm tên khách hàng ..." value="{{ Request::get('ct_name') }}">
                            <button type="submit" class="btn btn-info"><i class="fa fa-search"></i> Tìm kiếm</button>
                        </form>
                    </div>
                    {{--<div class="pull-right"> Hiển thị: 1 to 30 / Tổng 187653 record </div>--}}
                    <div class="clearfix"></div>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table class="table table-hover table-bordered">
                        <tbody>
                            <tr>
                                <th width="2%" class="text-center">STT</th>
                                <th>Mã khách hàng</th>
                                <th>Tên khách hàng</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Thao tác</th>
                            </tr>
                            @if(!$customers->isEmpty())
                                @foreach($customers as $key => $customer)
                                    <tr>
                                        <td widtd="2%" class="text-center">{{$key + 1}}</td>
                                        <td>{{ $customer->ct_code }}</td>
                                        <td>{{ $customer->ct_name }}</td>
                                        <td>{{ $customer->ct_email }}</td>
                                        <td>{{ $customer->ct_phone }}</td>
                                        <td>
                                            <a href="{{ route('get.update.customer',$customer->id) }}" class="btn btn-xs btn-info"><i class="fa fa-edit"></i></a>
                                            <a href="{{ route('get.delete.customer',$customer->id) }}" class="btn btn-xs btn-info confirm__btn"><i class="fa fa-trash-o"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="box-footer text-right">
                    {{ $customers->appends($query = '')->links() }}
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </section>
@endsection