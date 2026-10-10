@include('core::includes.header')

@include('core::includes.top_header')

    <div id="main" class="layout-row flex">
        <!-- ############ LAYOUT START-->

        @yield("main_body")

    </div>

@include('core::includes.footer')