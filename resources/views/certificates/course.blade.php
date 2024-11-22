<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Course Completion Certificate</title>
    <style>
        body {
            margin: 0;
            padding: 40px;
            width: 1920px;
            height: 1080px;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        .certificate {
            width: 100%;
            height: 100%;
            border: 20px solid #1a365d;
            padding: 40px;
            text-align: center;
            position: relative;
        }
        .header {
            margin-bottom: 60px;
        }
        .title {
            font-size: 48px;
            color: #1a365d;
            margin: 20px 0;
            text-transform: uppercase;
            font-weight: bold;
        }
        .content {
            margin: 60px 0;
        }
        .name {
            font-size: 36px;
            color: #2d3748;
            margin: 30px 0;
            font-weight: bold;
        }
        .course-title {
            font-size: 32px;
            color: #2d3748;
            margin: 30px 0;
        }
        .text {
            font-size: 24px;
            color: #4a5568;
            margin: 20px 0;
            line-height: 1.5;
        }
        .footer {
            position: absolute;
            bottom: 60px;
            left: 0;
            right: 0;
            text-align: center;
        }
        .signature {
            margin: 20px 0;
            font-size: 24px;
            color: #2d3748;
        }
        .certificate-id {
            position: absolute;
            bottom: 20px;
            right: 40px;
            font-size: 16px;
            color: #718096;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="header">
            <h1 class="title">Certificate of Completion</h1>
        </div>

        <div class="content">
            <p class="text">This is to certify that</p>
            <p class="name">{{ $user->name }}</p>
            <p class="text">has successfully completed the course</p>
            <p class="course-title">"{{ $course->title }}"</p>
            <p class="text">Completed on {{ $completedAt->format('F j, Y') }}</p>
        </div>

        <div class="footer">
            <div class="signature">
                <p>Course Director</p>
                <p>Construction LMS</p>
            </div>
        </div>

        <div class="certificate-id">Certificate ID: {{ $certificate_number }}</div>
    </div>
</body>
</html>
