<!-- Default Themosis page view -->
<div class="wrap">
    <h2>{{ $__page->get('title') }}</h2>

    <?php
        settings_errors();
        // Some needed "globals".
        $firstSection = $__sections[0]->getData();
        $activeTab = isset($_GET['tab']) ? $_GET['tab'] : $firstSection['slug'];
        $args = $__page->get('args');
    ?>

    {{-- Tabs --}}
    @if($__page->hasSections() && $args['tabs'])
        <h2 class="nav-tab-wrapper">
            @foreach($__sections as $section)
                <?php
                    $section = $section->getData();
                    $class = ($activeTab === $section['slug']) ? 'nav-tab-active' : '';
                ?>
                <a href="?page={{ $__page->get('slug') }}&tab={{ $section['slug'] }}" class="nav-tab {{ $class }}">{{ $section['name'] }}</a>
            @endforeach
        </h2>
    @endif
    {{-- End tabs --}}

    {{-- Main content --}}
    <form action="options.php" method="post">

    <?php
        submit_button();

        // Display sections and settings
        // for tabs.
        if($args['tabs']){
            foreach($__sections as $section){
                $section = $section->getData();

                // Display settings regarding the active tab.
                if($activeTab === $section['slug']){
                    settings_fields($section['slug']);
                    do_settings_sections($section['slug']);
                }
            }
        } else {
            // Do not use the tab navigation.
            // Display all sections in one page.
            settings_fields($__page->get('slug'));
            do_settings_sections($__page->get('slug'));
        }

        submit_button();
    ?>

    </form>
    {{-- End main content--}}

</div>