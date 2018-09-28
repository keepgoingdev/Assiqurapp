@extends('layouts.registerSale')

@section('content')

<div id="app">
    <registersale/>
</div>

@endsection

@section('scripts')
<script src="https://rawgit.com/cristijora/vue-form-wizard/master/dist/vue-form-wizard.js"></script>
<script src="assets/js/script.min.js"></script>
@endsection

@section('css')
<link rel="stylesheet" href="https://rawgit.com/lykmapipo/themify-icons/master/css/themify-icons.css">
<link rel="stylesheet" href="https://rawgit.com/cristijora/vue-form-wizard/master/dist/vue-form-wizard.min.css">
<link rel="stylesheet" href="assets/css/styles.min.css">

@endsection
