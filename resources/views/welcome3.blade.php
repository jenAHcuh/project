<?php
$judul = "ini judul from com";
?>
<x-halaman-layout :title="$judul">
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatibus incidunt aspernatur 
        sapiente consectetur. Velit earum ad hic vel perspiciatis. Minus?</p>

    <x-slot name="tanggal">17 Agus 2024</x-slot>
    <x-slot name="penulis">weh</x-slot>   
</x-halaman-layout>
