@extends('sanctumauthstarter::layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <h3>
                                {{ trans('sanctumauthstarter::socialite.user.welcome', ['name' => $user->name]) }}
                            </h3>
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
    if (localStorage.getItem('user_uuid')) {
        localStorage.removeItem('user_uuid');
    }
    window.close();
</script>
@endsection
