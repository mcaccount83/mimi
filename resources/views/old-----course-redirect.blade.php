<!DOCTYPE html>
<html>
<head>
    <title>Redirecting to Course...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }
        .loading {
            text-align: center;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loading">
        <div class="spinner"></div>
        <p>Logging you in and redirecting to course...</p>
        @if(isset($wp_response))
            <p style="font-size: 12px; color: #666;">Status: {{ $wp_response['message'] ?? 'Processing' }}</p>
        @endif
    </div>

    <script>
        // Redirect after a brief delay to ensure cookies are set
        setTimeout(function() {
            window.location.href = '{{ $course_url }}';
        }, 1500);
    </script>
</body>
</html>
