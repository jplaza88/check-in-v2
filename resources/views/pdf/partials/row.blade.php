{{-- Collapses when empty, the way DetailRow does on screen. Most check-in
     fields are gated by per-location config and are frequently null, so a
     template that rendered every row would be full of holes. --}}
@if (filled($value))
    <div class="row">
        <dt>{{ $label }}</dt>
        <dd @class(['mono' => $mono ?? false])>{{ $value }}</dd>
    </div>
@endif
