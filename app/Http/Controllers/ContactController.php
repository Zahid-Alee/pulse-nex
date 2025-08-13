<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ContactController extends Controller
{



    public function view()
    {
        $contacts = Contact::orderBy('created_at', 'desc')->get()->map(function ($contact) {
            return [
                'id' => $contact->id,
                'name' => $contact->name,
                'email' => $contact->email,
                'subject' => $contact->subject,
                'message' => $contact->message,
                'status' => $contact->status ?? 'new',
                'created_at' => $contact->created_at->format('Y-m-d H:i'),
            ];
        });

        return Inertia::render('contacts/list', [
            'contacts' => $contacts,
        ]);
    }

    /**
     * Store a new contact message.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Store the message in the database
        $contact = Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your message has been sent successfully.',
            'data' => $contact
        ]);
    }


    public function show($id)
    {
        $contact = Contact::findOrFail($id);

        if ($contact->status !== 'replied' && $contact->status !== 'read') {
            $contact->update(['status' => 'read']);
        }

        return response()->json([
            'id' => $contact->id,
            'name' => $contact->name,
            'email' => $contact->email,
            'subject' => $contact->subject,
            'message' => $contact->message,
            'status' => $contact->status,
            'created_at' => $contact->created_at->format('Y-m-d H:i'),
        ]);
    }


    /**
     * Get all contact messages.
     */
    public function index()
    {
        $contacts = Contact::all();
        return response()->json($contacts);
    }

    /**
     * Update contact status as read or replied.
     */
    public function updateStatus(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);

        $status = $request->status;
        if (in_array($status, ['read', 'replied'])) {
            $contact->update([
                'status' => $status,
                'replied_at' => $status == 'replied' ? now() : null
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid status.'
        ], 400);
    }
}
