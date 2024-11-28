<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Course Completion Certificate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .certificate {
            width: 1920px;
            height: 1080px;
            position: relative;
            padding: 50px;
            box-sizing: border-box;
            background: linear-gradient(45deg, #f6f6f6 25%, #ffffff 25%, #ffffff 50%, #f6f6f6 50%, #f6f6f6 75%, #ffffff 75%, #ffffff 100%);
            background-size: 20px 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-top: 40px;
        }
        .title {
            font-size: 48px;
            color: #2d3748;
            margin-bottom: 20px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .content {
            text-align: center;
            margin-bottom: 40px;
        }
        .text {
            font-size: 24px;
            color: #4a5568;
            margin: 10px 0;
        }
        .name {
            font-size: 36px;
            color: #2d3748;
            margin: 20px 0;
            font-weight: bold;
        }
        .course-title {
            font-size: 30px;
            color: #2d3748;
            margin: 20px 0;
            font-style: italic;
        }
        .signature {
            margin: 20px 0;
            font-size: 24px;
            color: #2d3748;
        }
        .certificate-id {
            position: absolute;
            bottom: 40px;
            right: 40px;
            font-size: 16px;
            color: #718096;
        }
        .expiry-info {
            position: absolute;
            bottom: 40px;
            left: 40px;
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
        
        @if(isset($expiryDate))
            <div class="expiry-info">
                Valid until: {{ $expiryDate }}
            </div>
        @endif
    </div>
</body>
</html>
