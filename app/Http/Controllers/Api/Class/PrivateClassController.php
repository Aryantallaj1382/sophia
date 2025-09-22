<?php

namespace App\Http\Controllers\Api\Class;

use App\Helpers\PlanHelper;
use App\Http\Controllers\Controller;
use App\Models\Accent;
use App\Models\AgeGroup;
use App\Models\GroupClass;
use App\Models\LanguageLevel;
use App\Models\LearningGoal;
use App\Models\LearningSubgoal;
use App\Models\PrivateClassReservation;
use App\Models\PrivateProfessorTimeSlot;
use App\Models\Professor;
use App\Models\ProfessorLearningGoal;
use App\Models\ProfessorTimeSlot;
use App\Models\ReservedBook;
use App\Models\Skill;
use App\Models\Story;
use App\Models\UserPlan;
use App\Models\Webinar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class
PrivateClassController extends Controller
{
    public function professors(Request $request)
    {

        $query = Professor::query();
        if ($request->has('sort')) {
            switch ($request->input('sort')) {
                case 'most_viewed':
                    $query->orderBy('view_count', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        if ($request->has('time')) {
            switch ($request->input('time')) {
                case '1':
                    $query->whereHas('timeSlots', function ($query) {
                        $query->where('date', '>', Carbon::now())->whereBetween('time', ['00:00:00', '05:59:00']);
                    });
                    break;
                case '2':
                    $query->whereHas('timeSlots', function ($query) {
                        $query->where('date', '>', Carbon::now())->whereBetween('time', ['06:00:00', '11:59:00']);

                    });
                    break;
                case '3':
                    $query->whereHas('timeSlots', function ($query) {
                        $query->where('date', '>', Carbon::now())->whereBetween('time', ['12:00:00', '17:59:00']);

                    });
                    break;
                case '4':
                    $query->whereHas('timeSlots', function ($query) {
                        $query->where('date', '>', Carbon::now())->whereBetween('time', ['18:00:00', '20:59:00']);

                    });
                    break;
                case '5':
                    $query->whereHas('timeSlots', function ($query) {
                        $query->where('date', '>', Carbon::now())->whereBetween('time', ['21:00:00', '23:59:00']);

                    });
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }
        if ($request->has('reserve')) {
            $hours = floatval($request->input('reserve')); // 0.5, 1, 3
            $now = Carbon::now();
            $end = $now->copy()->addHours($hours);

            $query->whereHas('timeSlots', function ($q) use ($now, $end) {
                $q->whereRaw("STR_TO_DATE(CONCAT(date, ' ', time), '%Y-%m-%d %H:%i:%s') BETWEEN ? AND ?", [$now, $end]);
            });
        }


        if ($request->filled('placement')) {
            $placement = $request->input('placement');
            $query->where('placement',  $placement );
        }
        if ($request->filled('trial')) {
            $trial = $request->input('trial');
            $query->where('trial',  $trial );
        }


        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($request->filled('accents')) {
            $accentId = $request->input('accents');
            $query->whereHas('accents', function ($q) use ($accentId) {
                $q->where('accents.id', $accentId);
            });
        }
        if ($request->filled('period')) {
            $period = $request->input('period');
            $query->whereHas('timeSlots', function ($q) use ($period) {
                $q->timeOfDay($period);
            });
        }


        if ($request->filled('language_levels')) {
            $languageLevelId = $request->input('language_levels');
            $query->whereHas('languageLevels', function ($q) use ($languageLevelId) {
                $q->where('language_levels.id', $languageLevelId);
            });
        }

        if ($request->filled('learning_goals')) {
            $learningGoalId = $request->input('learning_goals');
            $query->whereHas('learningGoals', function ($q) use ($learningGoalId) {
                $q->where('professor_learning_goals.id', $learningGoalId);
            });
        }

        if ($request->filled('age_groups')) {
            $ageGroupId = $request->input('age_groups');
            $query->whereHas('ageGroups', function ($q) use ($ageGroupId) {
                $q->where('age_groups.id', $ageGroupId);
            });
        }


        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('is_native')) {
            $query->where('is_native', filter_var($request->is_native, FILTER_VALIDATE_BOOLEAN));
        }
        if ($request->filled('min_experience') || $request->filled('max_experience')) {
            $min = $request->input('min_experience', 0);
            $max = $request->input('max_experience', 100); // یا یه مقدار بزرگ پیش‌فرض

            $query->whereBetween('years_of_experience', [$min, $max]);
        }

        $query->where('is_active', true);

        $professors = $query->with('user')->paginate(15);

        $professors->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->user->name ?? $item->name ?? null,
                'image' => $item->user->profile ?? null,
                'near' => null,
            ];
        });

        return api_response($professors);
    }

    public function getFilters(Request $request)
    {
        $lang = $request->header('X-Language', 'en');

        $accents = Accent::all()->map(function ($item) use ($lang) {
            return [
                'value' => $item->id,
                'name' => $lang === 'zh' ? ($item->title_ch ?? $item->title) : $item->title,
            ];
        });

        $ageGroups = AgeGroup::all()->map(function ($item) use ($lang) {
            return [
                'value' => $item->id,
                'name' => $lang === 'zh' ? ($item->title_ch ?? $item->title) : $item->title,
            ];
        });
        $languageLevels = LanguageLevel::all()->map(function ($item) use ($lang) {
            return [
                'value' => $item->id,
                'name' => $lang === 'zh' ? ($item->title_ch ?? $item->title) : $item->title,
            ];
        });
        $learningGoals = LearningSubgoal::with(['goal'])
            ->get()
            ->groupBy(function ($learningGoal) use ($lang) {
                $goal = $learningGoal->goal;
                if (!$goal) return null;
                return $lang === 'zh' ? ($goal->title_ch ?? $goal->title) : $goal->title;
            })
            ->map(function ($learningGoalsGroup) use ($lang) {
                $firstGoal = $learningGoalsGroup->first()->goal;

                return [
                    'goal_id' => $firstGoal->id,
                    'goal_title' => $lang === 'zh' ? ($firstGoal->title_ch ?? $firstGoal->title) : $firstGoal->title,
                    'subgoals' => $learningGoalsGroup->map(function ($learningGoal) use ($lang) {
                        return [
                            'value' => $learningGoal->id,
                            'name' => $lang === 'zh' ? ($learningGoal->title_ch ?? $learningGoal->title) : $learningGoal->title,
                        ];
                    })->values(),
                ];
            })
            ->values()
            ->toArray();


        $maxExperience = Professor::max('years_of_experience');

        return response()->json([
            'accents' => $accents,
            'age_groups' => $ageGroups,
            'language_levels' => $languageLevels,
            'learning_goals' => $learningGoals,
            'max_experience' => $maxExperience,

        ]);
    }

    public function showPrivate(Request $request, $id)
    {
        $professor = Professor::find($id);

        $professorSubgoals = $professor->learningGoals;
        $point = [];
        foreach ($professorSubgoals as $learningGoal) {
            $subgoal = $learningGoal->subgoal;
            if (!$subgoal || !$subgoal->goal) {
                continue;
            }
            $goal = $subgoal->goal;
            if (!isset($point[$goal->title])) {
                $point[$goal->title] = [];
            }
            $point[$goal->title][$subgoal->sub][] = $subgoal->title;
        }
        $lang = $request->header('X-Language', 'en'); // مثلا 'ch' برای چینی

        $accents = $professor->accents()->get()->map(fn($item) => $lang === 'zh' ? ($item->title_ch ?? $item->title) : $item->title
        )->toArray();

        $ageGroups = $professor->ageGroups()->get()->map(fn($item) => $lang === 'zh' ? ($item->title_ch ?? $item->title) : $item->title
        )->toArray();

        $languageLevels = $professor->languageLevels()->get()->map(fn($item) => $lang === 'zh' ? ($item->title_ch ?? $item->title) : $item->title
        )->toArray();
        $group = GroupClass::where('professor_id', $professor->id)->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'image' => $item->image,
                'time' => $item->time,
                'date' => Carbon::parse($item->date)->format('d F '),
                'name' => $item->name,
                'professor' => $item->professor->name,
                'profile' => $item->professor->user->profile,


            ];
        });
        $story = Story::where('professor_id', $professor->id)->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'image' => $item->cover_image,
                'like_count' => $item->likes()->count(),
                'comment_count' => $item->comments()->count(),


            ];
        });
        $webinar = Webinar::where('professor_id', $professor->id)->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'image' => $item->image,
                'time' => $item->time,
                'date' => Carbon::parse($item->date)->format('d F '),
                'name' => $item->name,
                'professor' => $item->professor->name,
                'profile' => $item->professor->user->profile,

            ];
        });
        $return = [
            'id' => $professor->id,
            'name' => $professor->name,
            'image' => $professor->user->profile,
            'is_verified' => $professor->is_verified,
            'accents' => $accents,
            'age_groups' => $ageGroups,
            'language_levels' => $languageLevels,
            'sample_video' => $professor->sample_video,
            'view_count' => (int)$professor->view_count,
            'experience' => $professor->created_at,
            'point' => $point,
            'group' => $group,
            'story' => $story,
            'webinar' => $webinar,
            'sample_video_cover' => $professor->sample_video_cover,
            'books' => $professor->books()->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->name,
                    'images' => $item->image ?? null,
                ];
            }),
            'platforms' => $professor->platforms()->select(['icon', 'title'])->get()->makeHidden('pivot'),
            'is_like' => $professor->is_like,
            'avg_rate' => (int)$professor->ratings()->avg('rating') ?? 0,
            'all_rate' => $professor->ratings()->count(),
            'rate' => $professor->rate,
            'teaching_video' => $professor->teaching_video,
            'teaching_video_cover' => $professor->teaching_video_cover,
            'available' => $professor->nearest_open_time,

            'placement' => 1,
            'trial' => 2,
            'number_student' => 0,
            'number_webinar' => 0,
            'number_group' => 0,
            'number_private' => 0,
        ];

        return api_response($return);

    }

    public function times(Request $request, $id)
    {
        $request->validate([
            'start_date' => 'required|date',
        ]);
        $startDate = $request->start_date;
        $endDate = date('Y-m-d', strtotime($startDate . ' +6 days'));

        $timeSlots = ProfessorTimeSlot::where('professor_id', $id)
            // ->whereDate('date', '>=', today())
            // ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy('date');

        // timezone کاربر از روی IP
//        $timezone = geoip()->getLocation($request->ip())->timezone ?? 'UTC';

        $formattedSlots = $this->formatTimeSlots($timeSlots, $timezone ?? 'Asia/Tehran');

        return api_response($formattedSlots);
    }

    private function formatTimeSlots($timeSlots, $timezone)
    {
        $daysOfWeek = [
            'Saturday' => 'Saturday',
            'Sunday' => 'Sunday',
            'Monday' => 'Monday',
            'Tuesday' => 'Tuesday',
            'Wednesday' => 'Wednesday',
            'Thursday' => 'Thursday',
            'Friday' => 'Friday',
        ];

        $orderedDays = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $formattedOutput = [];

        foreach ($timeSlots as $date => $slots) {
            foreach ($slots as $slot) {
                // تاریخ و ساعت ذخیره شده در UTC
                $utcDateTime = Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $slot->date . ' ' . $slot->time,
                    'UTC'
                );

                // تبدیل به timezone کاربر
                $localDateTime = $utcDateTime->clone()->setTimezone($timezone);

                $dayOfWeekEnglish = $localDateTime->format('l');
                $dayOfWeekEnglishNormalized = $daysOfWeek[$dayOfWeekEnglish];

                if (!isset($formattedOutput[$dayOfWeekEnglishNormalized])) {
                    $formattedOutput[$dayOfWeekEnglishNormalized] = [];
                }

                $formattedOutput[$dayOfWeekEnglishNormalized][] = [
                    'id' => $slot->id,
                    'time' => $localDateTime->format('H:i'),
                    'status' => $slot->status,
                    'date' => $localDateTime->format('Y-m-d'),
                ];
            }
        }

        // مرتب‌سازی خروجی بر اساس روز هفته
        $sortedOutput = [];
        foreach ($orderedDays as $day) {
            if (isset($formattedOutput[$day])) {
                $sortedOutput[$day] = $formattedOutput[$day];
            }
        }

        return $sortedOutput;
    }

    private function showSelectedOnly($professorId)
    {
        // گرفتن subgoalهایی که استاد انتخاب کرده
        $professorSelectedGoals = ProfessorLearningGoal::where('professor_id', $professorId)
            ->pluck('subgoal_id')
            ->toArray();

        // گرفتن subgoalها به همراه goal
        $selectedSubgoals = LearningSubgoal::with('goal')
            ->whereIn('id', $professorSelectedGoals)
            ->get()
            ->groupBy('goal.id');

        $data = [
            "goals" => [],
        ];

        // گرفتن همه goalها از جدول
        $goals = LearningGoal::whereIn('id', $selectedSubgoals->keys())
            ->get();

        foreach ($goals as $goal) {
            $goalData = [
                "id" => $goal->id,
                "title" => $goal->title,   // از دیتابیس
                "content" => [],
            ];

            // اگر این goal انتخاب شده باشه
            if ($selectedSubgoals->has($goal->id)) {
                $subgoals = $selectedSubgoals[$goal->id]->groupBy('sub');

                foreach ($subgoals as $subTitle => $groupedSubgoals) {
                    $subgroupData = [
                        "title" => $goal->title, // 👈 اینجا اسم خود هدف رو بذار
                        "data" => [],
                    ];

                    foreach ($groupedSubgoals as $subgoal) {
                        $subgroupData["data"][] = [
                            "title" => $subgoal->title,
                            "id" => $subgoal->id,
                        ];
                    }

                    $goalData["content"][] = $subgroupData;
                }
            }

            $data[] = $goalData;
        }

        $data = array_filter($data);       // حذف خالی‌ها
        return array_values($data);
    }

    public function details($id)
    {
        $professor = Professor::with([
            'accents:id,title',
            'ageGroups:id,title',
            'languageLevels:id,title',
            'platforms:id,title',
            'books:id,image,name',
            'user:id,profile'
        ])->findOrFail($id);

        return api_response([
            'id' => $professor->id,
            'professor' => [
                'id' => $professor->id,
                'name' => $professor->name,
                'profile' => $professor->user->profile,
                'avg_rate' => (int)$professor->ratings()->avg('rating') ?? 0,
                'all_rate' => $professor->ratings()->count(),
                'rate' => $professor->rate,

            ],
            'name' => $professor->user->name ?? null,
            'accents' => $professor->accents->makeHidden('pivot'),
            'age_groups' => $professor->ageGroups->makeHidden('pivot'),
            'language_levels' => $professor->languageLevels->makeHidden('pivot'),
            'platforms' => $professor->platforms->makeHidden('pivot'),
            'books' => $professor->books->makeHidden('pivot'),
            'class_types_details' => [
                [
                    'id' => 'sessional',
                    'title' => 'Select Sessions',
                    'content' => [
                        [
                            'title' => 'Sessions number',
                            'data' => null
                        ]
                    ],

                ],
                [
                    'id' => 'trial',
                    'title' => 'trial',
                ],
            ],
            'learningGoals' => $this->showSelectedOnly($professor->id),
        ], 'ok');
    }

    public function details_placement($id)
    {
        $professor = Professor::with([
            'accents:id,title',
            'languageLevels:id,title',
            'platforms:id,title',
            'skills:id,name',        // اضافه کردن مهارت‌ها

            'user:id,profile'
        ])->findOrFail($id);

        return api_response ([
            'id' => $professor->id,
            'professor' => [
                'id' => $professor->id,
                'name' => $professor->name,
                'profile' => $professor->user->profile,
                'avg_rate' => (int)$professor->ratings()->avg('rating') ?? 0,
                'all_rate' => $professor->ratings()->count(),
                'rate' => $professor->rate,

            ],
            'accents' => $professor->accents->makeHidden('pivot'),
            'language_levels' => $professor->languageLevels->makeHidden('pivot'),
            'platforms' => $professor->platforms->makeHidden('pivot'),
            'skills' => $professor->skills->makeHidden('pivot'),  // اضافه کردن مهارت‌ها به خروجی

        ], 'ok');
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'professor_id' => 'required|exists:professors,id',
            'age_group_id' => 'nullable|exists:age_groups,id',
            'language_level_id' => 'nullable|exists:language_levels,id',
            'platform_id' => 'nullable|exists:platforms,id',
            'subgoal_id' => 'nullable|exists:learning_subgoals,id',
            'description' => 'nullable|string',
            'class_type' => 'required|in:trial,placement,sessional',
            'sessions_count' => 'nullable|integer|min:1',
            'skill_id' => 'nullable',
        ]);
        $user = auth()->user()->student;
        $validated['user_id'] = $user->id;

        if ($user && $user->birth_date) {
            $age = \Carbon\Carbon::parse($user->birth_date)->age;

            if ($age <= 12) {
                $validated['age_group_id'] = 1;
            } elseif ($age <= 18) {
                $validated['age_group_id'] = 2;
            } else {
                $validated['age_group_id'] = 3;
            }
        }
        $bookData = $request->validate([
            'selection_type' => 'nullable|string',
            'link' => 'nullable|string',
            'file' => 'nullable|string',
            'book_id' => 'nullable|integer',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
        ]);

        $timeSlotIds = $request->input('times');

        $reservation = PrivateClassReservation::create($validated);
        if (!empty($bookData)) {
            $bookData['private_class_reservation_id'] = $reservation->id;
            ReservedBook::create($bookData);
        }

        // رزرو تایم‌اسلات‌ها

        $this->reserveTimeSlots($reservation->id, $timeSlotIds);

        return api_response(['data' =>$reservation->id]);
    }
    public function store2(Request $request , $id)
    {
        $userId = auth()->id();

        $result = PlanHelper::reserveClass($userId, 'one_to_one', $request , $id);

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 422);
        }

        return api_response([],'success');
    }
    public function reserveTimeSlots($reservationId, array $timeSlotIds)
    {
        $reservedSlots = PrivateProfessorTimeSlot::whereIn('professor_time_slot_id', $timeSlotIds)->get();

        $expiredSlots = [];

        foreach ($reservedSlots as $reservedSlot) {
            $reservation = $reservedSlot->reservation;

            if (
                $reservation &&
                $reservation->status === 'pending' &&
                $reservation->created_at->lt(now()->subMinutes(10))
            ) {
                $expiredSlots[] = $reservedSlot->id;
            }
        }

        if (!empty($expiredSlots)) {
            PrivateProfessorTimeSlot::whereIn('id', $expiredSlots)->delete();
        }

        $stillReserved = PrivateProfessorTimeSlot::whereIn('professor_time_slot_id', $timeSlotIds)->exists();

        if ($stillReserved) {
            return api_response(null, 'Some of the selected time slots are already reserved.');
        }
        $slots = ProfessorTimeSlot::whereIn('id', $timeSlotIds)
            ->orderBy('date')
            ->orderBy('time')
            ->get();
        $data = $slots->values()->map(function ($slot, $index) use ($reservationId) {

            return [
                'private_class_reservation_id' => $reservationId,
                'professor_time_slot_id' => $slot->id,
                'session_number' => $index + 1, // شماره جلسه بر اساس ترتیب
                'status' => 'Upcoming',
                'date' => $slot->date,
                'time' => $slot->time,
            ];
        });

        PrivateProfessorTimeSlot::insert($data->toArray());

        ProfessorTimeSlot::whereIn('id', $timeSlotIds)->update(['status' => 'reserved']);

        return api_response(null, 'Time slots reserved successfully.');
    }

    public function info($id)
    {
        $reserve = PrivateClassReservation::find($id);
        $timeSlots = PrivateProfessorTimeSlot::where('private_class_reservation_id', $id)->get();
        $grouped = [];
        $user  = auth()->user();

        foreach ($timeSlots as $slot) {
            // تبدیل تاریخ به روز هفته به انگلیسی
            $dayOfWeek = Carbon::parse($slot->date)->format('l'); // Saturday, Sunday, ...

            if (!isset($grouped[$dayOfWeek])) {
                $grouped[$dayOfWeek] = [];
            }

            $grouped[$dayOfWeek][] = [
                'time' => $slot->time->format('H:i'),
                'id' => $slot->id,
                'date' => $slot->date,

            ];
        }

        $orderedDays = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $sortedOutput = [];
        foreach ($orderedDays as $day) {
            if (isset($grouped[$day])) {
                $sortedOutput[$day] = $grouped[$day];
            }
        }
        $book = $reserve->reservedBooks()->first();
        $user_plan = UserPlan::where('user_id', $user->id)->where('is_active', 1)->whereRelation('plan', 'plan_type', 'one_to_one')
            ->where('class_count','>',0)->where('expires_at', '>=', Carbon::now())->first();
        $days_left = null;
        $hours_left = null;
        if ($user_plan) {
            $now = Carbon::now();
            $end = Carbon::parse($user_plan->expires_at);
            $days_left = $now->diffInDays($end);
            $hours_left = $now->diffInHours($end);

        }
        $return = [
            'id' => $reserve->id,
            'books' => [
                'name' =>$book?->book?->name,
                'selection_type' => $book?->selection_type,
                'image' => $book?->image,
                'username' => $book?->username,
                'password' => $book?->password,
                'link' => $book?->link,
                'file' => $book?->file,
            ],
            'professor' => [
                'id' => $reserve->professor->id,
                'name' => $reserve->professor->name,
                'profile' => $reserve->professor->user->profile,
                'avg_rate' => (int)$reserve->professor->ratings()->avg('rating') ?? 0,
                'all_rate' => $reserve->professor->ratings()->count(),
                'rate' => $reserve->professor->rate,

            ],
            'has_plan' => $user_plan ? true : false,
            'days_left' =>(int)$days_left ?? null ,
            'hours_left' => (int)$hours_left ?? null,
            'color' => $user_plan?->plan?->color ?? null,
            'plan_name' => $user_plan?->plan->name ?? null,
            'class_count' => $user_plan?->plan->class_count ?? null,
            'class_left' => $user_plan?->class_count ?? 0,

            'sessions_count' => $reserve->sessions_count,
            'class_type' => $reserve->class_type,
            'discount_code' => $reserve->discount_code,
            'description' => $reserve->description,
            'ageGroup' => $reserve->ageGroup->title,
            'languageLevel' => $reserve->languageLevel->title,
            'platform' => $reserve->platform->title,
            'skill' => $reserve?->skill?->title,
            'subgoal' => $reserve?->subgoal?->goal?->title . ' (' . $reserve?->subgoal?->title . ')' ?? null,
            'time' => $sortedOutput,

        ];
        return api_response($return);
    }

    public function toggleLike(Request $request, $id)
    {
        $group = Professor::findOrFail($id);
        $user = auth()->user();

        if (!$user) {
            return api_response([], 'برای لایک کردن لاگین کنید', 401);
        }

        $like = $user->likes()
            ->where('likeable_id', $group->id)
            ->where('likeable_type', Professor::class)
            ->first();

        if ($like) {
            $like->delete();
            return api_response([], 'لایک حذف شد');
        }

        $group->likes()->create([
            'user_id' => $user->id,
        ]);

        return api_response([], 'لایک شد');
    }

    public function comment(Request $request, $id)
    {

        $request->validate([
            'body' => 'nullable|string|min:1|max:400', // حداقل طول 1 برای جلوگیری از رشته خالی
            'video_url' => 'nullable|file',
            'voice_url' => 'nullable|file',
        ], [
            'body.min' => 'توضیحات نباید خالی باشد.',
            'body.required_without_all' => 'توضیحات یا ویدیو یا ویس باید ارسال شود.',
            'video_url.required_without_all' => 'توضیحات یا ویدیو یا ویس باید ارسال شود.',
            'voice_url.required_without_all' => 'توضیحات یا ویدیو یا ویس باید ارسال شود.',
        ], [
            'body' => 'توضیحات',
            'video_url' => 'ویدیو',
            'voice_url' => 'ویس',
        ]);


        $groupClass = Professor::findOrFail($id);

        $videoUrl = null;
        $voiceUrl = null;

        // ذخیره فایل ویدیو
        if ($request->hasFile('video_url')) {
            $videoPath = $request->file('video_url')->store('comment/videos', 'public');
            $videoUrl = $videoPath;
        }

        // ذخیره فایل صدا
        if ($request->hasFile('voice_url')) {
            $voicePath = $request->file('voice_url')->store('comment/voices', 'public');
            $voiceUrl = $voicePath;
        }

        // ساخت نظر
        $comment = $groupClass->comments()->create([
            'user_id' => auth()->user()->id,
            'body' => $request->body,
            'video_url' => $videoUrl,
            'voice_url' => $voiceUrl,
        ]);

        return api_response([
            'id' => $comment->id,
            'body' => $comment->body,
            'video_url' => $videoUrl ? asset($videoUrl) : null,
            'voice_url' => $voiceUrl ? asset($voiceUrl) : null,
            'created_at' => $comment->created_at->diffForHumans(),
        ], 'نظر با موفقیت ثبت شد');
    }

    public function showComments(Request $request, $id)
    {
        $user_id = auth()->id();

        $groupClass = Professor::findOrFail($id);


        $query = $groupClass->comments()->with([
            'user.student',
        ])->where('admin_status', 'approved');

        if ($user_id) {
            $query->with([
                'likes' => function ($q) use ($user_id) {
                    $q->where('user_id', $user_id);
                },
                'dislikes' => function ($q) use ($user_id) {
                    $q->where('user_id', $user_id);
                },
            ]);
        }

        if ($request->has('type')) {
            $type = $request->input('type');

            if ($type == 'text') {
                $query->whereNull('video_url')->whereNull('voice_url');
            } elseif ($type == 'video') {
                $query->whereNotNull('video_url')->whereNull('body');
            } elseif ($type == 'voice') {
                $query->whereNotNull('voice_url')->whereNull('body');
            }
        }

        $comments = $query->latest()->paginate();

        $comments->getCollection()->transform(function ($comment) use ($user_id) {
            return [
                'id' => $comment->id,
                'content' => $comment->body ?? null,
                'video_url' => $comment->video_url ? $comment->video_url : null,
                'voice_url' => $comment->voice_url ? $comment->voice_url : null,
                'created_at' => $comment->created_at->toDateTimeString(),
                'user' => [
                    'user_name' => optional($comment->user)->name ?? null,
                    'image' => $comment->user->profile
                ],
                'likes_count' => $comment->likes->count(),
                'dislikes_count' => $comment->dislikes->count(),
                'is_like' => $user_id ? $comment->likes->isNotEmpty() : false,
                'is_dislike' => $user_id ? $comment->dislikes->isNotEmpty() : false,
            ];
        });

        return api_response($comments, 'ok');
    }

    public function rate(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user) {
            return api_response([], 'برای امتیازدهی باید لاگین کنید', 401);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $classGroup = Professor::findOrFail($id);

        $existingRating = $user->ratings()
            ->where('ratable_id', $classGroup->id)
            ->where('ratable_type', Professor::class)
            ->where('user_id', $user->id)
            ->first();

        if ($existingRating) {
            $existingRating->rating = $request->input('rating');
            $existingRating->save();
            return api_response([], '');
        }

        $classGroup->ratings()->create([
            'user_id' => $user->id,
            'rating' => $request->input('rating'),
        ]);

        return api_response([], '');
    }




    //////////////////////
    ///

    public function index(Request $request)
    {
        $professor_id = $request->input('professor_id');
        $level = LanguageLevel::all();
        $skill = Skill::all();
        if ($professor_id) {
            $professor = \App\Models\Professor::find($professor_id);
            $platform = $professor->platforms()->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                ];
            });


        }
        return api_response([
            'level' => $level,
            'skill' => $skill,
            'platform' => $platform ?? null,

        ]);
    }

    public function professor_placement(Request $request)
    {
        $level = $request->input('language_level_id');
        $skill = $request->input('skill_id');
        $user = auth()->user()->student;
        $age = $user->age_group;
        $professors = \App\Models\Professor::query();

        if ($age) {
            $professors->whereHas('ageGroups', function($q) use ($age) {
                $q->where('age_group_id', $age);
            });
        }

        if ($level) {
            $professors->whereHas('languageLevels', function($q) use ($level) {
                $q->where('language_level_id', $level);
            });
        }

        if ($skill) {
            $professors->whereHas('skills', function($q) use ($skill) {
                $q->where('skill_id', $skill);
            });
        }
        $professor = $professors->with(['user'])->paginate(10);
        $professor->getCollection()->transform(function($item){
            return [
                'name'=> $item->user->name,
                'id'=> $item->id,
                'profile' => $item->user->profile ?? null,
            ];
        });

        return api_response($professor);

    }
}
