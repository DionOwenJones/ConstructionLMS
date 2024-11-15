<!DOCTYPE html>
<html>
<head>
    <title>Course Completion Certificate</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        }
        .certificate-container {
            width: 100%;
            height: 100vh;
            padding: 40px;
            box-sizing: border-box;
            position: relative;
            background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMCAwaDEwMHYxMDBIMHoiIGZpbGw9IiNmMWYxZjEiIGZpbGwtb3BhY2l0eT0iMC4yIi8+PC9zdmc+');
        }
        .certificate {
            background: white;
            border: 2px solid #1a1a1a;
            padding: 50px;
            box-shadow: 0 0 40px rgba(0,0,0,0.1);
            position: relative;
            height: calc(100vh - 80px);
            border-radius: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 60px;
        }
        .logo {
            margin-bottom: 20px;
        }
        .title {
            font-size: 48px;
            color: #1a1a1a;
            margin-bottom: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .subtitle {
            font-size: 24px;
            color: #666;
            margin-bottom: 40px;
        }
        .content {
            text-align: center;
            margin-bottom: 60px;
        }
        .recipient {
            font-size: 36px;
            color: #1a1a1a;
            margin: 20px 0;
            font-weight: 600;
        }
        .course-name {
            font-size: 28px;
            color: #333;
            margin: 20px 0;
            font-style: italic;
        }
        .completion-date {
            font-size: 20px;
            color: #666;
            margin: 20px 0;
        }
        .footer {
            position: absolute;
            bottom: 50px;
            left: 50px;
            right: 50px;
            text-align: center;
        }
        .certificate-number {
            font-size: 14px;
            color: #999;
            margin-top: 20px;
        }
        .signature-line {
            width: 200px;
            height: 1px;
            background: #1a1a1a;
            margin: 10px auto;
        }
        .signature-title {
            font-size: 16px;
            color: #666;
        }
        .border-pattern {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 2px solid #f0f0f0;
            border-radius: 5px;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate">
            <div class="border-pattern"></div>
            <div class="header">
                <div class="logo">
                    <!-- Your logo here -->
                </div>
                <h1 class="title">Certificate of Completion</h1>
                <div class="subtitle">This certifies that</div>
            </div>

            <div class="content">
                <div class="recipient">{{ $user_name }}</div>
                <div class="subtitle">has successfully completed</div>
                <div class="course-name">{{ $course_name }}</div>
                <div class="completion-date">Completed on {{ $completion_date }}</div>
            </div>

            <div class="footer">
                <div class="signature-line"></div>
                <div class="signature-title">Course Instructor</div>
                <div class="certificate-number">Certificate ID: {{ $certificate_number }}</div>
            </div>
        </div>
    </div>
</body>
</html>
