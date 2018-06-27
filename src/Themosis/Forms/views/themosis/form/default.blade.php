<div class="th-form">
    <form {!! $__form->attributes($__form->getAttributes()) !!}>
        @if('post' === $__form->getAttribute('method') && function_exists('wp_nonce_field'))
            {!! wp_nonce_field($__form->getOptions('nonce_action'), $__form->getOptions('nonce'), $__form->getOptions('referer'), false) !!}
        @endif
        @foreach($__form->repository()->getGroups() as $group)
            <div class="th-form-group">
                @each($group->getView(), $group->getItems(), '__field')
            </div>
        @endforeach
    </form>
</div>