@include('core::_includes.header')

@include('core::_includes.top_header')

    <div id="main" class="layout-row flex">

        <!-- ############ Content START-->
        <div class="flex">
            <!-- ############ Main START-->
            <div class="page-container" id="page-container">
        
                {{ $slot }}

            </div>
        </div>

    </div>

@include('core::components.alerts')

@include('core::_includes.footer')