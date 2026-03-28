@props(['label', 'name', 'type' => 'text'])
<div class="space-y-2">
    <label for="{{ $name }}" class="label">{{ $label }}</label>
    <input type="{{ $type }}" id="{{$name}}" name="{{ $name }}" class="input" {{ $attributes }} @if($type === 'email') value="{{old($name, '')}}" @endif">
    @error($name)
    <p class="error">{{ $message }}</p>
    @enderror
</div>
