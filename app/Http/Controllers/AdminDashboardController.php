<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\DiscountCode;
use App\Models\shop\Comment;
use App\Models\shop\EducationalProduct;
use App\Models\shop\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


use App\Models\ContactUs;
use App\Models\News;
use App\Models\Blog;
use App\Models\TicketMessage;
use App\Models\Ticket;

////تاید استاد
use App\Models\Professors\Professor;
use App\Models\Professors\Attachment;
use App\Models\Professors\Notification;
use App\Models\Languages;
use App\Models\shop\Accent;
use App\Models\shop\Product;
use App\Models\shop\Category;
use App\Models\shop\Publication;
use App\Models\shop\ProductType;
use App\Models\Professors\LearningSubgoal;

use App\Models\Professors\LanguageLevel;

use App\Models\Professors\GroupClass;
use App\Models\TeachingType;
//Professor
use App\Models\Professors\Gender;
use App\Models\AgeGroup;
use App\Models\Professors\AgeGroupLanguageLevel;
use App\Models\shop\Subjects;
use App\Models\Professors\ProfessorAddress;
use App\Models\Professors\Offline;
use App\Models\shop\Image;


use App\Models\Consultation;

////

use App\Models\Exam_question\QuestionVariant;
use App\Models\Exam_question\ExamPart;
use App\Models\Exam_question\Skill;
use App\Models\Exam_question\Exam;
use App\Models\Exam_question\ExamStudent;
use App\Models\Exam_question\MediaQuestion;
use App\Models\Exam_question\Question;
use App\Models\Exam_question\QuestionType;
use App\Models\Exam_question\Option;


//////////

use App\Models\shop\Slaider;
use App\Models\shop\Baner;
use App\Models\shop\VideoBaner;


use App\Models\order\OrderItem;
use App\Models\order\Order;


use App\Models\Seller\ProductSeller;

use App\Models\Financial\Transaction;


///عکس
use App\Models\shop\Cover;
use App\Models\shop\SampleImages;
use App\Models\shop\Story;

use App\Models\shop\Videos;

use App\Models\SystemSetting;

use App\Models\ExamPayment\OrderExamPayment;



///
use App\Models\Professors\WorkShop;
////
use App\Models\Financial\Wallet;

use App\Models\Professors\Webinar;

class AdminDashboardController extends Controller
{


    public function xadmin()
    {
        return view('admin.adminhome');
    }



///////////
//////////
/////////

    public function tayid_asatid()
    {
        $professors = Professor::with(['attachments' => function ($query) {
            $query->select('id', 'file_path', 'category', 'attachable_id');
        }])->get();

        return view('admin.tayid_asatid', compact('professors'));
    }

    public function approveProfessor(Request $request, $id)
    {
        $professor = Professor::find($id);

        if (!$professor) {
            return redirect()->back()->with('error', 'استاد پیدا نشد.');
        }

        $professor->status = 'approved';
        $professor->save();

        return redirect()->back()->with('success', 'استاد با موفقیت تایید شد.');
    }

    public function rejectProfessor(Request $request, $id)
    {
        // اعتبارسنجی ورودی
        $validatedData = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $text = $validatedData['reason'];

        $professor = Professor::find($id);

        if (!$professor) {
            return redirect()->back()->with('error', 'استاد پیدا نشد.');
        }

        $professor->status = 'rejected';
        $professor->save();

        Notification::create([
            'text' => $text,
            'professors_id' => $professor->id,
            'type' => '/admin',
        ]);

        return redirect()->back()->with('success', 'استاد با موفقیت رد شد.');
    }


    public function updateProfessor(Request $request, Professor $professor)
    {
        // اعتبارسنجی داده‌ها
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'professors_gender' => 'required|string',
            'identification_code' => 'nullable|string|max:50',
            'is_student' => 'required|boolean',
            'webinars_count' => 'nullable|integer',
            'group_classes_count' => 'nullable|integer',
            'private_classes_count' => 'nullable|integer',
            'students_count' => 'nullable|integer',
            'about_text' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'country_id' => 'nullable|string|max:255',
            'province_id' => 'nullable|string|max:255',
            'city_id' => 'nullable|string|max:255',
            'is_inside' => 'required|boolean',
            'age' => 'nullable|integer',
            'place_of_birth' => 'nullable|string|max:255',
            'national_code' => 'nullable|string|max:20',
        ]);

        // بروزرسانی رکورد استاد
        $professor->first_name = $request->input('first_name');
        $professor->last_name = $request->input('last_name');
        $professor->mobile = $request->input('mobile');
        $professor->email = $request->input('email');
        $professor->professors_gender = $request->input('professors_gender');
        $professor->identification_code = $request->input('identification_code');
        $professor->is_student = $request->input('is_student');
        $professor->webinars_count = $request->input('webinars_count');
//        $professor->group_classes_count = $request->input('group_classes_count');
        $professor->private_classes_count = $request->input('private_classes_count');
        $professor->students_count = $request->input('students_count');
        $professor->about_text = $request->input('about_text');
        $professor->date_of_birth = $request->input('date_of_birth');
        $professor->country_id = $request->input('country_id');
        $professor->province_id = $request->input('province_id');
        $professor->city_id = $request->input('city_id');
        $professor->is_inside = $request->input('is_inside');
        $professor->age = $request->input('age');
        $professor->place_of_birth = $request->input('place_of_birth');
        $professor->national_code = $request->input('national_code');

        $professor->save();

        return redirect()->route('tayid_asatid')->with('success', 'اطلاعات استاد با موفقیت بروزرسانی شد.');
    }

    public function delete_professor($id)
    {
        $professor = Professor::findOrFail($id);

        $professor->delete();

        return redirect()->route('tayid_asatid')->with('success', 'استاد با موفقیت حذف شد.');
    }


/////////////
////////////وارد کردن زبان ها
///////////

    public function vared_kardan_zabanha()
    {
        $languages = Languages::all();

        return view('admin.vared_kardan_zabanha', compact('languages'));
    }

    public function vared_kardan_zabanha_store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'flag' => 'required|image|mimes:svg|max:2048',
        ]);

        // Handle flag upload
        $flagPath = null;
        if ($request->hasFile('flag')) {
            $flag = $request->file('flag');
            $flagName = time() . '_' . $flag->getClientOriginalName();
            $flag->move(public_path('images/icon'), $flagName);
            $flagPath = 'images/icon/' . $flagName;
        }

        // Create language
        $language = Languages::create([
            'title' => $request->title,
            'flag' => $flagPath,
        ]);

        // Create default accent with the same title as the language
        Accent::create([
            'title' => $request->title,
            'languages_id' => $language->id,
        ]);

        return redirect()->route('vared_kardan_zabanha')->with('success', 'زبان و لهجه با موفقیت اضافه شدند.');
    }


    public function vared_kardan_zabanha_edit($id)
    {
        $language = Languages::findOrFail($id);
        return response()->json($language);
    }

    public function vared_kardan_zabanha_update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'flag' => 'nullable|image|mimes:svg|max:2048',
        ]);

        $language = Languages::findOrFail($id);
        $language->title = $request->title;

        if ($request->hasFile('flag')) {
            if (file_exists(public_path($language->flag))) {
                unlink(public_path($language->flag));
            }

            $flag = $request->file('flag');
            $flagName = time() . '_' . $flag->getClientOriginalName();
            $flagPath = $flag->move(public_path('images/icon'), $flagName);
            $language->flag = 'images/icon/' . $flagName;
        }

        $language->save();

        return redirect()->route('vared_kardan_zabanha')->with('success', 'زبان با موفقیت ویرایش شد.');
    }

    public function vared_kardan_zabanha_destroy($id)
    {
        $language = Languages::findOrFail($id);

        // حذف پرچم از سرور
        if (file_exists(public_path($language->flag))) {
            unlink(public_path($language->flag));
        }

        $language->delete();

        return redirect()->route('vared_kardan_zabanha')->with('success', 'زبان با موفقیت حذف شد.');
    }


