<!DOCTYPE html>
<html>
<head>
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
            background-color: #fff;
            padding: 10px 15px;
            border-radius: 4px 4px 0 0;
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
            border: none;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="toolbar">
            <h3>Document Viewer</h3>
            <div class="toolbar-buttons">
                <button id="downloadBtn">Download</button>
                {{-- <button id="printBtn">Print</button> --}}
                <button id="closeBtn">Close</button>
            </div>
        </div>
        <div class="viewer-container">
            <iframe id="pdfFrame" src=""></iframe>
        </div>
    </div>

    <script>
        // Get file ID from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const fileId = urlParams.get('id');

        // Set iframe source
        if (fileId) {
            // For preview
            const previewUrl = 'https://drive.google.com/file/d/' + fileId + '/preview';
            document.getElementById('pdfFrame').src = previewUrl;

            // For download button
            document.getElementById('downloadBtn').addEventListener('click', function() {
                const downloadUrl = 'https://drive.google.com/uc?export=download&id=' + fileId;
                window.open(downloadUrl, '_blank');
            });

            // For print button
            document.getElementById('printBtn').addEventListener('click', function() {
                // Option 1: Print the iframe content
                // const iframe = document.getElementById('pdfFrame');
                // iframe.contentWindow.focus();
                // iframe.contentWindow.print();

                // Option 2 (alternative): Open in new window for printing
                // const printUrl = 'https://drive.google.com/file/d/' + fileId + '/view';
                // const printWindow = window.open(printUrl, '_blank');
                // printWindow.addEventListener('load', function() {
                //     printWindow.print();
                // });
            });

            // For close button
            document.getElementById('closeBtn').addEventListener('click', function() {
                window.close();
            });
        }
    </script>
</body>
</html>
