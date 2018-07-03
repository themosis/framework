<div class="wrap">
    <h1>{{ $__page->getTitle() }}</h1>
    <form action="options.php" method="post">
        @php
            settings_fields($__page->getSlug());
            do_settings_sections($__page->getSlug());
        @endphp
    </form>
</div>