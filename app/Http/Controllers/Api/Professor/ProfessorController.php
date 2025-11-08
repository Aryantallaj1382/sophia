<?php

namespace App\Http\Controllers\Api\Professor;

use App\Http\Controllers\Controller;
use App\Models\GroupClass;
use App\Models\Professor;
use App\Models\Story;
use App\Models\Webinar;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProfessorController extends Controller
{
    public function showPrivate(Request $request)
    {
        $user = auth()->user()->id;
        $professor = Professor::find($user);

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
                'cover' => $item->cover_image,
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
    public function get_user()
    {
        $user = auth()->user();
        return api_response([
            'balance' => $user?->wallet?->balance,
            'name' => $user?->name,
            'profile' => $user->profile,
            'user_id' => $user->id,
            'id' => $user->professor?->id,
        ]);
    }
}
