@extends('layouts.public_theme')

    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        .page-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .toolbar {
            background-color: #3c4043;
            padding: 10px 15px;
            border-radius: 4px 4px 0 0;
            color: #f8f9fa;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .toolbar h3 {
            margin: 0;
        }
        .toolbar-buttons button {
            background-color: #f8f9fa;
            border: 1px solid #dadce0;
            border-radius: 4px;
            color: #3c4043;
            cursor: pointer;
            font-size: 14px;
            height: 36px;
            padding: 0 16px;
            margin-left: 8px;
        }
        .toolbar-buttons button:hover {
            background-color: #f1f3f4;
            border-color: #d2d5d9;
        }
        .viewer-container {
            width: 100%;
            height: 75vh;
            background-color: #fff;
            border-radius: 0 0 4px 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        iframe {
            width: 100%;
            height: 100%;
            border: 3px solid #3c4043;
            border-radius: 0px;
        }
    </style>

@section('content')
    <div class="page-container">
        <div class="toolbar">
            <div>
                <h3>Document Viewer</h3>
                <span>Refresh if document doesn't load properly</span>
            </div>
            <div class="toolbar-buttons">
                <button id="refreshBtn" title="Refresh document">↻ Refresh</button>
                <button id="downloadBtn">Download</button>
                <button id="closeBtn">Close</button>
            </div>
        </div>
        <div class="viewer-container">
            <div id="loadingIndicator" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); padding: 15px; background: rgba(255, 255, 255, 0.9); border-radius: 4px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); z-index: 10;">Loading document...</div>
            <iframe id="pdfFrame" src="" style="width: 100%; height: 100%; border: 3px solid #3c4043;"></iframe>
        </div>
    </div>
@endsection

@section('customscript')
<script src="https://mozilla.github.io/pdf.js/build/pdf.js"></script>

<script>
    // Get file ID from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const fileId = urlParams.get('id');
    const pdfFrame = document.getElementById('pdfFrame');
    const loadingIndicator = document.getElementById('loadingIndicator');

    // Function to load the document
    function loadDocument() {
        if (fileId) {
            // Show loading indicator
            loadingIndicator.style.display = 'block';
            pdfFrame.style.opacity = '0.3';

            // Add timestamp to prevent caching
            const timestamp = new Date().getTime();
            const previewUrl = `https://drive.google.com/file/d/${fileId}/preview?t=${timestamp}`;

            // Set iframe source
            pdfFrame.src = previewUrl;

            // Handle iframe load event
            pdfFrame.onload = function() {
                setTimeout(() => {
                    loadingIndicator.style.display = 'none';
                    pdfFrame.style.opacity = '1';
                }, 700); // Small delay to ensure content is fully rendered
            };
        } else {
            loadingIndicator.textContent = 'No document ID provided.';
            loadingIndicator.style.display = 'block';
        }
    }

    // For refresh button
    document.getElementById('refreshBtn').addEventListener('click', function() {
        loadDocument();
    });

    // For download button
    document.getElementById('downloadBtn').addEventListener('click', function() {
        if (fileId) {
            const downloadUrl = `https://drive.google.com/uc?export=download&id=${fileId}`;
            window.open(downloadUrl, '_blank');
        }
    });

    // For close button
    document.getElementById('closeBtn').addEventListener('click', function() {
        window.close();
    });

    // Initial load
    loadDocument();
</script>
@endsection
