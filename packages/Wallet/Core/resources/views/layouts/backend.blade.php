@include('core::_includes.header')

@include('core::_includes.top_header')

    <div id="main" class="layout-row flex">
        <!-- ############ LAYOUT START-->

        @yield("main_body")

    </div>

@include('core::_includes.footer')