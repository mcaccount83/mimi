@extends('layouts.public_theme')

    <title>PDF Viewer</title>
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
            <h3>Document Viewer</h3>
            <span>Refresh if document does not load properly</span>
            <div class="toolbar-buttons">
                <button id="refreshBtn" title="Refresh document">↻ Refresh</button>
                <button id="downloadBtn">Download</button>
                <button id="closeBtn">Close</button>
            </div>
        </div>
        <div class="viewer-container">
            <div id="loadingIndicator" style="display: none;">Loading document...</div>
            <iframe id="pdfFrame" src=""></iframe>
        </div>
    </div>
@endsection

@section('customscript')
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

            // For preview
            const previewUrl = 'https://drive.google.com/file/d/' + fileId + '/preview';
            pdfFrame.src = previewUrl;

            // Handle iframe load event
            pdfFrame.onload = function() {
                loadingIndicator.style.display = 'none';
                pdfFrame.style.opacity = '1';
            };

            // Handle iframe error
            pdfFrame.onerror = function() {
                loadingIndicator.textContent = 'Failed to load document. Please try refreshing.';
                loadingIndicator.style.display = 'block';
            };
        }
    }

    // Initial load
    if (fileId) {
        loadDocument();

        // Set up refresh button
        document.getElementById('refreshBtn').addEventListener('click', function() {
            loadDocument();
        });

        // For download button
        document.getElementById('downloadBtn').addEventListener('click', function() {
            const downloadUrl = 'https://drive.google.com/uc?export=download&id=' + fileId;
            window.open(downloadUrl, '_blank');
        });

        // For close button
        document.getElementById('closeBtn').addEventListener('click', function() {
            window.close();
        });
    } else {
        loadingIndicator.textContent = 'No document ID provided.';
        loadingIndicator.style.display = 'block';
    }
</script>
@endsection

