@extends('sanctumauthstarter::layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Google Sign Up') }}</div>

                <div class="card-body">
                    <div class="row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="click" id="googleSignUp" class="btn btn-primary">
                                {{ __('Google Sign Up') }}
                            </button>
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

            return userUUID;
        }

        const USER_UUID = getUserUUID();

        window.Echo.channel(`access.token.${USER_UUID}`)
        .listen('.Ikechukwukalu\\Sanctumauthstarter\\Events\\SocialiteLogin', (e) => {
            console.log(e);
        });

        document.getElementById('googleSignUp').onclick = () => {
            window.open(
                "{{ url('set/cookie') }}/" + USER_UUID,
                '_blank'
            )
        }
    });
</script>
@endsection
