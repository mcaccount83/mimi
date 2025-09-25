@extends('layouts.public_theme')

<style>
    body, html {
        margin: 0;
        padding: 0;
        height: 100%;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        background-color: #f8f9fa;
    }
    .pdf-container {
        max-width: 1200px;
        margin: 20px auto;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    .pdf-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 24px;
        background-color: #fff;
        border-bottom: 1px solid #e9ecef;
    }
    .pdf-header-title {
        font-size: 18px;
        font-weight: 600;
        color: #212529;
        margin: 0;
    }
    .pdf-header-info {
        color: #6c757d;
        font-size: 14px;
        margin-top: 4px;
    }
    .pdf-toolbar {
        background-color: #f8f9fa;
        padding: 10px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #e9ecef;
    }
    .pdf-toolbar-section {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .pdf-content {
        background-color: #e9ecef;
        padding: 20px;
        min-height: 70vh;
        display: flex;
        justify-content: center;
        position: relative;
    }
    #pdf-canvas {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        background-color: white;
    }
    #image-viewer {
        max-width: 100%;
        max-height: 100%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        background-color: white;
        cursor: zoom-in;
    }
    #image-viewer.zoomed {
        cursor: zoom-out;
    }
    .pdf-btn {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 8px 12px;
        font-size: 14px;
        color: #495057;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .pdf-btn:hover {
        background-color: #f1f3f5;
        border-color: #ced4da;
    }
    .pdf-btn-icon {
        width: 16px;
        height: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .pdf-btn-primary {
        background-color: #4263eb;
        color: white;
        border-color: #4263eb;
    }
    .pdf-btn-primary:hover {
        background-color: #3b5bdb;
        border-color: #3b5bdb;
        color: white;
    }
    .pdf-navigation {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    #page-input {
        width: 50px;
        text-align: center;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 8px;
        font-size: 14px;
    }
    .pdf-zoom {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    #zoom-level {
        min-width: 60px;
        text-align: center;
        font-size: 14px;
        color: #495057;
    }
    .pdf-loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
        background-color: white;
        padding: 24px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        z-index: 100;
    }
    .pdf-loading-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid #e9ecef;
        border-radius: 50%;
        border-top-color: #4263eb;
        animation: spin 1s ease-in-out infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    .pdf-error {
        padding: 24px;
        text-align: center;
        background-color: #fff5f5;
        border: 1px solid #ffc9c9;
        border-radius: 8px;
        color: #e03131;
        margin: 20px;
    }
    .pdf-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .pdf-footer {
        padding: 12px 24px;
        font-size: 12px;
        color: #adb5bd;
        text-align: center;
        border-top: 1px solid #e9ecef;
    }
    .hidden {
        display: none !important;
    }
    .image-controls {
        display: flex;
        align-items: center;
        gap: 8px;
    }
</style>

@section('content')
<div class="pdf-container">
    <div class="pdf-header">
        <div>
            <h1 class="pdf-header-title" id="pdf-title">Document Viewer</h1>
            <div class="pdf-header-info" id="pdf-info">Loading document information...</div>
        </div>
        <div class="pdf-actions">
            <button id="download-btn" class="pdf-btn">
                <span class="pdf-btn-icon">↓</span> Download
            </button>
            <button id="close-btn" class="pdf-btn">
                <span class="pdf-btn-icon">✕</span> Close
            </button>
        </div>
    </div>

    <div class="pdf-toolbar">
        <div class="pdf-toolbar-section">
            <!-- PDF Navigation (hidden for images) -->
            <div class="pdf-navigation" id="pdf-navigation">
                <button id="prev-page" class="pdf-btn" title="Previous page">
                    <span class="pdf-btn-icon">◀</span>
                </button>
                <input type="number" id="page-input" value="1" min="1">
                <span id="page-count">/ 0</span>
                <button id="next-page" class="pdf-btn" title="Next page">
                    <span class="pdf-btn-icon">▶</span>
                </button>
            </div>
        </div>

        <div class="pdf-toolbar-section">
            <div class="pdf-zoom">
                <button id="zoom-out" class="pdf-btn" title="Zoom out">−</button>
                <span id="zoom-level">100%</span>
                <button id="zoom-in" class="pdf-btn" title="Zoom in">+</button>
            </div>
            <button id="fit-width-btn" class="pdf-btn" title="Fit to width">
                <span class="pdf-btn-icon">↔</span>
            </button>
            <!-- Image-specific controls -->
            <button id="fit-screen-btn" class="pdf-btn hidden" title="Fit to screen">
                <span class="pdf-btn-icon">⛶</span>
            </button>
            <button id="actual-size-btn" class="pdf-btn hidden" title="Actual size">
                <span class="pdf-btn-icon">1:1</span>
            </button>
        </div>
    </div>

    <div class="pdf-content" id="pdf-content">
        <div id="pdf-loading" class="pdf-loading">
            <div class="pdf-loading-spinner"></div>
            <div>Loading document...</div>
        </div>
        <canvas id="pdf-canvas"></canvas>
        <img id="image-viewer" class="hidden" alt="Document Image">
    </div>

    <div class="pdf-footer" id="pdf-footer">
        Powered by PDF.js
    </div>
