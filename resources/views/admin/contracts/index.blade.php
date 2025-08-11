@extends('layouts.app')
@section('title','Contracts')

@section('content')
<div class="container-fluid page-wrap py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4">Contracts</h1>
    <a href="{{ route('admin.contracts.create') }}" class="btn btn-primary">Add Contract</a>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead><tr>
          <th>#</th><th>Title</th><th>Client</th><th>Total</th><th>Start</th><th>End</th><th></th>
        </tr></thead>
        <tbody>
          @forelse($contracts as $c)
          <tr>
            <td>{{ $c->contract_number ?: '#'.$c->id }}</td>
            <td>{{ $c->title }}</td>
            <td>{{ $c->client_name }}</td>
            <td>{{ '$'.number_format((float)$c->total_cost,2) }}</td>
            <td>{{ optional($c->start_date)->format('d M Y') }}</td>
            <td>{{ optional($c->end_date)->format('d M Y') }}</td>
            <td class="text-end">
              <a href="{{ route('admin.contracts.show',$c) }}" class="btn btn-sm btn-outline-secondary">View</a>
              <a href="{{ route('admin.contracts.edit',$c) }}" class="btn btn-sm btn-outline-primary">Edit</a>
              <form action="{{ route('admin.contracts.destroy',$c) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this contract?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-center py-5">No contracts yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $contracts->links() }}</div>
  </div>
</div>
@endsection
