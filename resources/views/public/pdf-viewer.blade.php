@extends('layouts.public_theme')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Viewer</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            height: 100%;
        }

        .pdf-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .pdf-toolbar {
            padding: 10px;
            background-color: #f3f3f3;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
        }

        /* New toolbar sections for left, center, right layout */
        .toolbar-left, .toolbar-center, .toolbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toolbar-left {
            flex: 1;  /* Takes 1/3 of space */
        }

        .toolbar-center {
            flex: 1;  /* Takes 1/3 of space */
            justify-content: center;
        }

        .toolbar-right {
            flex: 1;  /* Takes 1/3 of space */
            justify-content: flex-end;
        }

        .pdf-viewer {
            flex: 1;
            overflow: auto;
            background-color: #525659;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 20px;
            position: relative;
        }

        #pdf-canvas {
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            background-color: white;
        }

        button {
            padding: 6px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .download-btn {
            background-color: #2196F3;  /* Different color to distinguish it */
        }

        .download-btn:hover {
            background-color: #0b7dda;
        }

        .debug-info {
            position: fixed;
            bottom: 0;
            left: 0;
            background: rgba(0,0,0,0.8);
            color: #fff;
            padding: 10px;
            font-family: monospace;
            z-index: 9999;
            max-width: 100%;
            overflow: auto;
            max-height: 200px;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 100;
            font-size: 18px;
            font-weight: bold;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 5px;
            margin: 20px auto;
            max-width: 80%;
            text-align: center;
        }
    </style>

    <!-- PDF.js library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
    <script>
        // Set the worker source
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';
    </script>
    <!-- FontAwesome -->
<script defer src="{{ config('settings.base_url') }}theme/plugins/fontawesome-free-6.7.2/js/all.js"></script>
</head>
<body>
    <div class="pdf-container">
        <div class="pdf-toolbar">
            <!-- Left section - Zoom controls -->
            <div class="toolbar-left">
                <button id="zoom-in"><i class="fas fa-magnifying-glass-plus mr-2"></i>Zoom In</button>
                <button id="zoom-out"><i class="fas fa-magnifying-glass-minus mr-2"></i>Zoom Out</button>
            </div>

            <!-- Center section - Page navigation -->
            <div class="toolbar-center">
                <button id="prev"><i class="fas fa-backward mr-2"></i>Previous</button>
                <span>Page: <span id="page_num">0</span> / <span id="page_count">0</span></span>
                <button id="next">Next<i class="fas fa-forward ml-2"></i></button>
            </div>

            <!-- Right section - Download button -->
            <div class="toolbar-right">
                <button id="download" class="download-btn"><i class="fas fa-file-pdf mr-2"></i>Download PDF</button>
            </div>
        </div>

        <div class="pdf-viewer">
            <div id="loading-overlay" class="loading-overlay">Loading PDF...</div>
            <div id="error-message" class="error-message" style="display: none;"></div>
            <canvas id="pdf-canvas"></canvas>
        </div>
    </div>

    <div id="debug-info" class="debug-info">
        <div>PDF URL: <span id="debug-url">{{ $pdfUrl }}</span></div>
        <div>Google Drive ID: <span id="debug-gdrive-id">{{ $googleDriveId }}</span></div>
        <div>Has Token: <span id="debug-has-token">{{ $googleDriveToken ? 'Yes' : 'No' }}</span></div>
        <div>Status: <span id="debug-status">Initializing...</span></div>
    </div>

    <script>
        // Store PDF data
        let pdfDoc = null;
        let pageNum = 1;
        let pageRendering = false;
        let pageNumPending = null;
        let scale = 1.5;
        const canvas = document.getElementById('pdf-canvas');
        const ctx = canvas.getContext('2d');

        // Debug elements
        const debugStatus = document.getElementById('debug-status');
        const loadingOverlay = document.getElementById('loading-overlay');
        const errorMessage = document.getElementById('error-message');

        // PDF URL or Google Drive ID
        const pdfUrl = "{{ $pdfUrl }}";
        const googleDriveId = "{{ $googleDriveId }}";
        const googleDriveToken = "{{ $googleDriveToken }}";

        // Detect if the value is a Google Drive ID (check if it looks like a Drive ID)
        // This is the key fix - we're adding logic to detect if pdfUrl is actually a Drive ID
        function detectGoogleDriveId() {
            // If googleDriveId is already set, use that
            if (googleDriveId && googleDriveId.trim() !== '') {
                return googleDriveId;
            }

            // Check if pdfUrl looks like a Google Drive ID (typically 25-35 chars, alphanumeric)
            if (pdfUrl && /^[a-zA-Z0-9_-]{25,35}$/.test(pdfUrl)) {
                updateStatus('Detected Google Drive ID in PDF URL field');
                return pdfUrl;
            }

            return null;
        }

        /**
         * Update debug status
         */
        function updateStatus(status) {
            debugStatus.textContent = status;
            console.log(status);
        }

        /**
         * Show error
         */
        function showError(message) {
            errorMessage.textContent = message;
            errorMessage.style.display = 'block';
            loadingOverlay.style.display = 'none';
            updateStatus('ERROR: ' + message);
        }

/**
 * Load the PDF based on source
 */
 async function loadPdf() {
    updateStatus('Starting PDF load process');
    let source;

    // Define the full URL for the proxy route
    const proxyUrl = '{{ url("/proxy-gdrive-pdf") }}';

    // Get the effective Google Drive ID (either from googleDriveId or detected from pdfUrl)
    const effectiveDriveId = detectGoogleDriveId();

    if (effectiveDriveId) {
        updateStatus('Using Google Drive ID: ' + effectiveDriveId);

        if (googleDriveToken && googleDriveToken.trim() !== '') {
            updateStatus('Using direct access with token');
            source = {
                url: `https://www.googleapis.com/drive/v3/files/${effectiveDriveId}?alt=media`,
                httpHeaders: {
                    'Authorization': `Bearer ${googleDriveToken}`
                }
            };
        } else {
            updateStatus('Using proxy endpoint');
            // Use the full URL directly
            source = {
                url: `${proxyUrl}?file_id=${effectiveDriveId}`
            };

            updateStatus('Proxy URL: ' + source.url);
        }
    } else if (pdfUrl && pdfUrl.trim() !== '') {
        updateStatus('Using direct PDF URL: ' + pdfUrl);
        source = { url: pdfUrl };
    } else {
        showError('No PDF source provided');
        return;
    }

    try {
        updateStatus('Fetching PDF document...');
        // Load the PDF document
        const loadingTask = pdfjsLib.getDocument(source);

        loadingTask.onProgress = function(progressData) {
            if (progressData.total > 0) {
                const percent = Math.round((progressData.loaded / progressData.total) * 100);
                updateStatus(`Loading PDF: ${percent}%`);
            }
        };

        pdfDoc = await loadingTask.promise;
        updateStatus('PDF loaded successfully. Pages: ' + pdfDoc.numPages);

        document.getElementById('page_count').textContent = pdfDoc.numPages;
        loadingOverlay.style.display = 'none';

        // Initial render
        renderPage(pageNum);
    } catch (error) {
        console.error('Error loading PDF:', error);
        showError('Failed to load PDF: ' + error.message);
    }
}

        /**
         * Render the page
         */
        function renderPage(num) {
            pageRendering = true;
            updateStatus('Rendering page ' + num);

            // Get the page
            pdfDoc.getPage(num).then(function(page) {
                const viewport = page.getViewport({ scale: scale });
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                // Render PDF page
                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };

                const renderTask = page.render(renderContext);

                // Wait for rendering to finish
                renderTask.promise.then(function() {
                    pageRendering = false;
                    updateStatus('Page ' + num + ' rendered successfully');

                    if (pageNumPending !== null) {
                        // New page rendering is pending
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }
                }).catch(function(error) {
                    console.error('Error rendering page:', error);
                    updateStatus('Error rendering page: ' + error.message);
                });
            });

            // Update page counter
            document.getElementById('page_num').textContent = num;
        }

        /**
         * Queue rendering if another page is in progress
         */
        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }

        /**
         * Go to previous page
         */
        function onPrevPage() {
            if (pageNum <= 1) {
                return;
            }
            pageNum--;
            queueRenderPage(pageNum);
        }

        /**
         * Go to next page
         */
        function onNextPage() {
            if (!pdfDoc || pageNum >= pdfDoc.numPages) {
                return;
            }
            pageNum++;
            queueRenderPage(pageNum);
        }

        /**
         * Zoom in
         */
        function zoomIn() {
            scale += 0.25;
            queueRenderPage(pageNum);
        }

        /**
         * Zoom out
         */
        function zoomOut() {
            if (scale <= 0.5) return;
            scale -= 0.25;
            queueRenderPage(pageNum);
        }

 /**
 * Download the PDF file
 */
