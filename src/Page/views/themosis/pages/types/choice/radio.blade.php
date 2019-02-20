<div class="th-form-input-choice th-form-input-choice-radio">
    @foreach($field->getOption('choices')->format()->get() as $group => $choices)
        @if(is_array($choices))
            <div class="th-form-input-group">
                <span class="th-group-label">{{ $group }}</span>
                <div class="th-group-choices">
                    @foreach($choices as $label => $choice)
                        <?php
                            $checked = $field->checked(function ($value, $choice) {
                                return ! empty($value) && $value === $choice ? 'checked' : '';
                            }, [$field->getRawValue(), $choice]);
                        ?>
                        <label>
                            <input type="radio" name="{{ $field->getName() }}" value="{{ $choice }}" {{ $checked }}>{{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
        @else
            <?php
                $checked = $field->checked(function ($value, $choice) {
                    return ! empty($value) && $value === $choice ? 'checked' : '';
                }, [$field->getRawValue(), $choices]);
            ?>
            <label>
                <input type="radio" name="{{ $field->getName() }}" value="{{ $choices }}" {{ $checked }}>{{ $group }}
            </label>
        @endif
    @endforeach
</div>