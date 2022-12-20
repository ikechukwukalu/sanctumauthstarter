@extends('sanctumauthstarter::layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h3 align="center">
                Loading...
            </h3>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    setInterval(() => {
        window.location.href = "{{ route('auth.redirect') }}"
    }, 500);
</script>
@endsection
