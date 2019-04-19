<div class="th-form">
    @if($__form->getOption('tags'))
        <form {!! $__form->attributes($__form->getAttributes()) !!}>
    @endif
        @if('post' === $__form->getAttribute('method') && function_exists('wp_nonce_field'))
            {!! wp_nonce_field($__form->getOption('nonce_action'), $__form->getOption('nonce'), $__form->getOption('referer'), false) !!}
        @endif
        @if($__form->getOption('csrf') && function_exists('csrf_field'))
            {!! csrf_field() !!}
        @endif
        @foreach($__form->repository()->getGroups() as $group)
            <div class="{{ sprintf('th-form-group %s', sprintf('th-group-%s', $group->getId())) }}">
                @each($group->getView(true), $group->getItems(), '__field')
            </div>
        @endforeach
    @if($__form->getOption('tags'))
        </form>
    @endif
</div>