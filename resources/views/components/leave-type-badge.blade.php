@props(['type'])

@php
    $code = is_object($type) ? ($type->leave_code ?? 'CL') : (string)$type;
    $name = is_object($type) ? ($type->leave_name ?? $code) : $code;

    $badgeMeta = match($code) {
        'CL'  => ['bg' => '#eff6ff', 'color' => '#2563eb', 'border' => '#bfdbfe', 'icon' => 'bx-calendar'],
        'SL'  => ['bg' => '#fef2f2', 'color' => '#ef4444', 'border' => '#fee2e2', 'icon' => 'bx-first-aid'],
        'LSL' => ['bg' => '#f3e8ff', 'color' => '#9333ea', 'border' => '#e9d5ff', 'icon' => 'bx-award'],
        'ML'  => ['bg' => '#fdf2f8', 'color' => '#db2777', 'border' => '#fbcfe8', 'icon' => 'bx-heart'],
        'MTL' => ['bg' => '#fff1f2', 'color' => '#e11d48', 'border' => '#ffe4e6', 'icon' => 'bx-female'],
        'PTL' => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'border' => '#bbf7d0', 'icon' => 'bx-male'],
        'PIL' => ['bg' => '#fffbeb', 'color' => '#d97706', 'border' => '#fef3c7', 'icon' => 'bx-world'],
        'LWP' => ['bg' => '#fef2f2', 'color' => '#dc2626', 'border' => '#fecdd3', 'icon' => 'bx-money-withdraw'],
        'EL'  => ['bg' => '#f0fdfa', 'color' => '#0d9488', 'border' => '#99f6e4', 'icon' => 'bx-briefcase'],
        'BL'  => ['bg' => '#f1f5f9', 'color' => '#475569', 'border' => '#cbd5e1', 'icon' => 'bx-user-x'],
        default => ['bg' => '#f8fafc', 'color' => '#475569', 'border' => '#e2e8f0', 'icon' => 'bx-tag-alt'],
    };
@endphp

<span style="background: {{ $badgeMeta['bg'] }}; color: {{ $badgeMeta['color'] }}; border: 1px solid {{ $badgeMeta['border'] }}; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
    <i class="bx {{ $badgeMeta['icon'] }}"></i> {{ $name }}
</span>
