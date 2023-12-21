<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
            'ct_name' => 'required | max:255 ',
            'ct_code' => 'required| max:15 | unique:customers,ct_code,'.$this->id,
            'ct_email' => 'required| email| unique:customers,ct_email,'.$this->id,
            'ct_phone' => 'required | max:10 ',
            //'ct_status' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'ct_name.required' => 'Vui lòng nhập vào tên nhà cung cấp',
            'ct_name.max' => 'Tên nhà cung cấp vượt quá số ký tự cho phép',
            'ct_code.required' => 'Vui lòng nhập vào mã nhà cung cấp',
            'ct_code.max' => 'Mã nhà cung cấp vượt quá số ký tự cho phép',
            'ct_code.unique' => 'Mã nhà cung cấp đã bị trùng',
            'ct_email.required' => 'Vui lòng nhập vào email',
            'ct_email.email' => 'Vui lòng nhập vào email đúng định dạng',
            'ct_email.unique' => 'Địa chỉ email đã bị trùng',
            'ct_phone.required' => 'Vui lòng nhập vào số điện thoại',
            'ct_phone.max' => 'Số điện thoại vượt quá số ký tự cho phép',
            //'ct_status.required' => 'Vui lòng chọn trạng thái nhà cung cấp',
        ];
    }
}
