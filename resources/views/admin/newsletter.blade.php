@extends('layouts.admin')
@section('title','Newsletter') @section('page','Newsletter')
@section('content')
<div class="admin-page-heading"><div><span class="admin-kicker">AUDIENCE</span><h1>Newsletter readers</h1><p>People who asked to hear from MW Bangladesh.</p></div></div>
<div class="admin-panel"><div class="admin-panel-head"><div><h2>Subscribers</h2><p>{{ number_format($subscribers->total()) }} email addresses</p></div></div><div class="table-responsive"><table class="table admin-table align-middle mb-0"><thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Subscribed</th></tr></thead><tbody>@forelse($subscribers as $subscriber)<tr><td>{{ $subscriber->name ?: '—' }}</td><td>{{ $subscriber->email }}</td><td>{{ $subscriber->phone ?: '—' }}</td><td>{{ \Illuminate\Support\Carbon::parse($subscriber->created_at)->format('d M Y') }}</td></tr>@empty<tr><td colspan="4" class="text-center py-5">No newsletter subscribers yet.</td></tr>@endforelse</tbody></table></div><div class="admin-pagination">{{ $subscribers->links('pagination::bootstrap-5') }}</div></div>
@endsection
