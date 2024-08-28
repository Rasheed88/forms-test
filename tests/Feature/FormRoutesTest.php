<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Form;
use App\Models\Field;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\FormSubmissionMail;

class FormRoutesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_can_create_a_form()
    {
        $formData = [
            'name' => 'Contact Us'
        ];

        $response = $this->postJson('/api/forms', $formData);

        $response->assertStatus(201)
                 ->assertJson([
                     'name' => 'Contact Us',
                 ]);

        $this->assertDatabaseHas('forms', [
            'name' => 'Contact Us',
        ]);
    }

    /** @test */
    public function it_can_list_all_forms()
    {
        $form1 = Form::factory()->create(['name' => 'Form 1']);
        $form2 = Form::factory()->create(['name' => 'Form 2']);

        $response = $this->getJson('/api/forms');

        $response->assertStatus(200)
                 ->assertJson([
                     ['name' => 'Form 1'],
                     ['name' => 'Form 2'],
                 ]);
    }

    /** @test */
    public function it_can_view_a_single_form_with_its_fields()
    {
        $form = Form::factory()->create(['name' => 'Contact Us']);
        $field = Field::factory()->create([
            'form_id' => $form->id,
            'name' => 'email',
            'label' => 'Email Address',
            'type' => 'email'
        ]);

        $response = $this->getJson("/api/forms/{$form->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'name' => 'Contact Us',
                     'fields' => [
                         [
                             'name' => 'email',
                             'label' => 'Email Address',
                             'type' => 'email',
                         ]
                     ],
                 ]);
    }

    /** @test */
    public function it_can_submit_a_form_with_valid_data()
    {
        Mail::fake();

        $form = Form::factory()->create(['name' => 'Contact Us']);
        $field = Field::factory()->create([
            'form_id' => $form->id,
            'name' => 'email',
            'label' => 'Email Address',
            'type' => 'email',
            'validation_rules' => json_encode(['required', 'email']),
            'send_in_email' => true
        ]);

        $submissionData = [
            'email' => 'user@example.com'
        ];

        $response = $this->postJson("/api/forms/{$form->id}/submit", $submissionData);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Form submitted successfully!',
                 ]);

        $this->assertDatabaseHas('form_submissions', [
            'form_id' => $form->id,
            'data' => json_encode($submissionData),
        ]);

        Mail::assertSent(FormSubmissionMail::class, function ($mail) use ($submissionData) {
            return $mail->formSubmission['email'] === $submissionData['email'];
        });
    }

    /** @test */
    public function it_validates_form_submission_data()
    {
        $form = Form::factory()->create(['name' => 'Contact Us']);
        $field = Field::factory()->create([
            'form_id' => $form->id,
            'name' => 'email',
            'label' => 'Email Address',
            'type' => 'email',
            'validation_rules' => json_encode(['required', 'email']),
            'send_in_email' => true
        ]);

        $submissionData = [
            'email' => 'invalid-email'
        ];

        $response = $this->postJson("/api/forms/{$form->id}/submit", $submissionData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('email');
    }
}