///////
///////لهجه
    public function vared_kardan_lahje()
    {
        $languages =Languages::all();
        $lahjes = Accent::all();
        return view('admin.vared_kardan_lahje', compact('lahjes','languages'));
    }

    public function vared_kardan_lahje_store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'languages_id' => 'required|integer',
        ]);

        Accent::create($request->all());

        return redirect()->route('vared_kardan_lahje')->with('success', 'لهجه با موفقیت اضافه شد.');
    }

    public function vared_kardan_lahje_edit($id)
    {
        $lahje = Accent::findOrFail($id);
        return response()->json($lahje);
    }

    public function vared_kardan_lahje_update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'languages_id' => 'required|integer',
        ]);

        $lahje = Accent::findOrFail($id);
        $lahje->update($request->all());

        return redirect()->route('vared_kardan_lahje')->with('success', 'لهجه با موفقیت ویرایش شد.');
    }

    public function vared_kardan_lahje_destroy($id)
    {
        $lahje = Accent::findOrFail($id);
        $lahje->delete();

        return redirect()->route('vared_kardan_lahje')->with('success', 'لهجه با موفقیت حذف شد.');
    }


/////////
////////تایید کلاس گروهی
////////
    public function tayid_klas_goroohi()
    {
        $groupclass = GroupClass::with([
            'teachingType',
            'professor',
            'gender',
            'ageGroup',
            'level',
            'subject',
            'language',
            'books',
            'discount',
            'address.city',     // city درون address
            'address.country',  // country درون address
            'likes',
            'reserve',
            'payment',
            'comments',
            'ratings',
        ])->get();

        $TeachingType = TeachingType::all();
        $gender = Gender::all();
        $ageGroups = AgeGroup::all();
        $ageGroupLanguageLevels = AgeGroupLanguageLevel::all();
        $subjects = Subjects::all();
        $professorAddresses = Offline::with(['professor', 'city', 'country'])->get();

        return view('admin.tayid_klas_goroohi', compact(
            'groupclass',
            'TeachingType',
            'gender',
            'ageGroups',
            'ageGroupLanguageLevels',
            'subjects',
            'professorAddresses'
        ));
    }

    public function approve_tayid_klas_goroohi($id)
    {
        $groupClass = GroupClass::findOrFail($id);
        $groupClass->admin_status = 'approved';
        $groupClass->save();

        return redirect()->back()->with('success', 'کلاس با موفقیت تأیید شد.');
    }

    public function reject_tayid_klas_goroohi(Request $request, $id)
    {
        $validatedData = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $groupClass = GroupClass::findOrFail($id);
        $groupClass->admin_status = 'rejected';
        $groupClass->save();
        Notification::create([
            'text' => $validatedData['reason'],
            'professors_id' => $groupClass->professor->id,
            'type' => '/admin',
        ]);

        return redirect()->back()->with('success', 'کلاس با موفقیت رد شد و نوتیفیکیشن ارسال شد.');
    }

    public function workshops()
    {
        // بارگذاری اطلاعات ورکشاپ‌ها به همراه اطلاعات مرتبط
        $workshops = WorkShop::with([
            'professor',               // استاد
            'gender',                  // جنسیت
            'ageGroup',                // گروه سنی
            'level',                   // سطح
            'subject',                 // موضوع
            'language',                // زبان
            'address.city',            // شهر درون آدرس
            'address.country',         // کشور درون آدرس
            'discount',
        ])->get();

        $TeachingType = TeachingType::all();
        $gender = Gender::all();
        $ageGroups = AgeGroup::all();
        $subjects = Subjects::all();
        $professorAddresses = Offline::with(['professor', 'city', 'country'])->get();

        return view('admin.workshops', compact(
            'workshops',
            'TeachingType',
            'gender',
            'ageGroups',
            'subjects',
            'professorAddresses'
        ));


    }



    public function approve_workshops($id)
    {
        $workshop = WorkShop::findOrFail($id);
        $workshop->admin_status = 'approved';
        $workshop->save();

        return redirect()->back()->with('success', 'کلاس با موفقیت تأیید شد.');
    }

    public function reject_workshops(Request $request, $id)
    {
        $workshop = WorkShop::findOrFail($id);

        $workshop->admin_status = 'rejected';

        $workshop->save();

        Notification::create([
            'text' => $request->input('reason'),
            'professors_id' => $workshop->professor->id,
            'type' => '/admin',
        ]);

        return redirect()->back()->with('success', 'کلاس با موفقیت رد شد و نوتیفیکیشن ارسال شد.');
    }



//////
////
///
    public function tayid_webinar()
    {
        $groupclass = Webinar::with([
            'professor',
            'gender',
            'ageGroup',
            'level',
            'subject',
            'language',
            'books',
            'discount',
            'likes',
            'payment',
            'comments',
            'ratings',
        ])->get();

        $TeachingType = TeachingType::all();
        $gender = Gender::all();
        $ageGroups = AgeGroup::all();
        $ageGroupLanguageLevels = AgeGroupLanguageLevel::all();
        $subjects = Subjects::all();

        return view('admin.tayid_webinar', compact(
            'groupclass',
            'gender',
            'ageGroups',
            'ageGroupLanguageLevels',
            'subjects',
        ));
    }


    public function approve_tayid_webinar($id)
    {
        $webinar = Webinar::findOrFail($id);
        $webinar->admin_status = 'approved';
        $webinar->save();

        return redirect()->back()->with('success', 'وبینار تایید شد و نوتیفیکیشن ارسال شد.');
    }

    public function reject_tayid_webinar(Request $request, $id)
    {
        $webinar = Webinar::findOrFail($id);
        $webinar->admin_status = 'rejected';
        $webinar->save();

        Notification::create([
            'text' => $request->input('reason') ?? 'وبینار شما رد شد.',
            'professors_id' => $webinar->professor->id,
            'type' => '/admin',
        ]);

        return redirect()->back()->with('success', 'وبینار رد شد و نوتیفیکیشن ارسال شد.');
    }



    public function canceled_classes()
    {
        $canceled_classes = Languages::all();

        return view('admin.canceled_classes', compact('canceled_classes'));
    }











