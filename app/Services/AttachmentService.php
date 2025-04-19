<?php

namespace App\Services;

use App\Models\Attachment;

class AttachmentService
{
    public function getAllAttachments($request)
    {
        $q = Attachment::query()->with('animal')->latest();

        if($request->animal_id)
           $q->where('animal_id', $request->animal_id);
        
           if ($request->q) {
                if (is_numeric($request->q))
                      $q->where('id', $request->q);
                else
                    $q->where('name', 'LIKE', '%' . $request->q . '%');
            }

        if($request->with_paginate === '0')
          $attachments = $q->get();
        else
          $attachments = $q->paginate($request->per_page ?? 10);

        return $attachments;
    }
}