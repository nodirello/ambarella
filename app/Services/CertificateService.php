<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Str;

class CertificateService
{
    public function issue(Course $course, User $user): string
    {
        return sprintf(
            'AMB-%04d-%s',
            $course->id,
            Str::upper(Str::random(12))
        );
    }

    /**
     * @return array{0: Course, 1: User}|null [course, user] when the code is valid
     */
    public function resolve(string $code): ?array
    {
        $enrollment = \DB::table('course_user')
            ->where('certificate_code', strtoupper(trim($code)))
            ->whereNotNull('completed_at')
            ->first();

        if (! $enrollment) {
            return null;
        }

        $course = Course::find($enrollment->course_id);
        $user = User::find($enrollment->user_id);

        return $course && $user ? [$course, $user] : null;
    }
}
