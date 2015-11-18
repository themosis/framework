<!-- Default Themosis page view -->
<div class="wrap">
    <h1>{{ $__page->get('title') }}</h1>

    <?php
    settings_errors();

    // Display, handle tab navigation.
    $__page->renderTabs();
    ?>

    <form action="options.php" method="post" class="themosis-core-page">
        <?php
        // Display sections and settings.
        $__page->renderSettings();

        submit_button();
        ?>
    </form>

</div>