<form role="form" action="" method="POST">
    @csrf
    <div class="box-body">
        <div class="form-group {{ $errors->has('ct_code') ? 'has-error' : '' }}">
            <div class="fs-13">
                <label for="company">Mã khách hàng <span class="title-sup">(*)</span></label>
            </div>
            <div class="col-sm-12" style="display: inline-block; padding: 0px;">
                <input class="form-control random_code" id="random_code" oninput="if(value.length>15)value=value.slice(0,15)" name="ct_code" value="{{ old('ct_code', isset($customer) ? $customer->ct_code : '') }}" type="text" placeholder="Mã khách hàng">
            </div>
            @if($errors->has('ct_code'))
                <span class="help-block">{{$errors->first('ct_code')}}</span>
            @endif
            <div class="col-sm-12 default mg-t-10 mg-b-10" style="display: inline-block">
                <button class="btn btn-blue btn-info btn-change btn-change-code" ><i class="fa fa-fw fa-refresh"></i>  Tạo mã</button>
            </div>
        </div>

        <div class="form-group {{ $errors->has('ct_name') ? 'has-error' : '' }}">
            <label for="exampleInputEmail1" class="mg-t-10">Tên khách hàng <sup class="title-sup">(*)</sup></label>
            <input type="text" class="form-control" name="ct_name" value="{{ old('ct_name', isset($customer) ? $customer->ct_name : '') }}" placeholder="Tên khách hàng">
            @if($errors->has('ct_name'))
                <span class="help-block">{{$errors->first('ct_name')}}</span>
            @endif
        </div>

        <div class="form-group {{ $errors->has('ct_email') ? 'has-error' : '' }}">
            <label for="exampleInputEmail1">Email <sup class="title-sup">(*)</sup></label>
            <input type="email" class="form-control" name="ct_email" value="{{ old('ct_email', isset($customer) ? $customer->ct_email : '') }}" placeholder="example@gmail.com">
            @if($errors->has('ct_email'))
                <span class="help-block">{{$errors->first('ct_email')}}</span>
            @endif
        </div>

        <div class="form-group {{ $errors->has('ct_phone') ? 'has-error' : '' }}">
            <label for="exampleInputEmail1">Phone <sup class="title-sup">(*)</sup></label>
            <input type="text" class="form-control" name="ct_phone" value="{{ old('ct_phone', isset($customer) ? $customer->ct_phone : '') }}" placeholder="0xxxxxxxxx">
            @if($errors->has('ct_phone'))
                <span class="help-block">{{$errors->first('ct_phone')}}</span>
            @endif
        </div>
    <!-- /.box-body -->
    <div class="box-footer">
        <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Lưu thông tin</button>
        <a href="{{ route('get.list.customer') }}" class="btn btn-danger"><i class="fa fa-close"></i> Huỷ bỏ</a>
    </div>
</form>