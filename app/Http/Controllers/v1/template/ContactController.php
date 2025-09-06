<?php

namespace App\Http\Controllers\v1\template;

use App\Models\Email;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ContactController extends Controller
{
    public function contactsExist(Request $request)
    {
        $request->validate(
            [
                "email" => "required",
                "contact" => "required",
            ]
        );
        $email = Email::where("value", '=', $request->email)->first();
        $contact = Contact::where("value", '=', $request->contact)->first();
        // Check if both models are found
        $emailExists = $email !== null;
        $contactExists = $contact !== null;

        return response()->json([
            'email_found' => $emailExists,
            'contact_found' => $contactExists,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
