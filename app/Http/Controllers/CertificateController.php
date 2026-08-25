<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\CertificateService;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function generate(Course $course, CertificateService $certificates): View
    {
        $enrollment = auth()->user()->courses()->where('course_id', $course->id)->first();

        abort_if(! $enrollment?->pivot->completed_at, 403, 'Sertifikat olish uchun kursni tugatgan bo‘lishingiz kerak.');

        $code = $enrollment->pivot->certificate_code
            ?? tap($certificates->issue($course, auth()->user()), function (string $issued) use ($enrollment, $course, $certificates) {
                $course->students()->updateExistingPivot(auth()->id(), ['certificate_code' => $issued]);
            });

        return view('certificate.show', [
            'user' => auth()->user(),
            'course' => $course,
            'code' => $code,
        ]);
    }

    public function verify(string $code, CertificateService $certificates): View
    {
        $resolved = $certificates->resolve(trim($code));

        return view('certificate.verify', [
            'certificate' => $resolved ? ['course' => $resolved[0], 'user' => $resolved[1]] : null,
            'code' => trim($code),
        ]);
    }
}
