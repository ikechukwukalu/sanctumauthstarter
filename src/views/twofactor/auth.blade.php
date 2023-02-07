@extends('sanctumauthstarter::layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ trans('sanctumauthstarter::twofactor.header') }}</div>

                <div class="card-body">
                    <div class="row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <h5 align="center">Access token will be console logged!</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    window.addEventListener('DOMContentLoaded',  () => {
        const USER_UUID = "{{ Route::input('uuid') }}";

        console.log(USER_UUID);

        window.Echo.channel(`access.token.twofactor.${USER_UUID}`)
        .listen('.Ikechukwukalu\\Sanctumauthstarter\\Events\\TwoFactorLogin', (e) => {
            console.log(`payload:`, e);
        });
    });
</script>
@endsection
