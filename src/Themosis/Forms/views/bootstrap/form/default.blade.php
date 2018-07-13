<div class="th-form-bootstrap">
    @if($__form->getOptions('tags'))
        <form {!! $__form->attributes($__form->getAttributes()) !!}>
    @endif
        @if('post' === $__form->getAttribute('method') && function_exists('wp_nonce_field'))
            {!! wp_nonce_field($__form->getOptions('nonce_action'), $__form->getOptions('nonce'), $__form->getOptions('referer'), false) !!}
        @endif
        @foreach($__form->repository()->getGroups() as $group)
            <div class="th-form-group th-form-group-bootstrap">
                @each($group->getView(true), $group->getItems(), '__field')
            </div>
        @endforeach
    @if($__form->getOptions('tags'))
        </form>
    @endif
</div>