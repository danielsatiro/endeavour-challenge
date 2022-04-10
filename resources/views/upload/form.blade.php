@extends('layouts.app')

@section('content')

<main class="dashboard">

    <div class="wrap-dashboard">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="wrap-title">
                        <h1 class="page-title">{{ __($title) }}</h1>
                    </div>
                    <div class="content">
                        <div class="card">
                            <div class="card-body">
                                @if (session('status'))
                                    <div class="alert alert-success" role="alert">
                                        {!! session('status') !!}
                                    </div>
                                @endif
                                <form role="form" method="POST" action="{{ $action }}" data-toggle="validator" enctype="multipart/form-data" class="small">
                                    @if (isset($method) && $method == 'PUT')
                                        @method('PUT')
                                    @endif
                                    @csrf

                                    @include('includes._media', ['inputName' => 'upload_file', 'label' => 'File to process', 'accept' => '.json,.xml,.csv', 'required' => true])

                                    <div class="form-group no-bot">
                                        <button type="submit" class="btn btn-primary secondary">{{ __($buttonTitle) }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="{{ asset('assets/dropzone/js/dropzone-min.min.js') }}"></script>

<script>
    let dropzone_upload_file = new Dropzone("div#upload_file",
    {
        url: "{{ route('upload') }}",
        chunking: true,
        maxFiles: 1,
        maxFilesize: 10000,
        acceptedFiles: '.json,.xml,.csv',
        init: function () {
            this.on("complete", function (file) {
            response = JSON.parse(file.xhr.response);
            $('#'+this.element.dropzone.element.id).find('input[name=upload_file]').val(response.path+response.name);
            $('#'+this.element.dropzone.element.id).find('input[name=upload_file_extension]').val(response.extension);
            });
            this.on("sending", function (file, xhr, formData) {
            formData.append("_token", "{{ csrf_token() }}");
            });
        }
    });

</script>
@endpush