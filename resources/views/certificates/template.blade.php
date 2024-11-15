<!DOCTYPE html>
<html>
<head>
    <title>Course Completion Certificate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 40px;
        }
        .certificate {
            border: 20px solid #0a0a0a;
            padding: 25px;
            height: 600px;
            position: relative;
        }
        .certificate-header {
            margin-bottom: 40px;
        }
        .certificate-title {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #262626;
        }
        .student-name {
            font-size: 24px;
            margin-bottom: 20px;
            color: #262626;
        }
        .certificate-content {
            font-size: 18px;
            line-height: 1.5;
            margin-bottom: 40px;
            color: #404040;
        }
        .certificate-footer {
            position: absolute;
            bottom: 30px;
            left: 25px;
            right: 25px;
            text-align: center;
        }
        .certificate-number {
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="certificate-header">
            <h1 class="certificate-title">Certificate of Completion</h1>
        </div>

        <div class="student-name">
            This is to certify that
            <br><br>
            <strong>{{ $user_name }}</strong>
        </div>

        <div class="certificate-content">
            has successfully completed the course
            <br><br>
            <strong>{{ $course_name }}</strong>
            <br><br>
            on {{ $completion_date }}
        </div>

        <div class="certificate-footer">
            <div class="certificate-number">
                Certificate Number: {{ $certificate_number }}
            </div>
        </div>
    </div>
</body>
</html>
