<?php

namespace App\Invoices;

use LaravelDaily\Invoices\Classes\Seller;
use App\Models\ContactUs;

class CompanySeller extends Seller
{
    // Kita buat fungsi statis agar mudah mengambil data ContactUs pertama
    public static function dynamic(): self
    {
        $contact = ContactUs::first();
        
        return new self([
            'name'          => $contact->name ?? 'P PROJECT INDONESIA',
            'address'       => $contact->alamat ?? '',
            'custom_fields' => [
                'Phone' => $contact->nomor_hp ?? '-',
                'Email' => $contact->email ?? '-',
            ],
        ]);
    }
}