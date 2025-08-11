@extends('layouts.app')
@section('title','Invoices')
@section('content')
<div class="container-fluid page-wrap py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4">Invoices</h1>
    <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary">Add Invoice</a>
  </div>
  <div class="card">
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead><tr>
          <th>#</th><th>Client</th><th>Project</th><th>Total</th><th>Created</th><th></th>
        </tr></thead>
        <tbody>
          @forelse($invoices as $inv)
            <tr>
              <td>{{ $inv->invoice_number ?: '#'.$inv->id }}</td>
              <td>{{ $inv->client_name }}</td>
              <td>{{ $inv->project_title }}</td>
              <td>{{ '$'.number_format((float)$inv->total,2) }}</td>
              <td>{{ optional($inv->created_at)->format('d M Y') }}</td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.invoices.show',$inv) }}">View</a>
                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.invoices.edit',$inv) }}">Edit</a>
                <form action="{{ route('admin.invoices.destroy',$inv) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this invoice?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center py-5">No invoices yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $invoices->links() }}</div>
  </div>
</div>
@endsection
