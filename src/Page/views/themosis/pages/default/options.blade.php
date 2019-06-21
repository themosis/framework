<div class="wrap">
    <h1>{{ $__page->getTitle() }}</h1>
    @php
        if ('options-general.php' !== $__page->getParent()) {
            settings_errors($__page->getSlug());
        }
    @endphp
    <form action="options.php" method="post">
        @php
            settings_fields($__page->getSlug());
            do_settings_sections($__page->getSlug());
            submit_button();
        @endphp
    </form>
</div>