<!-- Default Themosis page view -->
<div class="wrap">
    <h2>{{ $__page->get('title') }}</h2>

    <?php settings_errors(); ?>

    {{-- Tabs --}}
    @if($__page->hasSections() && $__page->get('args')['tabs'])
        <?php
            $firstSection = $__sections[0]->getData();
            $activeTab = isset($_GET['tab']) ? $_GET['tab'] : $firstSection['slug'];
        ?>
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
</div>