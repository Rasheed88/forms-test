<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormController extends Controller
{
	public function index()
{
    $forms = Form::all();
    return response()->json($forms);
}
public function show($id)
{
    $form = Form::with('fields')->findOrFail($id);
    return response()->json($form);
}


	public function create(Request $request)
	{
   	 $data = $request->validate([
   	     'name' => 'required|string|max:255',
   	     'fields' => 'required|array',
        'fields.*.type' => 'required|string|in:input,textarea,select,radio,checkbox',
        'fields.*.name' => 'required|string|max:255',
        'fields.*.label' => 'required|string|max:255',
        'fields.*.validation_rules' => 'nullable|array',
        'fields.*.send_in_email' => 'required|boolean',
        'fields.*.options' => 'nullable|array'
    ]);

    $form = Form::create(['name' => $data['name']]);

    foreach ($data['fields'] as $field) {
        $form->fields()->create($field);
    }

    return response()->json(['message' => 'Form created successfully', 'form_id' => $form->id]);
}
public function submit(Request $request, $id)
{
    $form = Form::with('fields')->findOrFail($id);

    $validationRules = [];
    foreach ($form->fields as $field) {
        $rules = $field->validation_rules ?? [];
        $validationRules[$field->name] = $rules;
    }

    $data = $request->validate($validationRules);

    // Store the submission data
    $formSubmission = FormSubmission::create([
        'form_id' => $form->id,
        'submission_data' => $data
    ]);

    // Send email if necessary
    $emailData = array_filter($data, function($key) use ($form) {
        return $form->fields->where('name', $key)->first()->send_in_email;
    }, ARRAY_FILTER_USE_KEY);

    if (!empty($emailData)) {
        Mail::to('admin@example.com')->send(new FormSubmissionMail($emailData));
    }

    return response()->json(['message' => 'Form submitted successfully']);
}
public function displayForm($id)
{
    $form = Form::with('fields')->findOrFail($id);
    return view('form', compact('form'));
}

}
