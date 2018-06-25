<label for="{{ $field->getAttribute('id') }}">{{ $field->getOptions('label') }}</label>
<div class="th-form-input-choice th-form-input-choice-checkbox">
    @foreach($field->getOptions('choices')->format()->get() as $group => $choices)
        @if(is_array($choices))
            <div class="th-form-input-group">
                <span class="th-group-label">{{ $group }}</span>
                <div class="th-group-choices">
                    @foreach($choices as $label => $choice)
                        <label>
                            <input type="checkbox" name="{{ $field->getName() }}[]" value="{{ $choice }}">{{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
        @else
            <label>
                <input type="checkbox" name="{{ $field->getName() }}[]" value="{{ $choices }}">{{ $group }}
            </label>
        @endif
    @endforeach
</div>