async function downloadPdf() {
    updateStatus('Preparing PDF for download...');

    // Define the full URL for the proxy route
    const proxyUrl = '{{ url("/proxy-gdrive-pdf") }}';

    try {
        let downloadUrl;
        const effectiveDriveId = detectGoogleDriveId();

        if (effectiveDriveId) {
            // For Google Drive files, we need to use a different approach
            if (googleDriveToken && googleDriveToken.trim() !== '') {
                // Use direct access
                downloadUrl = `https://www.googleapis.com/drive/v3/files/${effectiveDriveId}?alt=media`;

                // Create a temporary link and trigger download
                const a = document.createElement('a');
                a.href = downloadUrl;
                a.download = `document-${effectiveDriveId}.pdf`;
                a.target = '_blank';

                // Add necessary authorization header using fetch
                fetch(downloadUrl, {
                    headers: {
                        'Authorization': `Bearer ${googleDriveToken}`
                    }
                })
                .then(response => response.blob())
                .then(blob => {
                    // Create a blob URL and trigger download
                    const blobUrl = URL.createObjectURL(blob);
                    a.href = blobUrl;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(blobUrl);
                    updateStatus('Download complete');
                })
                .catch(error => {
                    console.error('Download error:', error);
                    updateStatus('Download failed: ' + error.message);
                });
            } else {
                // Use the full URL for the proxy download
                const fullProxyUrl = `${proxyUrl}?file_id=${effectiveDriveId}&download=1`;
                window.open(fullProxyUrl, '_blank');
                updateStatus('Download initiated through proxy');
                updateStatus('Proxy download URL: ' + fullProxyUrl);
            }
        } else if (pdfUrl && pdfUrl.trim() !== '') {
            // For direct PDF URLs, just open in a new tab
            window.open(pdfUrl, '_blank');
            updateStatus('Download initiated');
        } else {
            showError('No PDF source available for download');
        }
    } catch (error) {
        console.error('Download error:', error);
        updateStatus('Download failed: ' + error.message);
    }
}

        // Button event listeners
        document.getElementById('prev').addEventListener('click', onPrevPage);
        document.getElementById('next').addEventListener('click', onNextPage);
        document.getElementById('zoom-in').addEventListener('click', zoomIn);
        document.getElementById('zoom-out').addEventListener('click', zoomOut);
        document.getElementById('download').addEventListener('click', downloadPdf);


        // Load the PDF when page loads
        document.addEventListener('DOMContentLoaded', function() {
            updateStatus('DOM loaded, initiating PDF load');
            loadPdf();
        });
    </script>

