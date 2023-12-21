<tr location="{{ $location }}" class="product_{{ $location }}">
    <td>
        <select name="pw_product_id[{{ $location }}]" id="" class="form-control select pw_product_id" required>
            <option value="">Chọn hàng hóa</option>
            @if($products)
                @foreach($products as $key => $product)
                    <option value="{{ $product->id }}">{{ $product->p_name }}</option>
                @endforeach
            @endif
        </select>
    </td>
    @if (!$type)
        <td>
            <select name="pw_customer_id[{{ $location }}]" id="" class="form-control select" required>
                <option value="">Chọn khách hàng</option>
                @if($customers)
                    @foreach($customers as $key => $customer)
                        <option value="{{ $customer->id }}">{{ $customer->ct_name }}</option>
                    @endforeach
                @endif
            </select>
        </td>
    @else
        <td>
            <select name="pw_supplier_id[{{ $location }}]" id="" class="form-control select" required>
                <option value="">Chọn nhà cung cấp</option>
                @if($suppliers)
                    @foreach($suppliers as $key => $supplier)
                        <option value="{{ $supplier->id }}" title="{{ $supplier->s_name }}"> {{ $supplier->s_name }}</option>
                    @endforeach
                @endif
            </select>
        </td>
    @endif

    <td>
        <input type="number" class="form-control pw_total_number" name="pw_total_number[{{ $location }}]" value="" placeholder="Số lượng hàng hóa" required>
    </td>
    <td>
        <span style="display:none"><input type="radio" value="1" name="pw_active_price[{{ $location }}]" checked class="pw_active_price"> Giá nhập </span>
        @if (!$type)
        <input type="radio" value="2" name="pw_active_price[{{ $location }}]" class="pw_active_price"> Giá bán lẻ
        <input type="radio" value="3" name="pw_active_price[{{ $location }}]" class="pw_active_price"> Giá bán sỉ
        @endif
        <input type="radio" value="4" name="pw_active_price[{{ $location }}]" class="pw_active_price"> Giá khác
        <input type="number" class="form-control pw_custom_price" name="pw_custom_price[{{ $location }}]" value="" placeholder="Giá hàng hóa" style="display: none">
    </td>
    
    <td>
        <p class="pw_total_price"></p>
        <input type="hidden" class="form-control pw_total_price" name="pw_total_price[{{ $location }}]" value="">
    </td>
    <td><a class="btn btn-xs btn-info mg-t-5 delete-item-product"><i class="fa fa-trash-o"></i></a></td>
</tr>
<script>
    $(document).ready(function () {
        $('.select').select2();
    });
</script>