@if(!empty($field->error()) && $field->getOptions('errors'))
    <ul class="th-errors-list th-errors-list-text">
        @foreach($__field->error() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif