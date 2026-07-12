@extends('layouts.app')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')
@section('page-subtitle', $product->name)

@section('content')

    <div class="table-wrap" style="padding:1.75rem;">
        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.products.form')

            <div style="display:flex; justify-content:flex-end; gap:0.75rem; margin-top:1.5rem; padding-top:1.5rem; border-top:1px solid var(--border);">
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Product</button>
            </div>
        </form>
    </div>

@endsection