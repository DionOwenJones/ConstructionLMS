<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Course Completion Certificate</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
            size: landscape;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #2D3748;
            line-height: 1.4;
            background: #FFFFFF;
            width: 100%;
            position: relative;
        }
        .certificate-container {
            width: 100%;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 72px;
            color: rgba(45, 55, 72, 0.05);
            white-space: nowrap;
            z-index: 1;
        }
        .border-container {
            border: 2px solid #4A5568;
            border-radius: 12px;
            padding: 2rem;
            position: relative;
            background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(249,250,251,0.95) 100%);
            z-index: 2;
        }
        .inner-border {
            position: absolute;
            top: 10px;
            right: 10px;
            bottom: 10px;
            left: 10px;
            border: 1px solid rgba(74, 85, 104, 0.2);
            border-radius: 8px;
        }
        .content {
            position: relative;
            text-align: center;
            padding: 1rem;
            z-index: 3;
        }
        .header {
            margin-bottom: 2rem;
        }
        .logo {
            font-size: 24px;
            color: #2D3748;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 1rem;
        }
        .title {
            font-size: 42px;
            color: #2D3748;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: linear-gradient(135deg, #2D3748 0%, #4A5568 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }
        .subtitle {
            font-size: 20px;
            color: #4A5568;
            font-weight: 300;
            letter-spacing: 1px;
            margin-bottom: 1.5rem;
        }
        .name {
            font-size: 36px;
            color: #2D3748;
            font-weight: bold;
            margin: 1rem 0;
        }
        .course-name {
            font-size: 24px;
            color: #4A5568;
            font-style: italic;
            margin: 1rem 0;
        }
        .date {
            font-size: 16px;
            color: #718096;
            font-weight: 300;
            margin: 1rem 0;
        }
        .signature {
            margin-top: 1.5rem;
        }
        .signature-line {
            width: 200px;
            margin: 0.5rem auto;
            border-top: 2px solid #718096;
        }
        .signature-title {
            font-size: 14px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .accreditation {
            font-style: italic;
            color: #718096;
            margin-top: 1rem;
        }
        .certificate-footer {
            text-align: center;
            font-size: 12px;
            color: #A0AEC0;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="watermark">ERYRI CONSULTING</div>
        <div class="border-container">
            <div class="inner-border"></div>
            <div class="content">
                <div class="header">
                    <div class="logo">Eryri Consulting</div>
                    <div class="title">Certificate of Completion</div>
                    <div class="subtitle">This is to certify that</div>
                </div>
                
                <div class="name">{{ $user->name }}</div>
                
                <div class="subtitle">has successfully completed the course</div>
                
                <div class="course-name">"{{ $course->title }}"</div>
                
                <div class="date">
                    Completed on {{ \Carbon\Carbon::parse($completedAt)->format('F j, Y') }}
                </div>
                
                <div class="signature">
                    <div class="signature-line"></div>
                    <div class="signature-title">Director, Eryri Consulting</div>
                </div>

                <div class="accreditation">
                    Accredited by Eryri Consulting Professional Development
                </div>
                
                <div class="certificate-footer">
                    Verify this certificate at www.eryriconsulting.com/verify
                </div>
            </div>
        </div>
    </div>
</body>
</html>
