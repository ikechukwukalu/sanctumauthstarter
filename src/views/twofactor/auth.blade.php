@extends('sanctumauthstarter::layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ trans('sanctumauthstarter::twofactor.header') }}</div>

                <div class="card-body">
                    <div class="row mb-0">
                        <div class="col-md-6 offset-md-3">
                            <p align="left">Access token will appear here: <span id="accessToken"></span></p>
                            <br/>
                            <p align="left">
                                <label>Click to test 2FA signin:&nbsp;</label>
                                <button type="click" id="twoFactorSignIn" class="btn btn-primary">
                                    {{ trans('sanctumauthstarter::twofactor.button') }}
                                </button>
                            </p>
                            <br/>
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
            document.getElementById('accessToken').innerHTML = e.access_token;
        });

        document.getElementById('twoFactorSignIn').onclick = () => {
            window.open(
                "{{ route('twofactor.required', ['email' => Route::input('email'), 'uuid' => Route::input('uuid')]) }}",
                '_blank'
            )
        }
    });
</script>
@endsection
