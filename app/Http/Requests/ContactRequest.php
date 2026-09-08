<?php

namespace App\Http\Requests;

use App\Enums\ContactSubject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactRequest extends FormRequest
{
    /** The field a human never sees; anything in it came from a bot. */
    public const HONEYPOT = 'company_website';

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            // dns check would block a valid address behind a slow resolver, and this
            // form is throttled anyway — rfc,strict is enough to stop typos.
            'email' => ['required', 'string', 'email:rfc,strict', 'max:150'],
            'phone' => ['nullable', 'string', 'max:40', 'regex:/^[\d\s+().\-]{6,40}$/'],
            'subject' => ['required', Rule::enum(ContactSubject::class)],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            self::HONEYPOT => ['prohibited'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'name' => __('contact.form.name'),
            'email' => __('contact.form.email'),
            'phone' => __('contact.form.phone'),
            'subject' => __('contact.form.subject'),
            'message' => __('contact.form.message'),
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            self::HONEYPOT.'.prohibited' => __('contact.errors.rejected'),
            'phone.regex' => __('contact.errors.phone'),
        ];
    }
}