////////////////////////////
///////////////////////////
//////////////////////////shop

    public function add_book(Request $request)
    {
        $query = Product::query();
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        return view('shop.add_book', [
            'books' => $query->with('sellers')->latest()->paginate(10),
            'languages' => Languages::all(),
            'categories' => Category::all(),
            'publications' => Publication::all(),
            'age_groups' => AgeGroup::all(),
            'subjects' => Subjects::all(),
            'accents' => Accent::all(),
            'goals' => LearningSubgoal::all(),
            'levels' => LanguageLevel::all(),
        ]);
    }

    public function store_book(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'nullable|string|max:255',
                'price' => 'required|numeric|min:0',
                'off_price' => 'nullable|numeric|min:0',
                'quantity' => 'required|integer|min:0',
                'Volume_number' => 'nullable|integer|min:1',
                'page_number' => 'nullable|integer|min:1',
                'year_of_publication' => 'nullable|integer|min:1000|max:' . date('Y'),
                'Dimensions' => 'nullable|string|max:255',
                'Time_to_print' => 'nullable|integer|min:1',
                'Paper_type' => 'nullable|string|max:255',
                'Cover_type' => 'nullable|string|max:255',
                'shabak_number' => 'nullable|string|max:255',
                'fipa_number' => 'nullable|string|max:255',
                'weight' => 'nullable|numeric|min:0',
                'language_id' => 'required|exists:languages,id',
                'category_id' => 'required|exists:categories,id',
                'subject_id' => 'nullable|exists:subjects,id',
                'accent_id' => 'nullable|exists:accents,id',
                'age_group_id' => 'nullable|exists:age_groups,id',
                'audio_file' => 'required|boolean',
                'video_file' => 'required|boolean',
                'video_accent' => 'required|boolean',
                'audio_accent' => 'required|boolean',
                'is_download' => 'required|boolean',
                'file' => 'nullable|file|mimes:pdf,epub,mobi|max:10240', // Added file validation for downloadable books
                'discount_expiration' => 'nullable|date|after:now',
                'learning_subgoal_id' => 'nullable|exists:learning_subgoals,id',
                'language_levels_id' => 'nullable|exists:language_levels,id',
                'book_status' => 'required|boolean', // Added book_status validation
                'images' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'video' => 'nullable|mimes:mp4,mov,avi,wmv|max:51200',
                'sample_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            $productData = $validated;
            unset($productData['images'], $productData['cover'], $productData['video'], $productData['sample_images'], $productData['price'], $productData['off_price'], $productData['quantity'], $productData['discount_expiration'], $productData['file']);

            $book = Product::create($productData);

            ProductSeller::create([
                'product_id' => $book->id,
                'seller_id' => 1,
                'quantity' => $validated['quantity'],
                'price' => $validated['price'],
                'discounted_price' => $validated['off_price'] ?? null,
                'discount_expire_at' => $validated['discount_expiration'] ?? null,
            ]);

            if ($request->hasFile('file') && $validated['is_download']) {
                $file = $request->file('file');
                $folder = 'files/product/' . $book->id;
                if (!file_exists(public_path($folder))) mkdir(public_path($folder), 0755, true);

                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($folder), $filename);

                $book->update(['file' => $folder . '/' . $filename]);
            }

            if ($request->hasFile('images')) {
                $imageFile = $request->file('images');
                $folder = 'images/product/' . $book->id;
                if (!file_exists(public_path($folder))) mkdir(public_path($folder), 0755, true);

                $filename = time() . '_' . Str::random(10) . '.' . $imageFile->getClientOriginalExtension();
                $imageFile->move(public_path($folder), $filename);

                Image::create([
                    'path' => $folder . '/' . $filename,
                    'imageable_type' => 'App\Models\shop\Product',
                    'imageable_id' => $book->id,
                    'type' => 'book_image',
                ]);
            }

            if ($request->hasFile('cover')) {
                $coverFile = $request->file('cover');
                $folder = 'images/cover/' . $book->id;
                if (!file_exists(public_path($folder))) mkdir(public_path($folder), 0755, true);

                $filename = time() . '_' . Str::random(10) . '.' . $coverFile->getClientOriginalExtension();
                $coverFile->move(public_path($folder), $filename);

                Cover::create([
                    'path' => $folder . '/' . $filename,
                    'coverable_type' => 'App\Models\shop\Product',
                    'coverable_id' => $book->id,
                ]);
            }

            if ($request->hasFile('video')) {
                $videoFile = $request->file('video');
                $folder = 'videos/product/' . $book->id;
                if (!file_exists(public_path($folder))) mkdir(public_path($folder), 0755, true);

                $filename = time() . '_' . Str::random(10) . '.' . $videoFile->getClientOriginalExtension();
                $videoFile->move(public_path($folder), $filename);

                Videos::create([
                    'path' => $folder . '/' . $filename,
                    'videoable_type' => 'App\Models\shop\Product',
                    'videoable_id' => $book->id,
                    'language_id' => $book->language_id,
                ]);
            }

            if ($request->hasFile('sample_images')) {
                $folder = 'images/SampleImages/' . $book->id;
                if (!file_exists(public_path($folder))) mkdir(public_path($folder), 0755, true);

                foreach ($request->file('sample_images') as $image) {
                    $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path($folder), $filename);

                    SampleImages::create([
                        'path' => $folder . '/' . $filename,
                        'imageable_type' => 'App\Models\shop\Product',
                        'imageable_id' => $book->id,
                    ]);
                }
            }

            return redirect()->route('add_book')->with('success', '📚 کتاب با موفقیت ذخیره شد.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ خطا در ذخیره‌سازی: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $book = Product::findOrFail($id);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'nullable|string|max:255',
                'price' => 'required|numeric|min:0',
                'off_price' => 'nullable|numeric|min:0',
                'quantity' => 'required|integer|min:0',
                'Volume_number' => 'nullable|integer|min:1',
                'page_number' => 'nullable|integer|min:1',
                'year_of_publication' => 'nullable|integer|min:1000|max:' . date('Y'),
                'Dimensions' => 'nullable|string|max:255',
                'Time_to_print' => 'nullable|integer|min:1',
                'Paper_type' => 'nullable|string|max:255',
                'Cover_type' => 'nullable|string|max:255',
                'shabak_number' => 'nullable|string|max:255',
                'fipa_number' => 'nullable|string|max:255',
                'weight' => 'nullable|numeric|min:0',
                'language_id' => 'required|exists:languages,id',
                'category_id' => 'required|exists:categories,id',
                'subject_id' => 'nullable|exists:subjects,id',
                'accent_id' => 'nullable|exists:accents,id',
                'age_group_id' => 'nullable|exists:age_groups,id',
                'audio_file' => 'required|boolean',
                'video_file' => 'required|boolean',
                'video_accent' => 'required|boolean',
                'audio_accent' => 'required|boolean',
                'is_download' => 'required|boolean',
                'file' => 'nullable|file|mimes:pdf,epub,mobi|max:10240', // Added file validation
                'discount_expiration' => 'nullable|date|after:now',
                'learning_subgoal_id' => 'nullable|exists:learning_subgoals,id',
                'language_levels_id' => 'nullable|exists:language_levels,id',
                'book_status' => 'required|boolean', // Added book_status validation
                'images' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'video' => 'nullable|mimes:mp4,mov,avi,wmv|max:51200',
                'sample_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            $productData = $validated;
            unset($productData['images'], $productData['cover'], $productData['video'], $productData['sample_images'], $productData['price'], $productData['off_price'], $productData['quantity'], $productData['discount_expiration'], $productData['file']);

            $book->update($productData);

            $productSeller = $book->productSeller ?? ProductSeller::create([
                'product_id' => $book->id,
                'seller_id' => 1]);
            $productSeller->update([
                'quantity' => $validated['quantity'],
                'price' => $validated['price'],
                'discounted_price' => $validated['off_price'] ?? null,
                'discount_expire_at' => $validated['discount_expiration'] ?? null,
            ]);

            if ($request->hasFile('file') && $validated['is_download']) {
                if ($book->file && file_exists(public_path($book->file))) {
                    unlink(public_path($book->file));
                }
                $file = $request->file('file');
                $folder = 'files/product/' . $book->id;
                if (!file_exists(public_path($folder))) mkdir(public_path($folder), 0755, true);

                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($folder), $filename);

                $book->update(['file' => $folder . '/' . $filename]);
            } elseif (!$validated['is_download'] && $book->file) {
                if (file_exists(public_path($book->file))) {
                    unlink(public_path($book->file));
                }
                $book->update(['file' => null]);
            }

            if ($request->hasFile('images')) {
                if ($book->images()->exists()) {
                    $oldImage = $book->images()->first();
                    if (file_exists(public_path($oldImage->path))) {
                        unlink(public_path($oldImage->path));
                    }
                    $oldImage->delete();
                }

                $imageFile = $request->file('images');
                $folder = 'images/product/' . $book->id;
                if (!file_exists(public_path($folder))) mkdir(public_path($folder), 0755, true);

                $filename = time() . '_' . Str::random(10) . '.' . $imageFile->getClientOriginalExtension();
                $imageFile->move(public_path($folder), $filename);

                Image::create([
                    'path' => $folder . '/' . $filename,
                    'imageable_type' => 'App\Models\shop\Product',
                    'imageable_id' => $book->id,
                    'type' => 'book_image',
                ]);
            }

            if ($request->hasFile('cover')) {
                if ($book->covers()->exists()) {
                    $oldCover = $book->covers()->first();
                    if (file_exists(public_path($oldCover->path))) {
                        unlink(public_path($oldCover->path));
                    }
                    $oldCover->delete();
                }

                $coverFile = $request->file('cover');
                $folder = 'images/cover/' . $book->id;
                if (!file_exists(public_path($folder))) mkdir(public_path($folder), 0755, true);

                $filename = time() . '_' . Str::random(10) . '.' . $coverFile->getClientOriginalExtension();
                $coverFile->move(public_path($folder), $filename);

                Cover::create([
                    'path' => $folder . '/' . $filename,
                    'coverable_type' => 'App\Models\shop\Product',
                    'coverable_id' => $book->id,
                ]);
            }

            if ($request->hasFile('video')) {
                if ($book->videos()->exists()) {
                    $oldVideo = $book->videos()->first();
                    if (file_exists(public_path($oldVideo->path))) {
                        unlink(public_path($oldVideo->path));
                    }
                    $oldVideo->delete();
                }

                $videoFile = $request->file('video');
                $folder = 'videos/product/' . $book->id;
                if (!file_exists(public_path($folder))) mkdir(public_path($folder), 0755, true);

                $filename = time() . '_' . Str::random(10) . '.' . $videoFile->getClientOriginalExtension();
                $videoFile->move(public_path($folder), $filename);

                Videos::create([
                    'path' => $folder . '/' . $filename,
                    'videoable_type' => 'App\Models\shop\Product',
                    'videoable_id' => $book->id,
                    'language_id' => $book->language_id,
                ]);
            }

            if ($request->hasFile('sample_images')) {
                if ($book->SampleImages()->exists()) {
                    foreach ($book->SampleImages as $oldSample) {
                        if (file_exists(public_path($oldSample->path))) {
                            unlink(public_path($oldSample->path));
                        }
                        $oldSample->delete();
                    }
                }

                $folder = 'images/SampleImages/' . $book->id;
                if (!file_exists(public_path($folder))) mkdir(public_path($folder), 0755, true);

                foreach ($request->file('sample_images') as $image) {
                    $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path($folder), $filename);

                    SampleImages::create([
                        'path' => $folder . '/' . $filename,
                        'imageable_type' => 'App\Models\shop\Product',
                        'imageable_id' => $book->id,
                    ]);
                }
            }

            return redirect()->route('add_book')->with('success', '📚 کتاب با موفقیت به‌روزرسانی شد.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ خطا در به‌روزرسانی: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $book = Product::findOrFail($id);

            if ($book->images()->exists()) {
                foreach ($book->images as $image) {
                    if (file_exists(public_path($image->path))) {
                        unlink(public_path($image->path));
                    }
                    $image->delete();
                }
            }

            if ($book->cover()->exists()) {
                $cover = $book->cover()->first();
                if (file_exists(public_path($cover->path))) {
                    unlink(public_path($cover->path));
                }
                $cover->delete();
            }

            if ($book->videos()->exists()) {
                $video = $book->videos()->first();
                if (file_exists(public_path($video->path))) {
                    unlink(public_path($video->path));
                }
                $video->delete();
            }

            if ($book->sample_images()->exists()) {
                foreach ($book->sample_images as $sample) {
                    if (file_exists(public_path($sample->path))) {
                        unlink(public_path($sample->path));
                    }
                    $sample->delete();
                }
            }

            if ($book->file && file_exists(public_path($book->file))) {
                unlink(public_path($book->file));
            }

            if ($book->productSeller) {
                $book->productSeller->delete();
            }

            $book->delete();

            return redirect()->route('add_book')->with('success', '📚 کتاب با موفقیت حذف شد.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ خطا در حذف: ' . $e->getMessage());
        }
    }
    ////////


    public function comments(Request $request)
    {
        $model = $request->input('model'); // مثلاً '\App\Models\GroupClass'

        // تعداد در هر صفحه
        $perPage = 10;

        $textComments = Comment::getCommentsForAdmin('text', $model)
            ->paginate($perPage, ['*'], 'text_page');

        $audioComments = Comment::getCommentsForAdmin('audio', $model)
            ->paginate($perPage, ['*'], 'audio_page');

        $videoComments = Comment::getCommentsForAdmin('video', $model)
            ->paginate($perPage, ['*'], 'video_page');

        $all = [
            'text' => $textComments,
            'audio' => $audioComments,
            'video' => $videoComments,
        ];

        return view('admin.tayid_kament', ['grouped' => $all]);
    }
    public function approve($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->admin_status = 'approved';
        $comment->save();

        return back()->with('success', 'کامنت تأیید شد.');
    }

    public function reject($id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->video_url && Storage::disk('public')->exists($comment->video_url)) {
            Storage::disk('public')->delete($comment->video_url);
        }
        if ($comment->voice_url && Storage::disk('public')->exists($comment->voice_url)) {
            Storage::disk('public')->delete($comment->voice_url);
        }

        $comment->delete();

        return back()->with('success', 'کامنت رد شد.');
    }


    public function discount()
    {
        $discounts = DiscountCode::latest()->get();
        return view('admin.discount', compact('discounts'));
    }
    public function storeDiscount(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:discount_codes,code',
            'type' => 'required|in:percentage,amount',
            'value' => 'required|integer|min:1',
            'start_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:start_at',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        DiscountCode::create([
            'code' => $request->code,
            'type' => $request->type,
            'value' => $request->value,
            'start_at' => $request->start_at,
            'expires_at' => $request->expires_at,
            'usage_limit' => $request->usage_limit,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('discount')->with('success', 'کد تخفیف با موفقیت ایجاد شد.');
    }

    public function discount_update(Request $request, $id)
    {
        $discount = DiscountCode::findOrFail($id);

        $request->validate([
            'code' => 'required|string|unique:discount_codes,code,' . $discount->id,
            'type' => 'required|in:percentage,amount',
            'value' => 'required|integer|min:1',
            'start_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:start_at',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $discount->update([
            'code' => $request->code,
            'type' => $request->type,
            'value' => $request->value,
            'start_at' => $request->start_at,
            'expires_at' => $request->expires_at,
            'usage_limit' => $request->usage_limit,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('discount')->with('success', 'کد تخفیف با موفقیت ویرایش شد.');
    }

    public function discount_destroy($id)
    {
        $discount = DiscountCode::findOrFail($id);
        $discount->delete();

        return redirect()->route('discount')->with('success', 'کد تخفیف با موفقیت حذف شد.');
    }

////////
/// //////

    public function indexEducationalProduct()
    {
        $products = EducationalProduct::with(['ageGroup', 'language', 'sellers'])->latest()->paginate(20);
        return view('shop.educational_products', compact('products'));
    }

    public function createEducationalProduct()
    {
        $ageGroups = AgeGroup::all();
        $languages = Languages::all();
        $sellers = Seller::all();
        return view('shop.educational_products', compact('ageGroups', 'languages', 'sellers'));
    }

    public function storeEducationalProduct(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'age_group_id' => 'required|exists:age_groups,id',
            'language_id' => 'required|exists:languages,id',
            'price' => 'required|integer|min:0',
            'off_price' => 'nullable|integer|min:0',
            'free_shipping' => 'sometimes|boolean',
            'today_shipping' => 'sometimes|boolean',
            'is_download' => 'sometimes|boolean',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_expiration' => 'nullable|date',
            'sellers' => 'nullable|array',
            'sellers.*' => 'exists:sellers,id',
        ]);

        $product = EducationalProduct::create($data);
        $product->sellers()->sync($data['sellers'] ?? []);

        return redirect()->route('shop.educational_products')->with('success', 'محصول با موفقیت ایجاد شد.');
    }

    public function editEducationalProduct(EducationalProduct $educationalProduct)
    {
        $ageGroups = AgeGroup::all();
        $languages = Languages::all();
        $sellers = Seller::all();
        return view('shop.educational_products', compact('educationalProduct', 'ageGroups', 'languages', 'sellers'));
    }

    public function updateEducationalProduct(Request $request, EducationalProduct $educationalProduct)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'age_group_id' => 'required|exists:age_groups,id',
            'language_id' => 'required|exists:languages,id',
            'price' => 'required|integer|min:0',
            'off_price' => 'nullable|integer|min:0',
            'free_shipping' => 'sometimes|boolean',
            'today_shipping' => 'sometimes|boolean',
            'is_download' => 'sometimes|boolean',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_expiration' => 'nullable|date',
            'sellers' => 'nullable|array',
            'sellers.*' => 'exists:sellers,id',
        ]);

        $educationalProduct->update($data);
        $educationalProduct->sellers()->sync($data['sellers'] ?? []);

        return redirect()->route('shop.educational_products')->with('success', 'محصول با موفقیت به‌روزرسانی شد.');
    }

    public function destroyEducationalProduct(EducationalProduct $educationalProduct)
    {
        $educationalProduct->delete();
        return redirect()->route('shop.educational_products')->with('success', 'محصول با موفقیت حذف شد.');
    }

    ////

    public function indexChat(Request $request)
    {
        $query = Conversation::with(['user1', 'user2']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user1', fn($q1) => $q1->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%");
                }))
                    ->orWhereHas('user2', fn($q2) => $q2->where(function($q) use ($search) {
                        $q->where('first_name', 'like', "%$search%")
                            ->orWhere('last_name', 'like', "%$search%");
                    }));
            });
        }

        $conversations = $query->latest()->paginate(10);

        return view('admin.conversations', compact('conversations'));
    }

    public function ajaxChatMessages($id)
    {
        $conversation = Conversation::with(['messages.sender', 'user1', 'user2'])->findOrFail($id);

        $messages = $conversation->messages->map(function ($m) {
            return [
                'sender'     => $m->sender ? ($m->sender->first_name . ' ' . $m->sender->last_name) : 'ناشناس',
                'sender_id'  => $m->sender_id,
                'message'    => $m->message,
                'file'       => $m->file_path ?? null,
                'voice'      => $m->voice_path ?? null,
                'type'       => $m->message_type,
                'created_at' => $m->created_at->format('Y-m-d H:i'),
            ];
        });

        return response()->json([
            'messages' => $messages,
            'user1_id' => $conversation->user1_id,
            'user2_id' => $conversation->user2_id,
        ]);
    }


    /////
    /// ////
    /// blog
    //
    public function blog()
    {
        return view('admin.blog', [
            'approvedBlogs' => Blog::where('status', 'approved')->latest()->get(),
            'pendingBlogs' => Blog::where('status', 'pending')->latest()->get(),
            'rejectedBlogs' => Blog::where('status', 'rejected')->latest()->get(),
        ]);
    }

    public function add_blog(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'tags' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'time_for_read' => 'nullable|string|max:255',
            'about_blog' => 'nullable|string|max:255',
            'text' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $path = 'image/blog/' . $filename;
            $file->move(public_path('image/blog'), $filename);
            $data['image'] = $path;
        }

        $data['user_id'] = auth()->id() ?? 1;

        if (!empty($data['tags'])) {
            $data['tags'] = array_map('trim', explode(',', $data['tags']));
        }

        Blog::create($data);

        return redirect()->back()->with('success', 'بلاگ با موفقیت افزوده شد.');
    }

    public function blog_update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'tags' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'time_for_read' => 'nullable|string|max:255',
            'about_blog' => 'nullable|string|max:255',
            'text' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        if ($request->hasFile('image')) {
            // حذف تصویر قبلی در صورت وجود
            if ($blog->image && file_exists(public_path($blog->image))) {
                unlink(public_path($blog->image));
            }

            $file = $request->file('image');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $path = 'image/blog/' . $filename;
            $file->move(public_path('image/blog'), $filename);
            $data['image'] = $path;
        }

        if (!empty($data['tags'])) {
            $data['tags'] = array_map('trim', explode(',', $data['tags']));
        }

        $blog->update($data);

        return redirect()->back()->with('success', 'بلاگ به‌روزرسانی شد.');
    }
    public function uploadImage(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|mimes:jpeg,png,gif|max:5120' // حداکثر 5MB
        ]);

        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $path = 'image/blog/' . $filename;
            $file->move(public_path('image/blog'), $filename);

            return response()->json([
                'url' => asset($path)
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

    public function blog_destroy($id)
    {
        $blog = Blog::findOrFail($id);

        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }

        $blog->delete();

        return redirect()->back()->with('success', 'بلاگ حذف شد.');
    }
    public function blog_approve($id)
    {
        $blog = Blog::findOrFail($id);
        $blog->status = 'approved';
        $blog->save();

        return back()->with('success', 'بلاگ تأیید شد.');
    }

    public  function blog_reject(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $blog = Blog::findOrFail($id);
        $blog->status = 'rejected';
        $blog->save();

//    Notification::create([
//        'text' => $validated['reason'],
//        'professors_id' => $blog->user_id,
//        'type' => '/admin',
//    ]);

        return back()->with('success', 'بلاگ رد شد و دلیل ذخیره شد.');
    }

    public function consultation_form()
    {
        // دریافت تمام فرم‌های مشاوره
        $consultations = Consultation::all();

        return view('admin.consultation_form', compact('consultations'));
    }

    public function consultation_destroy($id)
    {
        $consultation = Consultation::findOrFail($id);
        $consultation->delete();

        return redirect()->back()->with('success', 'فرم مشاوره با موفقیت حذف شد.');
    }



    // صفحه مدیریت تیکت‌ها
    public function tickets()
    {
        $departments = Ticket::select('department')->distinct()->pluck('department')->toArray();
        $statuses = ['pending', 'open', 'closed', 'complete'];

        // بارگذاری کاربر با فیلدهای مورد نیاز
        $tickets = Ticket::with(['user' => function($query) {
            $query->select('id', 'first_name', 'last_name');
        }])->get();

        $ticketsGrouped = [];

        foreach ($departments as $department) {
            foreach ($statuses as $status) {
                $ticketsGrouped[$department][$status] = $tickets->where('department', $department)
                    ->where('status', $status);
            }
        }

        return view('admin.tickets', compact('departments', 'statuses', 'ticketsGrouped'));
    }

    public function getTicketMessages($id)
    {
        $ticket = Ticket::with(['messages.user'])->find($id);

        if (!$ticket) {
            return response()->json(['message' => 'تیکت یافت نشد'], 404);
        }

        $messages = $ticket->messages->map(function ($msg) {
            return [
                'id' => $msg->id,
                'message' => $msg->message,
                'is_support_reply' => (bool)$msg->is_support_reply,
                'created_at' => $msg->created_at->format('Y/m/d H:i'),
                'file' => $msg->file,
                'user_name' => $msg->user ? $msg->user->name : 'نامشخص',
            ];
        });

        return response()->json([
            'ticket_number' => $ticket->ticket_number,
            'subject' => $ticket->subject,
            'status' => $ticket->status,
            'messages' => $messages,
        ]);
    }

    public function ticketClose($id)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['message' => 'تیکت یافت نشد'], 404);
        }

        $ticket->status = 'closed';
        $ticket->save();

        return response()->json(['message' => 'تیکت با موفقیت بسته شد']);
    }

    public function ticketReopen($id)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['message' => 'تیکت یافت نشد'], 404);
        }

        $ticket->status = 'open';
        $ticket->save();

        return response()->json(['message' => 'تیکت با موفقیت باز شد']);
    }

    public function ticketComplete($id)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['message' => 'تیکت یافت نشد'], 404);
        }

        $ticket->status = 'complete';
        $ticket->save();

        return response()->json(['message' => 'تیکت با موفقیت اتمام یافت']);
    }

    public function ticketReply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'تیکت یافت نشد'], 404);
        }

        $message = $ticket->messages()->create([
            'message' => $request->message,
            'is_support_reply' => true, // نشان می‌دهد این پاسخ از طرف پشتیبانی است
            'user_id' => $ticket->user_id, // آیدی کاربری که تیکت را ایجاد کرده
        ]);

        return response()->json(['success' => true, 'message' => 'پیام با موفقیت ارسال شد']);
    }

    ///
    /// /
    ///
    public function news($id = null)
    {
        $news = News::orderBy('order', 'asc')->get();

        // اگر آی‌دی اومد یعنی ویرایش می‌خوای
        $editNews = null;
        if ($id) {
            $editNews = News::findOrFail($id);
        }

        return view('admin.news', compact('news', 'editNews'));
    }

    public function news_store_update(Request $request)
    {
        $data = $request->validate([
            'id' => 'nullable|exists:news,id',
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'tag' => 'nullable|string',
            'text' => 'nullable|string',
            'order' => 'required|integer|min:1|max:10',
        ]);

        // ذخیره تصویر با استفاده از move
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName(); // نام فایل شامل زمان
            $file->move(public_path('images'), $filename); // انتقال فایل به مسیر دلخواه
            $data['image'] = 'images/' . $filename; // ذخیره مسیر فایل در دیتابیس
        }

        $data['updated_at'] = now();

        if (!empty($data['id'])) {
            // ویرایش
            $news = News::findOrFail($data['id']);

            // اگر تصویر جدید آپلود شده و تصویر قبلی وجود دارد، تصویر قبلی حذف شود
            if ($request->hasFile('image') && $news->image) {
                if (file_exists(public_path($news->image))) {
                    unlink(public_path( $news->image));
                }
            }

            $news->update($data);
            $msg = 'خبر با موفقیت به‌روزرسانی شد.';
        } else {
            // ایجاد جدید
            $data['view'] = 0;
            $data['created_at'] = now();
            News::create($data);
            $msg = 'خبر با موفقیت ایجاد شد.';
        }

        return redirect()->route('news')->with('success', $msg);
    }

    public function news_destroy($id)
    {
        // پیدا کردن خبر مورد نظر
        $news = News::findOrFail($id);

        // حذف تصویر از سرور اگر تصویر وجود دارد
        if ($news->image) {
            $imagePath = public_path( $news->image); // مسیر کامل تصویر
            if (file_exists($imagePath)) {
                unlink($imagePath); // حذف تصویر
            }
        }

        // حذف خبر از پایگاه داده
        $news->delete();

        // بازگشت به صفحه مدیریت اخبار با پیام موفقیت
        return redirect()->route('news')->with('success', 'خبر با موفقیت حذف شد.');
    }
    ////
    /// //
    ///
    public function bankexans()
    {
        $exams = Exam::all();
        $skills = Skill::all();
        $languages = Languages::all();

        return view('admin.bankexans', compact('exams', 'skills', 'languages'));
    }

    public function createBankExan()
    {
        $skills = Skill::all();
        $languages = Languages::all();
        return view('admin.create_exam_step1', compact('skills', 'languages'));
    }

    public function storeBankExan(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'skill_id' => 'required|exists:skills,id',
            'language_id' => 'required|exists:languages,id',
            'level' => 'required|string|in:beginner,intermediate,hard,advanced',
            'type' => 'required|string|in:konkur,masters,phd,university,school,olympiad,language,employment_exam,custom',
            'duration_minutes' => 'required|integer|min:10|max:240',
            'questions_count' => 'required|integer|min:1|max:1000',
            'is_foreign' => 'required|boolean',
            'is_active' => 'required|boolean',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'questions' => 'nullable',
            'TimeToRead' => 'nullable|string'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/exams'), $imageName);
            $validated['image'] = 'images/exams/' . $imageName; // Store relative path
        }

        $exam = Exam::create($validated);

        return redirect()->route('admin.bankexans.create_step2', ['exam_id' => $exam->id])
            ->with('success', 'آزمون با موفقیت ایجاد شد. حالا بخش‌ها را اضافه کنید.');
    }


    public function createExamPart(Request $request)
    {
        $exams = Exam::all();
        $exam_id = $request->query('exam_id');
        return view('admin.create_exam_step2', compact('exams', 'exam_id'));
    }

    public function storeExamPart(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'number' => 'required|integer|min:1|unique:exam_parts,number,NULL,id,exam_id,' . $request->exam_id,
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'passenger' => 'required_if:questionType,reading|nullable|string',
            'duration' => 'required|string',
            'questionType' => 'required|string|in:welcome,cloze_test,listening,reading,speaking,writing',
            'order' => 'required|integer|min:1',
            'passenger_title' => 'nullable|string',
            'questions_title' => 'nullable|string',
            'multiple_correct' => 'required|boolean',
        ]);

        if ($request->questionType !== 'reading') {
            $validated['passenger'] = null;
        }

        $examPart = ExamPart::create($validated);

        return redirect()->route('admin.questions.create', ['exam_part_id' => $examPart->id])
            ->with('success', 'بخش آزمون با موفقیت ایجاد شد. حالا سوالات را اضافه کنید.');
    }


    public function createQuestion(Request $request)
    {
        $exam_parts = ExamPart::with('exam')->get();
        $exam_part_id = $request->query('exam_part_id');
        $question_types = QuestionType::all();
        return view('admin.questions.create', compact('exam_parts', 'exam_part_id', 'question_types'));
    }

    public function storeQuestion(Request $request)
    {
        $validated = $request->validate([
            'exam_part_id' => 'required|exists:exam_parts,id',
            'title' => 'required|string|max:255',
            'question_type_id' => 'required|exists:question_types,id',
            'question_text' => 'required|string',
            'difficulty' => 'required|string|in:easy,medium,hard',
            'multiple_correct' => 'required|boolean',
            'short_answer' => 'required|string|regex:/^[a-zA-Z]+ @$/',
            'variants.*' => 'nullable|string',
            'options.*' => 'nullable|string',
            'is_correct.*' => 'nullable|in:1',
            'media' => 'nullable|file|mimes:jpg,png,mp3,mp4|max:10240',
            'media_type' => 'nullable|in:image,audio,video',
            'media_description' => 'nullable|string',
        ]);

        // Create question
        $question = Question::create([
            'exam_part_id' => $validated['exam_part_id'],
            'title' => $validated['title'],
            'question_type_id' => $validated['question_type_id'],
            'question_text' => $validated['question_text'],
            'difficulty' => $validated['difficulty'],
            'multiple_correct' => $validated['multiple_correct'],
            'short_answer' => $validated['short_answer'],
            'parent' => 0,
        ]);

        // Save variants
        if (!empty($validated['variants'])) {
            foreach ($validated['variants'] as $variant_text) {
                if ($variant_text) {
                    QuestionVariant::create([
                        'question_id' => $question->id,
                        'variant_text' => $variant_text,
                    ]);
                }
            }
        }

        // Save answer options
        if (!empty($validated['options'])) {
            foreach ($validated['options'] as $index => $option_text) {
                if ($option_text) {
                    Option::create([
                        'question_variant_id' => null, // Assuming single variant for simplicity
                        'question_blank_id' => null, // No specific blank ID for now
                        'option_text' => $option_text,
                        'is_correct' => isset($validated['is_correct'][$index]) ? 1 : 0,
                    ]);
                }
            }
        }

        // Save media
        if ($request->hasFile('media') && $request->media_type) {
            $path = $request->file('media')->store('media', 'public');
            MediaQuestion::create([
                'question_id' => $question->id,
                'exam_part_id' => $validated['exam_part_id'],
                'media_path' => $path,
                'media_type' => $request->media_type,
                'description' => $request->media_description,
            ]);
        }

        return redirect()->route('admin.bankexans')->with('success', 'سوال با موفقیت ایجاد شد.');
    }



















    /////
    /// /
    public function contacts()
    {
        $contacts = ContactUs::latest()->paginate(10);
        return view('admin.contacts', compact('contacts'));
    }

    public function markSeen($id)
    {
        $contact = ContactUs::findOrFail($id);
        $contact->status = 'answered';
        $contact->save();

        return redirect()->back()->with('success', 'پیام با موفقیت تایید شد.');
    }


    ///////////

    public function slider()
    {
        $sliders = Slaider::latest()->get();
        return view('admin.slider', compact('sliders'));
    }

    public function slider_destroy($id)
    {
        $slider = Slaider::findOrFail($id);

        // حذف عکس‌ها اگر موجود باشند
        if ($slider->mobile_image && file_exists(public_path($slider->mobile_image))) {
            unlink(public_path($slider->mobile_image));
        }

        if ($slider->tablet_image && file_exists(public_path($slider->tablet_image))) {
            unlink(public_path($slider->tablet_image));
        }

        if ($slider->laptop_image && file_exists(public_path($slider->laptop_image))) {
            unlink(public_path($slider->laptop_image));
        }

        // حذف رکورد دیتابیس
        $slider->delete();

        return redirect()->route('admin.slider')->with('success', 'اسلایدر و فایل‌های مرتبط حذف شدند.');
    }

    public function slider_store_update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|exists:slaiders,id',
            'link' => 'nullable|string',
            'where_page' => 'required|string',
            'location' => 'required|string',
            'mobile_image' => 'nullable|image',
            'tablet_image' => 'nullable|image',
            'laptop_image' => 'nullable|image',
        ]);

        if ($request->id) {
            $slider = Slaider::findOrFail($request->id);
        } else {
            $slider = new Slaider();
        }

        $slider->link = $request->link;
        $slider->where_page = $request->where_page;
        $slider->location = $request->location;

        // Mobile image
        if ($request->hasFile('mobile_image')) {
            // حذف فایل قبلی
            if ($slider->mobile_image && file_exists(public_path($slider->mobile_image))) {
                unlink(public_path($slider->mobile_image));
            }

            $mobileFile = $request->file('mobile_image');
            $mobileName = time() . '_mobile.' . $mobileFile->getClientOriginalExtension();
            $mobilePath = 'profiles/professor/' . $mobileName;
            $mobileFile->move(public_path('profiles/professor'), $mobileName);
            $slider->mobile_image = $mobilePath;
        }

        // Tablet image
        if ($request->hasFile('tablet_image')) {
            if ($slider->tablet_image && file_exists(public_path($slider->tablet_image))) {
                unlink(public_path($slider->tablet_image));
            }

            $tabletFile = $request->file('tablet_image');
            $tabletName = time() . '_tablet.' . $tabletFile->getClientOriginalExtension();
            $tabletPath = 'profiles/professor/' . $tabletName;
            $tabletFile->move(public_path('profiles/professor'), $tabletName);
            $slider->tablet_image = $tabletPath;
        }

        // Laptop image
        if ($request->hasFile('laptop_image')) {
            if ($slider->laptop_image && file_exists(public_path($slider->laptop_image))) {
                unlink(public_path($slider->laptop_image));
            }

            $laptopFile = $request->file('laptop_image');
            $laptopName = time() . '_laptop.' . $laptopFile->getClientOriginalExtension();
            $laptopPath = 'profiles/professor/' . $laptopName;
            $laptopFile->move(public_path('profiles/professor'), $laptopName);
            $slider->laptop_image = $laptopPath;
        }

        $slider->save();

        return redirect()->route('admin.slider')->with('success', 'اسلایدر ذخیره شد.');
    }


    ////////
    ///
    ///
    ///
    ///
    public function Baner()
    {
        $baners = Baner::with('product')->get();
        $products = Product::all();

        return view('admin.Baner', compact('baners', 'products'));
    }


    public function Baner_update(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'product_id' => 'nullable|exists:products,id',
        ]);

        $baner = Baner::findOrFail($id);
        $baner->update([
            'title' => $data['title'],
            'url' => $data['url'],
            'product_id' => $data['product_id'] ?? null,
        ]);

        return redirect()->route('admin.Baner')->with('success', 'بنر با موفقیت ویرایش شد.');
    }

    ///
    ///

    public function videoBaner()
    {
        $videoBaners = VideoBaner::all();
        return view('admin.VideoBaner', compact('videoBaners'));
    }

    public function videoBanerUpdate(Request $request, $id)
    {
        $baner = VideoBaner::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'video' => 'nullable|file|mimes:mp4,mov,avi,wmv|max:512000',
            'cover' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:80240',
        ]);

        $baner->title = $request->title;




        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $videoName = time() . '_' . $video->getClientOriginalName();
            $video->move(public_path('video'), $videoName);
            $baner->path = 'video/' . $videoName;
        }

        if ($request->hasFile('cover')) {
            $cover = $request->file('cover');
            $coverName = time() . '_' . $cover->getClientOriginalName();
            $cover->move(public_path('cover'), $coverName);
            $baner->cover = 'cover/' . $coverName;
        }

        $baner->save();

        return redirect()->back()->with('success', 'ویدیو بنر بروزرسانی شد.');
    }

    ///
    ///
    public function story()
    {
        $stories = Story::all();
        return view('admin.story', compact('stories'));
    }

    public function story_approve($id)
    {
        $story = Story::findOrFail($id);
        $story->is_approved = 1;
        $story->save();

        return redirect()->route('admin.story')->with('success', 'استوری تایید شد.');
    }

    public function story_reject($id)
    {
        $story = Story::findOrFail($id);
        $story->is_approved = 0;
        $story->save();

        return redirect()->route('admin.story')->with('success', 'استوری رد شد.');
    }


    //////
    /// ژ

    public function publications()
    {
        $publications = Publication::all();
        return view('admin.publications', compact('publications'));
    }

    public function publications_store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255']);

        Publication::create(['title' => $request->title]);

        return back()->with('success', 'انتشارات جدید اضافه شد.');
    }

    public function publications_update(Request $request, $id)
    {
        $request->validate(['title' => 'required|string|max:255']);

        $pub = Publication::findOrFail($id);
        $pub->update(['title' => $request->title]);

        return back()->with('success', 'انتشارات ویرایش شد.');
    }

    public function publications_delete($id)
    {
        $pub = Publication::findOrFail($id);
        $pub->delete();

        return back()->with('success', 'انتشارات حذف شد.');
    }