</div>
@endsection

@section('customscript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    // Set the PDF.js worker
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    // Get elements
    const pdfContent = document.getElementById('pdf-content');
    const pdfCanvas = document.getElementById('pdf-canvas');
    const imageViewer = document.getElementById('image-viewer');
    const ctx = pdfCanvas.getContext('2d');
    const pdfLoading = document.getElementById('pdf-loading');
    const pageInput = document.getElementById('page-input');
    const pageCount = document.getElementById('page-count');
    const prevButton = document.getElementById('prev-page');
    const nextButton = document.getElementById('next-page');
    const zoomInButton = document.getElementById('zoom-in');
    const zoomOutButton = document.getElementById('zoom-out');
    const fitWidthButton = document.getElementById('fit-width-btn');
    const fitScreenButton = document.getElementById('fit-screen-btn');
    const actualSizeButton = document.getElementById('actual-size-btn');
    const zoomLevel = document.getElementById('zoom-level');
    const pdfTitle = document.getElementById('pdf-title');
    const pdfInfo = document.getElementById('pdf-info');
    const downloadBtn = document.getElementById('download-btn');
    const closeBtn = document.getElementById('close-btn');
    const pdfNavigation = document.getElementById('pdf-navigation');
    const pdfFooter = document.getElementById('pdf-footer');

    // Variables to hold PDF document and current page
    let pdfDoc = null;
    let currentPage = 1;
    let zoomFactor = 1.0;
    let fitToWidth = false;
    let isImage = false;
    let originalImageWidth = 0;
    let originalImageHeight = 0;
    const fileId = '{{ $fileId }}';
    const proxyUrl = '{{ route("pdf-proxy") }}?id=' + fileId;

    // Function to detect file type
    async function detectFileType(url) {
        try {
            const response = await fetch(url, { method: 'HEAD' });
            const contentType = response.headers.get('content-type');

            if (contentType) {
                if (contentType.includes('image/')) {
                    return 'image';
                } else if (contentType.includes('pdf')) {
                    return 'pdf';
                }
            }

            // Fallback: try to load as PDF first, then as image
            return 'unknown';
        } catch (error) {
            console.error('Error detecting file type:', error);
            return 'unknown';
        }
    }

    // Function to setup UI for image viewing
    function setupImageViewer() {
        isImage = true;
        pdfNavigation.classList.add('hidden');
        fitScreenButton.classList.remove('hidden');
        actualSizeButton.classList.remove('hidden');
        pdfCanvas.classList.add('hidden');
        imageViewer.classList.remove('hidden');
        pdfFooter.textContent = 'Image Viewer';

        // Update title
        pdfTitle.textContent = 'Image Viewer';
        pdfInfo.textContent = 'Image document';
    }

    // Function to setup UI for PDF viewing
    function setupPdfViewer() {
        isImage = false;
        pdfNavigation.classList.remove('hidden');
        fitScreenButton.classList.add('hidden');
        actualSizeButton.classList.add('hidden');
        pdfCanvas.classList.remove('hidden');
        imageViewer.classList.add('hidden');
        pdfFooter.textContent = 'Powered by PDF.js';
    }

    // Function to load and display image
    async function loadImage() {
        try {
            pdfLoading.style.display = 'flex';
            setupImageViewer();

            imageViewer.onload = function() {
                originalImageWidth = this.naturalWidth;
                originalImageHeight = this.naturalHeight;

                // Fit to container by default
                fitImageToContainer();

                pdfLoading.style.display = 'none';

                // Update info
                pdfInfo.textContent = `${originalImageWidth} × ${originalImageHeight} pixels`;
            };

            imageViewer.onerror = function() {
                showError('Failed to load image. The file may be corrupted or inaccessible.');
            };

            imageViewer.src = proxyUrl;

        } catch (error) {
            console.error('Error loading image:', error);
            showError('Failed to load image. ' + error.message);
        }
    }

    // Function to fit image to container
    function fitImageToContainer() {
        const containerWidth = pdfContent.clientWidth - 40;
        const containerHeight = pdfContent.clientHeight - 40;

        const widthRatio = containerWidth / originalImageWidth;
        const heightRatio = containerHeight / originalImageHeight;
        const ratio = Math.min(widthRatio, heightRatio);

        zoomFactor = ratio;
        applyImageZoom();
    }

    // Function to apply zoom to image
    function applyImageZoom() {
        const newWidth = originalImageWidth * zoomFactor;
        const newHeight = originalImageHeight * zoomFactor;

        imageViewer.style.width = newWidth + 'px';
        imageViewer.style.height = newHeight + 'px';

        zoomLevel.textContent = Math.round(zoomFactor * 100) + '%';
    }

    // Function to render PDF page
    async function renderPage() {
        if (!pdfDoc) return;

        pdfLoading.style.display = 'flex';

        try {
            const page = await pdfDoc.getPage(currentPage);

            let scale = zoomFactor;
            if (fitToWidth) {
                const viewport = page.getViewport({ scale: 1 });
                const containerWidth = pdfContent.clientWidth - 40;
                scale = containerWidth / viewport.width;
                zoomFactor = scale;
                zoomLevel.textContent = Math.round(scale * 100) + '%';
            }

            const viewport = page.getViewport({ scale: scale });

            pdfCanvas.width = viewport.width;
            pdfCanvas.height = viewport.height;

            const renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };

            await page.render(renderContext).promise;
            pageInput.value = currentPage;

        } catch (error) {
            console.error('Error rendering page:', error);
            showError('Failed to render page. ' + error.message);
        } finally {
            pdfLoading.style.display = 'none';
        }
    }

    // Function to show error message
    function showError(message) {
        pdfLoading.style.display = 'none';

        const errorDiv = document.createElement('div');
        errorDiv.className = 'pdf-error';
        errorDiv.innerHTML = `
            <strong>Error:</strong> ${message}<br>
            <button id="retry-btn" class="pdf-btn pdf-btn-primary" style="margin-top: 16px;">
                Try Again
            </button>
            <a href="https://drive.google.com/file/d/${fileId}/view" target="_blank" class="pdf-btn" style="margin-top: 16px; margin-left: 8px;">
                Open in Google Drive
            </a>
        `;

        pdfContent.appendChild(errorDiv);

        document.getElementById('retry-btn').addEventListener('click', () => {
            errorDiv.remove();
            loadDocument();
        });
    }

    // Function to load PDF document
    async function loadPdf() {
        try {
            pdfLoading.style.display = 'flex';
            setupPdfViewer();

            const loadingTask = pdfjsLib.getDocument(proxyUrl);
            pdfDoc = await loadingTask.promise;

            pageCount.textContent = `/ ${pdfDoc.numPages}`;
            pageInput.max = pdfDoc.numPages;

            try {
                const metadata = await pdfDoc.getMetadata();
                if (metadata && metadata.info) {
                    const title = metadata.info.Title || 'Document';
                    pdfTitle.textContent = title;

                    let infoText = '';
                    if (metadata.info.CreationDate) {
                        try {
                            const creationDate = new Date(metadata.info.CreationDate.substring(2, 16));
                            infoText += `Created: ${creationDate.toLocaleDateString()}`;
                        } catch (e) {
                            infoText += 'Document information';
                        }
                    }

                    infoText += infoText ? ' • ' : '';
                    infoText += `${pdfDoc.numPages} page${pdfDoc.numPages != 1 ? 's' : ''}`;

                    pdfInfo.textContent = infoText;
                } else {
                    pdfTitle.textContent = 'Document';
                    pdfInfo.textContent = `${pdfDoc.numPages} page${pdfDoc.numPages != 1 ? 's' : ''}`;
                }
            } catch (err) {
                console.log('Could not get metadata:', err);
                pdfTitle.textContent = 'Document';
                pdfInfo.textContent = `${pdfDoc.numPages} page${pdfDoc.numPages != 1 ? 's' : ''}`;
            }

            currentPage = 1;
            await renderPage();

        } catch (error) {
            console.error('Error loading PDF:', error);
            // If PDF loading fails, try loading as image
            await loadImage();
        }
    }

    // Main function to load document
    async function loadDocument() {
        const fileType = await detectFileType(proxyUrl);

        if (fileType == 'image') {
            await loadImage();
        } else if (fileType == 'pdf') {
            await loadPdf();
        } else {
            // Try PDF first, then image
            await loadPdf();
        }
    }

    // Event listeners for PDF navigation
    prevButton.addEventListener('click', async () => {
        if (currentPage > 1) {
            currentPage--;
            await renderPage();
        }
    });

    nextButton.addEventListener('click', async () => {
        if (pdfDoc && currentPage < pdfDoc.numPages) {
            currentPage++;
            await renderPage();
        }
    });

    pageInput.addEventListener('change', async () => {
        const pageNum = parseInt(pageInput.value);
        if (pdfDoc && pageNum >= 1 && pageNum <= pdfDoc.numPages) {
            currentPage = pageNum;
            await renderPage();
        } else {
            pageInput.value = currentPage;
        }
    });

    // Zoom controls
    zoomInButton.addEventListener('click', async () => {
        fitToWidth = false;
        zoomFactor *= 1.2;

        if (isImage) {
            applyImageZoom();
        } else {
            zoomLevel.textContent = Math.round(zoomFactor * 100) + '%';
            await renderPage();
        }
    });

    zoomOutButton.addEventListener('click', async () => {
        fitToWidth = false;
        zoomFactor /= 1.2;
        if (zoomFactor < 0.1) zoomFactor = 0.1;

        if (isImage) {
            applyImageZoom();
        } else {
            zoomLevel.textContent = Math.round(zoomFactor * 100) + '%';
            await renderPage();
        }
    });

    fitWidthButton.addEventListener('click', async () => {
        if (isImage) {
            // For images, fit to container width
            const containerWidth = pdfContent.clientWidth - 40;
            zoomFactor = containerWidth / originalImageWidth;
            applyImageZoom();
        } else {
            fitToWidth = true;
            await renderPage();
        }
    });

    // Image-specific controls
    fitScreenButton.addEventListener('click', () => {
        fitImageToContainer();
    });

    actualSizeButton.addEventListener('click', () => {
        zoomFactor = 1.0;
        applyImageZoom();
    });

    // Image click to zoom
    imageViewer.addEventListener('click', (e) => {
        if (imageViewer.classList.contains('zoomed')) {
            // Zoom out
            fitImageToContainer();
            imageViewer.classList.remove('zoomed');
        } else {
            // Zoom in
            zoomFactor = 1.0;
            applyImageZoom();
            imageViewer.classList.add('zoomed');
        }
    });

    // Download button
    downloadBtn.addEventListener('click', () => {
        if (fileId) {
            window.open(`https://drive.google.com/uc?export=download&id=${fileId}`, '_blank');
        }
    });

    // Close button
    closeBtn.addEventListener('click', () => {
        window.close();
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', async (e) => {
        if (!isImage) {
            // PDF navigation
            if (e.key == 'ArrowLeft' || e.key == 'PageUp') {
                if (currentPage > 1) {
                    currentPage--;
                    await renderPage();
                }
                e.preventDefault();
            } else if (e.key == 'ArrowRight' || e.key == 'PageDown') {
                if (pdfDoc && currentPage < pdfDoc.numPages) {
                    currentPage++;
                    await renderPage();
                }
                e.preventDefault();
            } else if (e.key == 'Home') {
                currentPage = 1;
                await renderPage();
                e.preventDefault();
            } else if (e.key == 'End') {
                if (pdfDoc) {
                    currentPage = pdfDoc.numPages;
                    await renderPage();
                }
                e.preventDefault();
            }
        }

        // Zoom controls (work for both PDF and images)
        if (e.key == '+' || e.key == '=') {
            fitToWidth = false;
            zoomFactor *= 1.1;

            if (isImage) {
                applyImageZoom();
            } else {
                zoomLevel.textContent = Math.round(zoomFactor * 100) + '%';
                await renderPage();
            }
            e.preventDefault();
        } else if (e.key == '-') {
            fitToWidth = false;
            zoomFactor /= 1.1;
            if (zoomFactor < 0.1) zoomFactor = 0.1;

            if (isImage) {
                applyImageZoom();
            } else {
                zoomLevel.textContent = Math.round(zoomFactor * 100) + '%';
                await renderPage();
            }
            e.preventDefault();
        }
    });

    // Handle window resize
    window.addEventListener('resize', async () => {
        if (isImage) {
            // Maintain current zoom level for images
            applyImageZoom();
        } else if (fitToWidth) {
            await renderPage();
        }
    });

    // Initialize the viewer
    loadDocument();
</script>
@endsection
