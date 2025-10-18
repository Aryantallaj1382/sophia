<?php

namespace App\Http\Controllers\Api\Professor;

use App\Models\Certificate;
use App\Models\Webinar;
use App\Models\WebinarReservation;

class ProfessorWebinarController
{
    public function index()
    {
        $user = auth()->user()->professor;
        $webinar = Webinar::where('professor_id', $user->id)->paginate();
        $webinar->getCollection()->transform(function ($item, $key) {


            return [
                'id' => $item->id,
                'image' => $item->image,
                'professor_id' => $item->professor_id,
                'professor_name' => $item->professor->name,
                'professor_profile' => $item->professor->user->profile,
                'subgoal' => $item?->subject?->goal?->title. ' ('. $item?->subject?->title.')',
                'date' =>$item->date->format('j F'),
                'time' =>$item->time->format('H:i'),
            ];
        });

        return api_response($webinar);
    }

    public function new_class($id)
    {
        $webinar = Webinar::find($id);
        $certificate = Certificate::where('for' , 'webinar')->where('for_id', $id)->exists();
        $certificate_file = Certificate::where('for' , 'webinar')->where('for_id', $id)->first();
        $a = [
            'link' =>$webinar->class_link ?? null,
            'professor_id' => $webinar->professor->user->id ?? null,
            'profile' => $webinar->professor->user->profile ?? null,
            'subgoal' => $webinar->subject->goal->title . ' (' . $webinar->subject->title . ')',
            'date' => $webinar->date->format('j F') ,
            'time' =>$webinar->time->format('H:i') ,
            'has_certificate' => $certificate,
            'certificate_file' => $certificate_file->file ?? null,
        ];

        return api_response($a);
    }
    public function information($id)
    {
        $webinar = Webinar::find($id);
        $return = [
            'id' => $webinar->id,
            'professor_name' => $webinar->professor->name,
            'professor_id' => $webinar->professor->id,
            'ageGroup' => $webinar->ageGroup->title,
            'languageLevel' => $webinar->languageLevel->title,
            'platform' => $webinar->platform->title,
            'description' => $webinar->description,
            'subgoal' => $webinar->subject->goal->title . ' (' . $webinar->subject->title . ')',
            'book' => [
                'name' => $webinar?->book?->name,
                'languageLevel' => $webinar?->languageLevel?->title,
                'ageGroup' => $webinar?->ageGroup->title,
                'image' => $webinar?->book?->image,
                'author' => $webinar?->book->author,
                'edition' => $webinar?->book->edition,
                'topics' => $webinar?->book->topics,
            ],
            'reserve_time' => $webinar->created_at?->format('D ,j M Y , H:i'),

        ];
        return api_response($return);
    }
}
