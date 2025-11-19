@props(['showValidation' => true])

@php
    $classesMap = [
        'success' => 'border-emerald-300/50 dark:border-emerald-400/30 bg-emerald-50/70 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-200',
        'error'   => 'border-rose-300/50 dark:border-rose-400/30 bg-rose-50/70 dark:bg-rose-950/30 text-rose-700 dark:text-rose-200',
        'warning' => 'border-amber-300/50 dark:border-amber-400/30 bg-amber-50/70 dark:bg-amber-950/30 text-amber-700 dark:text-amber-200',
        'info'    => 'border-sky-300/50 dark:border-sky-400/30 bg-sky-50/70 dark:bg-sky-950/30 text-sky-700 dark:text-sky-200',
    ];

    $buckets = [
        'success' => [],
        'error'   => [],
        'warning' => [],
        'info'    => [],
    ];

    foreach (array_keys($buckets) as $type) {
        $val = session($type);
        if ($val instanceof \Illuminate\Support\MessageBag) {
            $buckets[$type] = array_merge($buckets[$type], $val->all());
        } elseif (is_array($val)) {
            $buckets[$type] = array_merge($buckets[$type], $val);
        } elseif (!is_null($val)) {
            $buckets[$type][] = (string) $val;
        }
    }

    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
    
    if ($showValidation && $errors->any()) {
        $buckets['error'] = array_merge($buckets['error'], $errors->all());
    }
@endphp

@foreach ($buckets as $type => $messages)
    @if (!empty($messages))
        <div {{ $attributes->merge(['class' => "mb-5 rounded-xl border {$classesMap[$type]} p-3 text-sm"]) }}>
            @if (count($messages) === 1)
                <div class="pr-1">{{ $messages[0] }}</div>
            @else
                <ul class="list-disc pr-5 space-y-1">
                    @foreach ($messages as $m)
                        <li>{{ $m }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
@endforeach