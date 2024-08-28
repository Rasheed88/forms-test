@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ $form->name }}</h2>
    <form id="dynamicForm" method="post">
        @csrf
        @foreach ($form->fields as $field)
            <div class="form-group">
                <label for="{{ $field->name }}">{{ $field->label }}</label>
                @if($field->type == 'input')
                    <input type="text" class="form-control" id="{{ $field->name }}" name="{{ $field->name }}">
                @elseif($field->type == 'textarea')
                    <textarea class="form-control" id="{{ $field->name }}" name="{{ $field->name }}"></textarea>
                @elseif($field->type == 'select')
                    <select class="form-control" id="{{ $field->name }}" name="{{ $field->name }}">
                        @foreach(json_decode($field->options, true) as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        @endforeach
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
@endsection
@section('scripts')
<script>
$(document).ready(function() {
    $('#dynamicForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            type: "POST",
            url: "{{ url('/api/form/submit/'.$form->id) }}",
            data: $(this).serialize(),
            success: function(response) {
                alert("Form submitted successfully!");
            },
            error: function(error) {
                alert("There was an error submitting the form.");
            }
        });
    });
});
</script>
@endsection

