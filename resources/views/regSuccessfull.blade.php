@extends('layouts.registerSale')

@section('content')

<div id="app">
    <div class = "text-center"><img style = "margin-top:30px" class = "register_step_logo" src = "assets/img/Assiqura_Logo.png"/></div>
    <div class = "thankpage-text">Hai firmato il contratto con successo!</div>
    <div class = "thankpage-link-to-new" ><a href = "{{ url('/') }}" style = "text-decoration: none !important;">Nuova registrazione</a></div>
</div>

@endsection

@section('scripts')
<script src="assets/js/script.min.js"></script>
@endsection

@section('css')
<link rel="stylesheet" href="assets/css/main.css">
@endsection

<script src = "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        $.ajax({
            url: "/download_finished_document_background?sale_id={!! $sale_id !!}",
            type: "GET"
        });
    });
</script>
