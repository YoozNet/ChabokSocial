@include('layouts.header', ['title' => 'داشبورد'])
<script>
    window.dmz_user = @json(new \App\Http\Resources\UserResource(auth()->user()));
</script>
<div id="root" class="w-full h-screen"></div>

@include('layouts.footer')
