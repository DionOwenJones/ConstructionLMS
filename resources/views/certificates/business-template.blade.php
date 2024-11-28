<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Business Course Completion Certificate</title>
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
        .border-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 2px solid #e2e8f0;
            margin: 20px;
            pointer-events: none;
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
        .subtitle {
            font-size: 24px;
            color: #4a5568;
            margin: 10px 0;
        }
        .content {
            text-align: center;
            margin-bottom: 40px;
        }
        .name {
            font-size: 36px;
            color: #2d3748;
            margin: 20px 0;
            font-weight: bold;
        }
        .business-name {
            font-size: 28px;
            color: #4a5568;
            margin: 15px 0;
            font-style: italic;
        }
        .course-name {
            font-size: 30px;
            color: #2d3748;
            margin: 20px 0;
            font-style: italic;
        }
        .date {
            font-size: 24px;
            color: #4a5568;
            margin: 10px 0;
        }
        .signature {
            text-align: center;
            margin-top: 60px;
        }
        .signature-line {
            width: 200px;
            height: 2px;
            background-color: #2d3748;
            margin: 10px auto;
        }
        .signature-title {
            font-size: 24px;
            color: #2d3748;
            margin-top: 10px;
        }
        .certificate-number {
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

        @if(isset($expiryDate))
            <div class="expiry-info">
                Valid until: {{ $expiryDate }}
            </div>
        @endif
    </div>
</body>
</html>
