<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'p_category_id' => 'required',
            'p_name' => 'required | max:255 ',
            'p_code' => 'required| max:15 | unique:products,p_code,'.$this->id,
            //'p_status' => 'required',
            'images' => ['nullable','image', 'mimes:jpeg,jpg,png', 'max:3072'],
        ];
    }

    public function messages()
    {
        return [
            'p_category_id.required' => 'Vui lòng chọn loại hàng hóa',
            'p_name.required' => 'Vui lòng nhập tên hàng hóa',
            'p_name.max' => 'Tên hàng hóa vượt quá số ký tự cho phép',
            'p_code.required' => 'Vui lòng nhập vào mã hàng hóa',
            'p_code.max' => 'Mã hàng hóa vượt quá số ký tự cho phép',
            'p_code.unique' => 'Mã hàng hóa đã bị trùng',
           // 'p_status.required' => 'Vui lòng chọn trạng thái hàng hóa',
            'images.image' => 'Vui lòng nhập vào định dạng file ảnh',
            'images.max' => 'Dung lượng file vượt quá ',
        ];
    }
}
