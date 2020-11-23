@extends('layouts.app')

@section('content')
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Import Excel</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form action="{{ route('import-products-post') }}" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <input type="file" name="file" />

                                <button type="submit" class="btn btn-primary">Import</button>
                            </div>
                        </form>

                            @if (isset($errors) && $errors->any())
                                <div class="alert alert-danger">
                                    @foreach ($errors->all() as $error)
                                        {{ $error }}
                                    @endforeach
                                </div>
                            @endif

                            @if (session()->has('failures'))

                                <table class="table table-danger">
                                    <tr>
                                        <th>Row</th>
                                        <th>Attribute</th>
                                        <th>Errors</th>
                                    </tr>

                                    @foreach (session()->get('failures') as $validation)
                                        <tr>
                                            <td>{{ $validation->row() }}</td>
                                            <td>{{ $validation->attribute() }}</td>
                                            <td>
                                                <ul>
                                                    @foreach ($validation->errors() as $e)
                                                        <li>{{ $e }}</li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>

                            @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
