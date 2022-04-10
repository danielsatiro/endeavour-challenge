@php
    $inputName = $inputName ?? 'image';
@endphp

<div class="form-group {{ $errors->has('$inputName') ? ' has-error has-danger ' : '' }}" id="imageDiv">
    <label for="{{ $inputName }}" class="control-label">{{ $label }}</label>

    <div id="{{$inputName}}" class="dropzone" class="wrap-input">
        <!--<input type="file" class="upload inputFile form-control{{ $errors->has($inputName) ? ' is-invalid' : '' }}" accept="{{ $accept ?? '.json' }}" aria-describedby="nameHelp" {{ !empty($multiple) ? 'multiple' : '' }}>-->
        <input type="hidden" name="{{ $inputName }}">
        <input type="hidden" name="{{ $inputName }}_extension">
    </div>

    <div class="help-block with-errors">
        @if ($errors->has('$inputName'))
            {{ $errors->first('$inputName') }}
        @endif
    </div>
</div>
