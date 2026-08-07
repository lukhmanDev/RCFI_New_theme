@props(['status'])

@php
    $style = match($status) {
        'Approved' => 'background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;',
        'Rejected' => 'background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2;',
        'Cancelled' => 'background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;',
        default => 'background: #fffbeb; color: #d97706; border: 1px solid #fef3c7;',
    };
    $icon = match($status) {
        'Approved' => 'bx-check-circle',
        'Rejected' => 'bx-x-circle',
        'Cancelled' => 'bx-block',
        default => 'bx-time-five',
    };
@endphp

<span style="{{ $style }} padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;">
    <i class="bx {{ $icon }}"></i> {{ strtoupper($status) }}
</span>
