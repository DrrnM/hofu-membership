@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Daftar Member</h2>
    <table border="1" cellpadding="10">
        <tr>
            <th>Nama</th>
            <th>No HP</th>
            <th>Poin</th>
        </tr>
        @foreach($members as $member)
        <tr>
            <td>{{ $member->nama }}</td>
            <td>{{ $member->no_hp }}</td>
            <td>{{ $member->poin }}</td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
