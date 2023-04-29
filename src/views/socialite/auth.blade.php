@extends('sanctumauthstarter::layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ trans('sanctumauthstarter::socialite.google.header') }}</div>

                <div class="card-body">
                    @if(session()->has('fail'))
                        <div class="alert alert-danger m-5">
                        {!! session('fail') !!}
                        </div>
                    @endif
                    <div class="row mb-0">
                        <div class="col-md-6 offset-md-3">
                            <p align="left">Access token will appear here: <span id="accessToken"></span></p>
                            <br/>
                            <p align="left">
                                <label>Click to test Google signup:&nbsp;</label>
                                <button type="click" id="googleSignUp" class="btn btn-primary">
                                    {{ trans('sanctumauthstarter::socialite.google.button') }}
                                </button>
                            </p>
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
        const getUserUUID = () => {
            let userUUID = localStorage.getItem('user_uuid');

            if (!userUUID) {
                userUUID = crypto.randomUUID();
                localStorage.setItem('user_uuid', userUUID);
            }

            console.log('user_uuid created', userUUID);
            return userUUID;
        }

        const removeUserUUID = () => {
            if (localStorage.getItem('user_uuid')) {
                localStorage.removeItem('user_uuid');
            }

            console.log('user_uuid removed');
        }

        const USER_UUID = getUserUUID();
        const TIMEOUT = parseInt("{{ $minutes }}") * 60 * 1000;

        window.Echo.channel(`access.token.socialite.${USER_UUID}`)
        .listen('.Ikechukwukalu\\Sanctumauthstarter\\Events\\SocialiteLogin', (e) => {
            console.log(`payload:`, e);
            document.getElementById('accessToken').innerHTML = e.access_token;
        });

        document.getElementById('googleSignUp').onclick = () => {
            window.open(
                "{{ url('auth/redirect') }}/" + USER_UUID,
                '_blank'
            )
        }

        setTimeout(() => {
            removeUserUUID();
        }, TIMEOUT);
    });
</script>
@endsection
