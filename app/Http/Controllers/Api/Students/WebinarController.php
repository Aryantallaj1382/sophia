<?php

namespace App\Http\Controllers\Api\Students;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\contact\Price;
use App\Models\GroupClassReservation;
use App\Models\WebinarReservation;
use Illuminate\Http\Request;

class WebinarController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $webinar = WebinarReservation::where('user_id', $user->id)->paginate();
        $webinar->getCollection()->transform(function ($item, $key) {


            return [
                'id' => $item->id,
                'image' => $item->webinar->image,
                'professor_id' => $item->webinar->professor_id,
                'professor_name' => $item->webinar->professor->name,
                'professor_profile' => $item->webinar->professor->user->profile,
                'subgoal' => $item->webinar?->subject?->goal?->title. ' ('. $item->webinar?->subject?->title.')',
                'date' =>$item->webinar->date->format('j F'),
                'time' =>$item->webinar->time->format('H:i'),
            ];
        });

        return api_response($webinar);
    }

    public function new_class($id)
    {
        $class = WebinarReservation::find($id);
        $webinar = $class->webinar;
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
        $group = WebinarReservation::find($id);
        $return = [
            'id' => $group->id,
            'professor_name' => $group->webinar->professor->name,
            'professor_id' => $group->webinar->professor->id,
            'ageGroup' => $group->webinar->ageGroup->title,
            'languageLevel' => $group->webinar->languageLevel->title,
            'platform' => $group->webinar->platform->title,
            'description' => $group->description,
            'subgoal' => $group->webinar->subject->goal->title . ' (' . $group->webinar->subject->title . ')',
            'book' => [
                'name' => $group->webinar?->book?->name,
                'languageLevel' => $group->webinar?->languageLevel?->title,
                'ageGroup' => $group->webinar?->ageGroup->title,
                'image' => $group->webinar?->book?->image,
                'author' => $group->webinar?->book->author,
                'edition' => $group->webinar?->book->edition,
                'topics' => $group->webinar?->book->topics,
            ],
            'reserve_time' => $group->created_at->format('D ,j M Y , H:i'),

        ];
        return api_response($return);
    }

    public function certificate(Request $request,$id)
    {
        $request->validate([
            'en_name' => 'nullable|string',
            'zh_name' => 'nullable|string',
            'in_person' => 'nullable',
            'electronic' => 'nullable',
            'phone' => 'nullable|string',
            'email' => 'nullable|string',
            'city' => 'nullable|string',
            'district' => 'nullable|string',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string',
        ]);
        if ( $request->electronic == 1 ) {
            $type = 'electronic';
        }
        else{
            $type = 'printable';

        }

        Certificate::updateOrCreate
        ([
            'for_id' => $id,
                'for' => 'webinar',
            ]
            ,[
            'user_id' => auth()->id(),
            'for' => 'webinar',
            'for_id' => $id,
            'en_name' => $request->en_name,
            'zh_name' => $request->zh_name,
            'in_person' => $request->in_person,
            'electronic' => $request->electronic,
            'phone' => $request->phone,
            'email' => $request->email,
            'type' => $type,
            'city' => $request->city,
            'district' => $request->district,
            'address' => $request->address,
            'postal_code' => $request->postal_code,
        ]);
        $a = $this->certificate_show($id);
        return api_response($a->original, 'Certificate created');



    }
    public function certificate_show($id)
    {
        $certificate = Certificate::where('for' , 'group')->where('for_id', $id)->first();
        $a  = WebinarReservation::findOrFail($id);
        $class = $a->webinar;
        $return = [
            'id' => $certificate->id ,
            'en_name' => $certificate->en_name ,
            'zh_name' => $certificate->zh_name ,
            'email' => $certificate->email ,
            'phone' => $certificate->phone ,
            'electronic' => $certificate->electronic ,
            'type' => $certificate->type ,
            'city' => $certificate->city ,
            'district' => $certificate->district ,
            'address' => $certificate->address ,
            'postal_code' => $certificate->postal_code,
            'professor' =>  $class->professor->name ,
            'professor_id' =>  $class->professor->id ,
            'profile' =>  $class->professor->user->profile ,
            'ageGroup' =>  $class->ageGroup->title ,
            'languageLevel' =>  $class->languageLevel->title ,
            'platform' =>  $class->platform->title ,
            'subgoal' =>  $class->subject->title ,
            'e_price' => Price::e_price,
            'p_price' => Price::p_price,
            'where' => 'One on One class Certification'


        ];
        return api_response($return);


    }
    public function submit(Request $request,$id)
    {
        $user = auth()->user();
        $balance = $user->wallet->balance;
        $certificate = Certificate::findOrFail($id);
        $type = $certificate->type;
        if ($type == 'electronic') {
            if ($balance < Price::e_price) {
                return api_response([], 'Insufficient price' ,422);
            }
            $certificate->update([
                'status' => 'approved'
            ]);
            return api_response($certificate, 'Certificate submitted');
        }
        if ($type == 'printable') {
            if ($balance < Price::p_price) {
                return api_response([], 'Insufficient price',422);
            }
            $certificate->update([
                'status' => 'approved'
            ]);
            return api_response($certificate, 'Certificate submitted');
        }
        return api_response([], 'Insufficient price',422);


    }

}
