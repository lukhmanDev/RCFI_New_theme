<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\LeaveRequest;

class RejectLeaveRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        $leaveRequest = LeaveRequest::find($this->route('leaveRequest')?->id ?? $this->route('leaveRequest'));
        return $leaveRequest && $this->user()->can('reject', $leaveRequest);
    }

    public function rules(): array
    {
        return [
            'remarks' => 'required|string|max:1000',
        ];
    }
}
