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
  // Set the PDF.js worker
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

// Get elements
const canvasContainer = document.getElementById('canvas-container');
const pdfCanvas = document.getElementById('pdf-canvas');
const ctx = pdfCanvas.getContext('2d');
const loadingIndicator = document.getElementById('loading-indicator');
const pageNumber = document.getElementById('page-number');
const pageCount = document.getElementById('page-count');
const prevButton = document.getElementById('prev-page');
const nextButton = document.getElementById('next-page');
const documentTitle = document.getElementById('document-title');

// Variables to hold PDF document and current page
let pdfDoc = null;
let currentPage = 1;
let zoomLevel = 1.0;
const fileUrl = '{{ $filePath }}';
const googleDriveId = fileUrl.includes('id=') ? fileUrl.split('id=')[1].split('&')[0] : null;

// Function to render the current page
async function renderPage() {
    if (!pdfDoc) return;

    loadingIndicator.style.display = 'block';

    // Get the page
    const page = await pdfDoc.getPage(currentPage);

    // Calculate scale based on container width and zoom level
    const viewport = page.getViewport({ scale: zoomLevel });

    // Set canvas dimensions to match the viewport
    pdfCanvas.width = viewport.width;
    pdfCanvas.height = viewport.height;

    // Render the PDF page
    const renderContext = {
        canvasContext: ctx,
        viewport: viewport
    };

    await page.render(renderContext).promise;

    // Update page info and hide loading indicator
    pageNumber.value = currentPage;
    loadingIndicator.style.display = 'none';
}

// Function to load the PDF document
async function loadPdf() {
    try {
        loadingIndicator.style.display = 'block';

        // For Google Drive files, we might need to handle authentication
        const loadingTask = pdfjsLib.getDocument(fileUrl);

        // Handle password-protected PDFs
        loadingTask.onPassword = function(updatePassword, reason) {
            // You can implement password handling if needed
            console.log('Password required');
            updatePassword('');
        };

        // Load the PDF document
        pdfDoc = await loadingTask.promise;

        // Set total page count
        pageCount.textContent = `/ ${pdfDoc.numPages}`;

        // Set max value for page input
        pageNumber.max = pdfDoc.numPages;

        // Try to get document title
        if (googleDriveId) {
            documentTitle.textContent = 'Google Drive Document';
        } else {
            documentTitle.textContent = 'Document';
        }

        // Render the first page
        currentPage = 1;
        await renderPage();

    } catch (error) {
        console.error('Error loading PDF:', error);

        // If loading fails, it might be due to CORS or authentication
        if (googleDriveId) {
            loadingIndicator.innerHTML = `
                Failed to load PDF directly. This might be due to Google Drive restrictions.<br>
                <a href="https://drive.google.com/file/d/${googleDriveId}/view" target="_blank" class="btn btn-primary mt-3">
                    Open in Google Drive
                </a>
            `;
        } else {
            loadingIndicator.textContent = 'Failed to load PDF. ' + error.message;
        }
    }
}

// Event listeners for navigation and zoom controls
// [Rest of your event listeners from previous implementation]

// Initialize the viewer
loadPdf();
</script>
@endsection