/////////
///
    public function wallets(Request $request)
    {
        $query = Wallet::with('user');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        $wallets = $query->paginate(15);

        return view('admin.wallets', compact('wallets'));
    }

    /////
    ///
    public function settings()
    {
        // فقط تنظیمات مدنظر رو می‌گیریم (اضافه کردم موارد جدید)
        $keys = [
            'dolar_price',
            'tipax',
            'post',
            'online_certificate',
            'physical_certificate',
            'group_class_percentage',
            'workshop_percentage',
            'private_class_percentage',
            'webinar_percentage',
        ];

        $settings = SystemSetting::whereIn('name', $keys)->get()->keyBy('name');

        return view('admin.settings', compact('settings'));
    }

    public function settings_update(Request $request)
    {
        $keys = [
            'dolar_price',
            'tipax',
            'post',
            'online_certificate',
            'physical_certificate',
            'group_class_percentage',
            'workshop_percentage',
            'private_class_percentage',
            'webinar_percentage',
        ];

        $data = $request->validate([
            'dolar_price' => 'required|numeric',
            'tipax' => 'required|numeric',
            'post' => 'required|numeric',
            'online_certificate' => 'required|numeric',
            'physical_certificate' => 'required|numeric',
            'group_class_percentage' => 'required|numeric|min:0|max:100',
            'workshop_percentage' => 'required|numeric|min:0|max:100',
            'private_class_percentage' => 'required|numeric|min:0|max:100',
            'webinar_percentage' => 'required|numeric|min:0|max:100',
        ]);

        foreach ($keys as $key) {
            SystemSetting::updateOrCreate(
                ['name' => $key],
                ['value' => $data[$key]]
            );
        }

        return redirect()->route('admin.settings')->with('success', 'تنظیمات با موفقیت به‌روز شدند.');
    }


    //
    public function examPayments()
    {
        $search = request()->input('search');

        $query = OrderExamPayment::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%$search%")
                    ->orWhere('exam_location', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%");
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->get();

        return view('admin.exam_payments', compact('payments', 'search'));
    }
    /////
    ///
    ///
    public function categoryicon()
    {
        $categories = Category::all();
        return view('admin.categoryicon', compact('categories'));
    }

    public function categoryicon_update(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'icon' => 'required|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        $category = Category::findOrFail($request->category_id);

        // مسیر پوشه داخل public
        $folder = 'category_icons';

        // حذف آیکون قبلی اگر وجود داشته باشد
        if ($category->icon && file_exists(public_path($category->icon))) {
            unlink(public_path($category->icon));
        }

        // ساخت پوشه اگر وجود ندارد
        if (!file_exists(public_path($folder))) {
            mkdir(public_path($folder), 0755, true);
        }

        // نام جدید فایل
        $filename = uniqid() . '.' . $request->file('icon')->getClientOriginalExtension();

        // انتقال فایل به public/category_icons
        $request->file('icon')->move(public_path($folder), $filename);

        // ذخیره مسیر نسبی در دیتابیس
        $category->icon = $folder . '/' . $filename;
        $category->save();

        return redirect()->route('admin.categoryicon')->with('success', 'آیکون با موفقیت تغییر کرد.');
    }

    //
    //
    //
    public function orders(Request $request)
    {
        $orders = Order::with('items.product')
            ->where('payment_status', 'paid')
            ->where('seller_id', 1)
            ->latest()
            ->get();

        return view('admin.orders', compact('orders'));
    }

    public function updateOrder(Request $request)
    {
        $order = Order::findOrFail($request->order_id);

        $order->tracking_code = $request->tracking_code;

        if ($request->has('seller_rejection_reason')) {
            $order->seller_rejection_reason = $request->seller_rejection_reason;
            $order->returned_status = 'returned';
        }

        $order->save();

        return back()->with('success', 'سفارش بروزرسانی شد');
    }

    ////
    ///
    ///
    // TransactionController.php
    public function transactions()
    {
        $types = ['deposit', 'withdraw', 'payment', 'lingo']; // انواع دلخواهت
        $statuses = ['pending', 'approved', 'rejected'];

        $transactions = [];
        foreach ($types as $type) {
            $transactions[$type] = [];
            foreach ($statuses as $status) {
                $transactions[$type][$status] = \App\Models\Financial\Transaction::where('type', $type)
                    ->where('status', $status)
                    ->orderByDesc('created_at')
                    ->get();
            }
        }

        return view('admin.transactions', compact('transactions', 'types', 'statuses'));
    }
    public function transactions_update(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'status' => 'required|in:pending,approved,rejected',
            'authority' => 'nullable|string|max:255',
        ]);

        $trx = Transaction::findOrFail($request->transaction_id);
        $trx->status = $request->status;
        $trx->authority = $request->authority;
        $trx->save();

        return redirect()->back()->with('success', 'تراکنش بروزرسانی شد.');
    }

