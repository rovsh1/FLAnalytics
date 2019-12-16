<?php
if(strpos(request()->route()->uri, 'ustabor')!==false){
    $prefix = 'ustabor';
}else{
    $prefix = 'fixinglist';
}
?>
@extends('layouts.app')

@section('template_title')
    Showing Analytics
@endsection

@section('content')

<iframe width="100%" height="1000px" src="<?=$iframe?>" frameborder="0" style="border:0" allowfullscreen></iframe>

@endsection

@section('template_scripts')


@endsection