<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Course Completion Certificate</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #333;
            line-height: 1.6;
            text-align: center;
            padding: 40px;
            background: #fff;
        }
        .certificate {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #2c5282;
            padding: 40px;
            position: relative;
            background: linear-gradient(45deg, #f8fafc 25%, transparent 25%, transparent 75%, #f8fafc 75%, #f8fafc),
                        linear-gradient(45deg, #f8fafc 25%, transparent 25%, transparent 75%, #f8fafc 75%, #f8fafc);
            background-size: 60px 60px;
            background-position: 0 0, 30px 30px;
        }
        .header {
            margin-bottom: 40px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 36px;
            color: #2c5282;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .subtitle {
            font-size: 24px;
            color: #4a5568;
            margin-bottom: 40px;
        }
        .content {
            margin-bottom: 40px;
            font-size: 18px;
        }
        .name {
            font-size: 28px;
            color: #2d3748;
            font-weight: bold;
            margin: 20px 0;
        }
        .course-name {
            font-size: 24px;
            color: #2c5282;
            font-weight: bold;
            margin: 20px 0;
        }
        .business-name {
            font-size: 20px;
            color: #4a5568;
            font-style: italic;
            margin: 20px 0;
        }
        .date {
            font-size: 18px;
            color: #4a5568;
            margin: 20px 0;
        }
        .certificate-number {
            font-size: 14px;
            color: #718096;
            position: absolute;
            bottom: 20px;
            right: 40px;
        }
        .signature {
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .signature-line {
            width: 200px;
            margin: 0 auto;
            border-top: 1px solid #000;
            margin-top: 10px;
        }
        .signature-title {
            font-size: 16px;
            color: #4a5568;
        }
        .border-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 2px solid #2c5282;
            margin: 10px;
            pointer-events: none;
        }
        .border-pattern::before {
            content: '';
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            border: 1px solid #4a5568;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="border-pattern"></div>
        
        <div class="header">
            <div class="title">Certificate of Completion</div>
            <div class="subtitle">This is to certify that</div>
        </div>

        <div class="content">
            <div class="name">{{ $user->name }}</div>
            <div class="business-name">Employee of {{ $business_name }}</div>
            <p>has successfully completed the course</p>
            <div class="course-name">{{ $course->title }}</div>
            <div class="date">Completed on {{ $completed_at }}</div>
        </div>

        <div class="signature">
            <div class="signature-line"></div>
            <div class="signature-title">Course Instructor</div>
        </div>

        <div class="certificate-number">
            Certificate Number: {{ $certificate_number }}
        </div>
    </div>
</body>
</html>