/////
///
    public function teacher_video(Request $request)
    {
        // دریافت زبان با مقدار پیش‌فرض (انگلیسی = 1)
        $languageId = $request->input('language_id', 1);
        $search = $request->input('search', '');
        $professorId = $request->input('professor_id');

        // اعتبارسنجی languageId
        $validLanguages = Languages::pluck('id')->toArray();
        if (!in_array($languageId, $validLanguages)) {
            $languageId = 1; // بازگشت به انگلیسی اگر نامعتبر باشد
        }

        // جستجوی اساتید که ویدیوی معرفی برای زبان انتخاب‌شده دارند
        $professors = Professor::select('id', 'first_name', 'last_name')
            ->when($search, function ($query, $search) {
                return $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%');
            })
            ->whereHas('videos', function ($query) use ($languageId) {
                $query->where('language_id', $languageId)
                    ->where('type', 'about_video'); // فقط ویدیوی معرفی
            })
            ->get();

        $aboutVideo = null;
        $teachingVideos = collect([]); // مقدار پیش‌فرض به صورت مجموعه خالی

        if ($professorId) {
            // دریافت ویدیوی معرفی
            $aboutVideo = Videos::where('type', 'about_video')
                ->where('videoable_type', Professor::class)
                ->where('videoable_id', $professorId)
                ->where('language_id', $languageId)
                ->first();

            // دریافت ویدیوهای نحوه آموزش
            $teachingVideos = Videos::where('type', 'teaching_example_video')
                ->where('videoable_type', Professor::class)
                ->where('videoable_id', $professorId)
                ->where('language_id', $languageId)
                ->get();
        }

        return view('admin.teacher_video', compact('professors', 'aboutVideo', 'teachingVideos', 'search', 'languageId'));
    }
    public function about_video(Request $request)
    {
        // دریافت زبان و شناسه استاد
        $languageId = $request->input('language_id', 1);
        $professorId = $request->input('professor_id');

        // اعتبارسنجی languageId
        $validLanguages = Languages::pluck('id')->toArray();
        if (!in_array($languageId, $validLanguages)) {
            $languageId = 1;
        }

        // دریافت نام زبان
        $language = Languages::find($languageId);
        $languageName = $language ? $language->title : 'نامشخص';

        // دریافت ویدیوی معرفی
        $video = Videos::where('type', 'about_video')
            ->where('videoable_type', Professor::class)
            ->where('videoable_id', $professorId)
            ->where('language_id', $languageId)
            ->first();

        if ($video) {
            return response()->json([
                'status' => 'success',
                'video' => [
                    'path' => asset($video->path),
                    'type' => $video->type,
                ],
                'language' => $languageName,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'ویدیوی معرفی یافت نشد.',
            'language' => $languageName,
        ], 404);
    }

    public function teaching_example_video(Request $request)
    {
        // دریافت زبان و شناسه استاد
        $languageId = $request->input('language_id', 1);
        $professorId = $request->input('professor_id');

        // اعتبارسنجی languageId
        $validLanguages = Languages::pluck('id')->toArray();
        if (!in_array($languageId, $validLanguages)) {
            $languageId = 1;
        }

        // دریافت نام زبان
        $language = Languages::find($languageId);
        $languageName = $language ? $language->title : 'نامشخص';

        // دریافت ویدیوهای نحوه آموزش
        $videos = Videos::where('type', 'teaching_example_video')
            ->where('videoable_type', Professor::class)
            ->where('videoable_id', $professorId)
            ->where('language_id', $languageId)
            ->get();

        // دیباگ تعداد ویدیوها

        if ($videos->isNotEmpty()) {
            return response()->json([
                'status' => 'success',
                'videos' => $videos->map(function ($video) {
                    return [
                        'path' => asset($video->path),
                        'type' => $video->type,
                    ];
                })->toArray(),
                'language' => $languageName,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'ویدیوی نحوه آموزش یافت نشد.',
            'language' => $languageName,
        ], 404);
    }

    ///
    ///
    public function sellerConfirmation(Request $request)
    {
        $search = $request->input('search', '');
        $sellers = Seller::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('national_code', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->get();


        $statuses = [
            'individual_pending' => 'حقیقی - در حال بررسی',
            'individual_approved' => 'حقیقی - تأیید شده',
            'individual_rejected' => 'حقیقی - رد شده',
            'legal_pending' => 'حقوقی - در حال بررسی',
            'legal_approved' => 'حقوقی - تأیید شده',
            'legal_rejected' => 'حقوقی - رد شده'
        ];

        return view('admin.Sellerconfirmation', compact('sellers', 'statuses', 'search'));
    }

    public function updateSellerStatus(Request $request, $id)
    {
        $seller = Seller::findOrFail($id);
        $seller->status = $request->input('status');
        $seller->save();

        return response()->json(['message' => 'وضعیت با موفقیت به‌روزرسانی شد']);
    }


    //////
    ///
    ///

    public function book_confirmation(Request $request)
    {
        try {
            // Fetch books for each status with pagination and eager-loaded relationships
            $confirmedBooks = Product::where('status', 'approved')
                ->with(['language' => fn($query) => $query->select('id', 'title', 'flag'),
                    'category' => fn($query) => $query->select('id', 'title', 'slug', 'is_physical')])
                ->latest()
                ->paginate(10, ['*'], 'confirmed_page');

            $rejectedBooks = Product::where('status', 'rejected')
                ->with(['language' => fn($query) => $query->select('id', 'title', 'flag'),
                    'category' => fn($query) => $query->select('id', 'title', 'slug', 'is_physical')])
                ->latest()
                ->paginate(10, ['*'], 'rejected_page');

            $pendingBooks = Product::where('status', 'pending')
                ->with(['language' => fn($query) => $query->select('id', 'title', 'flag'),
                    'category' => fn($query) => $query->select('id', 'title', 'slug', 'is_physical'),
                    'sellers'])
                ->latest()
                ->paginate(10, ['*'], 'pending_page');

            return view('admin.book_confirmation', [
                'confirmedBooks' => $confirmedBooks,
                'rejectedBooks' => $rejectedBooks,
                'pendingBooks' => $pendingBooks,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'خطا در بارگذاری داده‌ها: ' . $e->getMessage());
        }
    }

    public function confirm_book(Request $request, $id)
    {
            $book = Product::with('sellers')->findOrFail($id);
            $book->update(['status' => 'approved']);



    }

    public function reject_book(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:255',
            ]);

            $book = Product::with('sellers')->findOrFail($id);
            $book->update(['status' => 'rejected']);

            $seller = $book->sellers->first();
            if ($seller) {
                Notification::create([
                    'user_id' => $seller->seller_id,
                    'text' => 'کتاب "' . $book->title . '" رد شد. دلیل: ' . $request->reason,
                    'type' => 'book_rejection',
                ]);
            }

            return response()->json(['message' => 'کتاب با موفقیت رد شد و اعلان ارسال شد.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'خطا در رد کتاب: ' . $e->getMessage()], 500);
        }
    }

}
